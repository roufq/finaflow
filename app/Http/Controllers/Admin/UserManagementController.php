<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::pluck('name');
        $adminCount = User::role('admin')->count();

        return view('admin.users.index', compact('users', 'roles', 'adminCount'));
    }

    public function show(User $user): View
    {
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $plans = $this->saasPlans();
        $activePlan = $this->determinePlanFromPermissions($userPermissions, $plans);

        return view('admin.users.show', [
            'user' => $user,
            'plans' => $plans,
            'activePlan' => $activePlan,
            'userPermissions' => $userPermissions,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $newRole = $request->string('role')->toString();

        $currentAdminCount = User::role('admin')->count();
        $isCurrentlyAdmin = $user->hasRole('admin');

        if ($newRole === 'admin' && ! $isCurrentlyAdmin && $currentAdminCount >= 2) {
            return back()->with('error', 'Maksimal 2 admin diperbolehkan.');
        }

        if ($newRole !== 'admin' && $isCurrentlyAdmin && $currentAdminCount <= 1) {
            return back()->with('error', 'Harus ada minimal satu admin aktif.');
        }

        $user->syncRoles([$newRole]);
        $user->save();

        return back()->with('status', 'Akses pengguna diperbarui.');
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $plans = $this->saasPlans();
        $planKeys = array_keys($plans);
        $request->validate([
            'plan' => 'required|string|in:'.implode(',', $planKeys),
        ]);

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Hak akses admin tidak dapat diubah di sini.');
        }

        $selectedPlan = $request->string('plan')->toString();
        $syncedPermissions = $plans[$selectedPlan]['permissions'];

        $user->syncRoles(['user']);
        $user->syncPermissions($syncedPermissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', 'Hak akses pengguna diperbarui.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'deactivation_message' => 'nullable|string|max:500',
        ]);

        $currentAdminCount = User::role('admin')->count();
        $isCurrentlyAdmin = $user->hasRole('admin');
        $isActivating = $request->boolean('is_active');

        if (! $isActivating && $isCurrentlyAdmin && $currentAdminCount <= 1) {
            return back()->with('error', 'Harus ada minimal satu admin aktif.');
        }

        $user->is_active = $isActivating;
        $user->deactivation_message = $isActivating ? null : $request->string('deactivation_message')->toString();
        $user->save();

        return back()->with('status', 'Status pengguna diperbarui.');
    }

    private function saasPlans(): array
    {
        $basicPermissions = [
            'access dashboard',
            'manage accounts',
            'manage transactions',
            'manage budgets',
            'manage goals',
            'view reports',
        ];

        return [
            'basic' => [
                'label' => 'Basic',
                'description' => 'Fitur dasar untuk kebutuhan utama.',
                'features' => [
                    'Dashboard (summary cards only)',
                    'Accounts: view balances, connect up to 2 institutions',
                    'Transactions: import/upload, basic search/filter',
                    'Budgets: create up to 3 budgets with monthly limits',
                    'Goals: create up to 2 goals with progress tracking',
                    'Reports: export to CSV',
                    'Profile & Security: password/update profile, 2FA basics',
                ],
                'permissions' => $basicPermissions,
            ],
            'standard' => [
                'label' => 'Standard',
                'description' => 'Semua Basic + otomatisasi ringan, analitik, dan langganan.',
                'features' => [
                    'Everything in Basic, plus:',
                    'Accounts: unlimited connections, auto-sync',
                    'Transactions: rules/categorization, duplicate detection',
                    'Budgets: unlimited budgets, alerts/notifications',
                    'Goals: unlimited goals, shared goals with 1 collaborator',
                    'Subscriptions: detection, pause/cancel reminders',
                    'Analytics: cashflow trends, spending by category',
                    'Rewards & Loyalty: track cards/programs, cashback reminders',
                    'Privacy: data export, deletion requests, IP/device history',
                ],
                'permissions' => array_values(array_unique(array_merge($basicPermissions, [
                    'manage family',
                ]))),
            ],
            'premium' => [
                'label' => 'Premium',
                'description' => 'Semua Standard + automations, investasi, dan coaching.',
                'features' => [
                    'Everything in Standard, plus:',
                    'AI Insights: anomalies, recommendations, predictions',
                    'Automation: custom workflows, scheduled reports',
                    'Investments & Net Worth: portfolio tracking, asset allocation',
                    'Taxes: document vault and annual summary export (PDF)',
                    'Family Finance: shared expenses, family goals, member roles',
                    'Coaching: personalized plans, task/journal, ongoing check-ins',
                    'Priority Support: chat/email priority, dedicated success manager',
                ],
                'permissions' => array_values(array_unique(array_merge($basicPermissions, [
                    'manage automations',
                    'manage family',
                    'manage behavioral',
                ]))),
            ],
        ];
    }

    private function determinePlanFromPermissions(array $userPermissions, array $plans): string
    {
        foreach (array_reverse(array_keys($plans)) as $planKey) {
            $expectedPermissions = $plans[$planKey]['permissions'];

            if (empty(array_diff($expectedPermissions, $userPermissions))) {
                return $planKey;
            }
        }

        return 'custom';
    }
}
