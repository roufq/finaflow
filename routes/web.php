<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AIInsightsController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Api\TransactionApiController;
use App\Http\Controllers\ApiIntegrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\BankIntegrationController;
use App\Http\Controllers\BehavioralController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FinancialCoachingController;
use App\Http\Controllers\IntegrationToolController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TaxDocumentController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Two Factor Auth
Route::middleware('auth')->group(function () {
    Route::get('/twofactor/setup', [TwoFactorController::class, 'showSetup'])->name('twofactor.setup');
    Route::post('/twofactor/enable', [TwoFactorController::class, 'enable'])->name('twofactor.enable');
    Route::post('/twofactor/disable', [TwoFactorController::class, 'disable'])->name('twofactor.disable');
    Route::post('/twofactor/backup/regenerate', [TwoFactorController::class, 'regenerateBackupCodes'])->name('twofactor.backup.regenerate');
});

Route::get('/twofactor/challenge', [TwoFactorController::class, 'showChallenge'])->name('twofactor.challenge');
Route::post('/twofactor/challenge', [TwoFactorController::class, 'verifyChallenge'])->name('twofactor.challenge.verify');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/security', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::resource('settings', \App\Http\Controllers\SettingController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('transactions', \App\Http\Controllers\TransactionController::class);
    Route::post('transactions/scan-receipt', [\App\Http\Controllers\TransactionController::class, 'scanReceipt'])->name('transactions.scanReceipt');
    Route::resource('accounts', \App\Http\Controllers\AccountController::class);
    Route::resource('transfers', \App\Http\Controllers\TransferController::class);
    Route::resource('goals', \App\Http\Controllers\GoalController::class);
    Route::post('goals/{goal}/update-progress', [\App\Http\Controllers\GoalController::class, 'updateProgress'])->name('goals.updateProgress');
    Route::resource('budgets', \App\Http\Controllers\BudgetController::class);
    Route::post('budgets/{budget}/update-spent', [\App\Http\Controllers\BudgetController::class, 'updateSpent'])->name('budgets.updateSpent');
    Route::resource('debts', \App\Http\Controllers\DebtController::class);
    Route::post('debts/{debt}/add-payment', [\App\Http\Controllers\DebtController::class, 'addPayment'])->name('debts.addPayment');
    Route::resource('tags', \App\Http\Controllers\TagController::class);
    Route::resource('investments', \App\Http\Controllers\InvestmentController::class);
    Route::resource('assets', \App\Http\Controllers\AssetController::class);
    Route::get('/net-worth', [\App\Http\Controllers\NetWorthController::class, 'index'])->name('net-worth.index');
    Route::resource('tax-documents', TaxDocumentController::class);

    // Behavioral Finance Routes
    Route::get('/behavioral', [BehavioralController::class, 'index'])->name('behavioral.index');
    Route::get('/behavioral/triggers', [BehavioralController::class, 'triggers'])->name('behavioral.triggers');
    Route::get('/behavioral/triggers/create', [BehavioralController::class, 'createTrigger'])->name('behavioral.triggers.create');
    Route::post('/behavioral/triggers', [BehavioralController::class, 'storeTrigger'])->name('behavioral.triggers.store');
    Route::get('/behavioral/habits', [BehavioralController::class, 'habits'])->name('behavioral.habits');
    Route::get('/behavioral/habits/create', [BehavioralController::class, 'createHabit'])->name('behavioral.habits.create');
    Route::post('/behavioral/habits', [BehavioralController::class, 'storeHabit'])->name('behavioral.habits.store');
    Route::post('/behavioral/habits/{habit}/achieved', [BehavioralController::class, 'markHabitAchieved'])->name('behavioral.habits.achieved');
    Route::get('/behavioral/personality', [BehavioralController::class, 'personality'])->name('behavioral.personality');
    Route::get('/behavioral/personality/quiz', [BehavioralController::class, 'takePersonalityQuiz'])->name('behavioral.personality.quiz');
    Route::post('/behavioral/personality/quiz', [BehavioralController::class, 'storePersonalityQuiz'])->name('behavioral.personality.quiz.store');
    Route::get('/behavioral/gamification', [BehavioralController::class, 'gamification'])->name('behavioral.gamification');

    // Subscription Management Routes
    Route::resource('subscriptions', SubscriptionController::class);
    Route::get('/subscriptions/analytics', [SubscriptionController::class, 'analytics'])->name('subscriptions.analytics');
    Route::post('/subscriptions/{subscription}/pause', [SubscriptionController::class, 'pause'])->name('subscriptions.pause');
    Route::post('/subscriptions/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/detect', [SubscriptionController::class, 'detectFromTransactions'])->name('subscriptions.detect');

    // Reward & Loyalty Routes
    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::get('/rewards/create', [RewardController::class, 'createReward'])->name('rewards.create');
    Route::post('/rewards', [RewardController::class, 'storeReward'])->name('rewards.store');
    Route::get('/rewards/create-loyalty', [RewardController::class, 'createLoyaltyProgram'])->name('rewards.create-loyalty');
    Route::post('/rewards/loyalty', [RewardController::class, 'storeLoyaltyProgram'])->name('rewards.store-loyalty');
    Route::get('/rewards/{reward}', [RewardController::class, 'showReward'])->name('rewards.show-reward');
    Route::get('/rewards/loyalty/{loyaltyProgram}', [RewardController::class, 'showLoyaltyProgram'])->name('rewards.show-loyalty');
    Route::get('/rewards/{reward}/edit', [RewardController::class, 'editReward'])->name('rewards.edit-reward');
    Route::put('/rewards/{reward}', [RewardController::class, 'updateReward'])->name('rewards.update-reward');
    Route::get('/rewards/loyalty/{loyaltyProgram}/edit', [RewardController::class, 'editLoyaltyProgram'])->name('rewards.edit-loyalty');
    Route::put('/rewards/loyalty/{loyaltyProgram}', [RewardController::class, 'updateLoyaltyProgram'])->name('rewards.update-loyalty');
    Route::post('/rewards/{reward}/redeem', [RewardController::class, 'redeemReward'])->name('rewards.redeem');
    Route::post('/rewards/loyalty/{loyaltyProgram}/redeem', [RewardController::class, 'redeemLoyaltyPoints'])->name('rewards.redeem-loyalty');
    Route::get('/rewards/optimize', [RewardController::class, 'optimizeRewards'])->name('rewards.optimize');
    Route::delete('/rewards/{reward}', [RewardController::class, 'destroyReward'])->name('rewards.destroy-reward');
    Route::delete('/rewards/loyalty/{loyaltyProgram}', [RewardController::class, 'destroyLoyaltyProgram'])->name('rewards.destroy-loyalty');

    // Automation & Integration Routes
    Route::resource('automations', AutomationController::class);
    Route::post('/automations/{automation}/run', [AutomationController::class, 'run'])->name('automations.run');
    Route::post('/automations/{automation}/toggle', [AutomationController::class, 'toggle'])->name('automations.toggle');

    Route::resource('bank-integrations', BankIntegrationController::class);
    Route::post('/bank-integrations/{bankIntegration}/sync', [BankIntegrationController::class, 'sync'])->name('bank-integrations.sync');
    Route::post('/bank-integrations/{bankIntegration}/upload-csv', [BankIntegrationController::class, 'uploadCsv'])->name('bank-integrations.upload-csv');
    Route::get('/bank-integrations/{bankIntegration}/status', [BankIntegrationController::class, 'status'])->name('bank-integrations.status');

    Route::resource('api-integrations', ApiIntegrationController::class);
    Route::post('/api-integrations/{apiIntegration}/sync', [ApiIntegrationController::class, 'sync'])->name('api-integrations.sync');
    Route::get('/api-integrations/{apiIntegration}/status', [ApiIntegrationController::class, 'status'])->name('api-integrations.status');
    Route::get('/api-integrations/data/{provider}', [ApiIntegrationController::class, 'getData'])->name('api-integrations.get-data');

    // Automation tools (voice entry, email parsing, reminders)
    Route::get('/integrations/voice-entry', [IntegrationToolController::class, 'voiceEntry'])->name('integrations.voice-entry');
    Route::post('/integrations/voice-entry', [IntegrationToolController::class, 'storeVoiceEntry'])->name('integrations.voice-entry.store');
    Route::get('/integrations/email-parser', [IntegrationToolController::class, 'emailParser'])->name('integrations.email-parser');
    Route::post('/integrations/email-parser', [IntegrationToolController::class, 'parseEmail'])->name('integrations.email-parser.store');
    Route::get('/integrations/reminders', [IntegrationToolController::class, 'reminders'])->name('integrations.reminders');
    Route::post('/integrations/reminders', [IntegrationToolController::class, 'storeReminder'])->name('integrations.reminders.store');

    // Family Finance Routes
    Route::get('/family', [FamilyController::class, 'index'])->name('family.index');
    Route::get('/family/members', [FamilyController::class, 'members'])->name('family.members');
    Route::get('/family/members/create', [FamilyController::class, 'createMember'])->name('family.members.create');
    Route::post('/family/members', [FamilyController::class, 'storeMember'])->name('family.members.store');
    Route::get('/family/members/{member}/edit', [FamilyController::class, 'editMember'])->name('family.members.edit');
    Route::put('/family/members/{member}', [FamilyController::class, 'updateMember'])->name('family.members.update');
    Route::get('/family/shared-expenses', [FamilyController::class, 'sharedExpenses'])->name('family.shared-expenses');
    Route::get('/family/shared-expenses/create', [FamilyController::class, 'createSharedExpense'])->name('family.shared-expenses.create');
    Route::post('/family/shared-expenses', [FamilyController::class, 'storeSharedExpense'])->name('family.shared-expenses.store');
    Route::get('/family/shared-expenses/{expense}/edit', [FamilyController::class, 'editSharedExpense'])->name('family.shared-expenses.edit');
    Route::put('/family/shared-expenses/{expense}', [FamilyController::class, 'updateSharedExpense'])->name('family.shared-expenses.update');
    Route::patch('/family/shared-expenses', [FamilyController::class, 'settleSharedExpense'])->name('family.shared-expenses.settle');
    Route::patch('/family/shared-expenses/{expense}/settle', [FamilyController::class, 'settleSharedExpense'])->name('family.shared-expenses.settle.direct');
    Route::get('/family/goals', [FamilyController::class, 'familyGoals'])->name('family.goals');
    Route::get('/family/goals/create', [FamilyController::class, 'createFamilyGoal'])->name('family.goals.create');
    Route::post('/family/goals', [FamilyController::class, 'storeFamilyGoal'])->name('family.goals.store');
    Route::get('/family/goals/{goal}/edit', [FamilyController::class, 'editFamilyGoal'])->name('family.goals.edit');
    Route::put('/family/goals/{goal}', [FamilyController::class, 'updateFamilyGoal'])->name('family.goals.update');
    Route::delete('/family/goals/{goal}', [FamilyController::class, 'destroyFamilyGoal'])->name('family.goals.destroy');
    Route::get('/family/gift-events', [FamilyController::class, 'giftEvents'])->name('family.gift-events');
    Route::get('/family/gift-events/create', [FamilyController::class, 'createGiftEvent'])->name('family.gift-events.create');
    Route::post('/family/gift-events', [FamilyController::class, 'storeGiftEvent'])->name('family.gift-events.store');
    Route::get('/family/gift-events/{event}/edit', [FamilyController::class, 'editGiftEvent'])->name('family.gift-events.edit');
    Route::put('/family/gift-events/{event}', [FamilyController::class, 'updateGiftEvent'])->name('family.gift-events.update');
    Route::delete('/family/gift-events/{event}', [FamilyController::class, 'destroyGiftEvent'])->name('family.gift-events.destroy');
    Route::post('/family/gift-events/{event}/gifts', [FamilyController::class, 'storeGift'])->name('family.gift-events.gifts.store');

    // Privacy Settings Routes
    Route::get('/privacy', [PrivacyController::class, 'index'])->name('privacy.settings');
    Route::put('/privacy', [PrivacyController::class, 'update'])->name('privacy.update');
    Route::post('/privacy/request-deletion', [PrivacyController::class, 'requestDeletion'])->name('privacy.request-deletion');
    Route::post('/privacy/cancel-deletion', [PrivacyController::class, 'cancelDeletion'])->name('privacy.cancel-deletion');
    Route::get('/privacy/export-data', [PrivacyController::class, 'exportData'])->name('privacy.export-data');

    // AI Insights Routes
    Route::get('/insights', [AIInsightsController::class, 'index'])->name('insights.index');
    Route::get('/insights/recommendations', [AIInsightsController::class, 'recommendations'])->name('insights.recommendations');
    Route::post('/insights/recommendations/{recommendation}/read', [AIInsightsController::class, 'markRecommendationRead'])->name('insights.recommendations.read');
    Route::get('/insights/anomalies', [AIInsightsController::class, 'anomalies'])->name('insights.anomalies');
    Route::post('/insights/anomalies/{anomaly}/resolve', [AIInsightsController::class, 'resolveAnomaly'])->name('insights.anomalies.resolve');
    Route::get('/insights/predictions', [AIInsightsController::class, 'predictions'])->name('insights.predictions');
    Route::match(['get', 'post'], '/insights/generate', [AIInsightsController::class, 'generateInsights'])->name('insights.generate');

    // Reporting Routes
    Route::get('/reporting', [ReportingController::class, 'dashboard'])->name('reporting.dashboard');
    Route::get('/reporting/builder/{report?}', [ReportingController::class, 'builder'])->name('reporting.builder');
    Route::post('/reporting/reports', [ReportingController::class, 'storeReport'])->name('reporting.reports.store');
    Route::post('/reporting/widgets', [ReportingController::class, 'storeWidget'])->name('reporting.widgets.store');
    Route::put('/reporting/widgets/{widget}', [ReportingController::class, 'updateWidget'])->name('reporting.widgets.update');
    Route::delete('/reporting/widgets/{widget}', [ReportingController::class, 'destroyWidget'])->name('reporting.widgets.destroy');
    Route::post('/reporting/widgets/positions', [ReportingController::class, 'updateWidgetPositions'])->name('reporting.widgets.positions');
    Route::post('/reporting/reports/{report}/export', [ReportingController::class, 'exportReport'])->name('reporting.reports.export');

    // API v1 (authenticated)
    Route::prefix('api/v1')->name('api.v1.')->middleware('throttle:60,1')->group(function () {
        Route::get('/transactions', [TransactionApiController::class, 'index'])->name('transactions.index');
    });

    // Financial Education Routes
    Route::get('/education', [EducationController::class, 'index'])->name('education.index');
    Route::get('/education/modules/{module}', [EducationController::class, 'showModule'])->name('education.module');
    Route::post('/education/modules/{module}/progress', [EducationController::class, 'updateProgress'])->name('education.module.progress');
    Route::get('/education/news', [EducationController::class, 'news'])->name('education.news');
    Route::post('/education/community-stories', [EducationController::class, 'storeCommunityStory'])->name('education.community-stories.store');
    Route::get('/coaching', [FinancialCoachingController::class, 'index'])->name('coaching.index');
    Route::post('/coaching/action-plan', [FinancialCoachingController::class, 'storeActionPlan'])->name('coaching.action-plan.store');
    Route::post('/coaching/tasks', [FinancialCoachingController::class, 'storeTask'])->name('coaching.tasks.store');
    Route::patch('/coaching/tasks/{task}', [FinancialCoachingController::class, 'updateTask'])->name('coaching.tasks.update');
    Route::post('/coaching/micro-learning/{lesson}', [FinancialCoachingController::class, 'updateLessonProgress'])->name('coaching.lessons.update');
    Route::post('/coaching/journal', [FinancialCoachingController::class, 'storeJournalEntry'])->name('coaching.journal.store');
    Route::get('/coaching/export', [FinancialCoachingController::class, 'exportSummary'])->name('coaching.export');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/education', [\App\Http\Controllers\Admin\EducationAdminController::class, 'index'])->name('education.index');
        Route::get('/education/create', [\App\Http\Controllers\Admin\EducationAdminController::class, 'create'])->name('education.create');
        Route::get('/education/{module}', [\App\Http\Controllers\Admin\EducationAdminController::class, 'show'])->name('education.show');
        Route::get('/education/{module}/edit', [\App\Http\Controllers\Admin\EducationAdminController::class, 'edit'])->name('education.edit');
        Route::post('/education', [\App\Http\Controllers\Admin\EducationAdminController::class, 'store'])->name('education.store');
        Route::put('/education/{module}', [\App\Http\Controllers\Admin\EducationAdminController::class, 'update'])->name('education.update');
        Route::delete('/education/{module}', [\App\Http\Controllers\Admin\EducationAdminController::class, 'destroy'])->name('education.destroy');

        Route::get('/news', [\App\Http\Controllers\Admin\FinancialNewsAdminController::class, 'index'])->name('news.index');
        Route::get('/news/create', [\App\Http\Controllers\Admin\FinancialNewsAdminController::class, 'create'])->name('news.create');
        Route::get('/news/{news}', [\App\Http\Controllers\Admin\FinancialNewsAdminController::class, 'show'])->name('news.show');
        Route::get('/news/{news}/edit', [\App\Http\Controllers\Admin\FinancialNewsAdminController::class, 'edit'])->name('news.edit');
        Route::post('/news', [\App\Http\Controllers\Admin\FinancialNewsAdminController::class, 'store'])->name('news.store');
        Route::put('/news/{news}', [\App\Http\Controllers\Admin\FinancialNewsAdminController::class, 'update'])->name('news.update');
        Route::delete('/news/{news}', [\App\Http\Controllers\Admin\FinancialNewsAdminController::class, 'destroy'])->name('news.destroy');

        Route::get('/education-categories', [\App\Http\Controllers\Admin\EducationCategoryAdminController::class, 'index'])->name('education-categories.index');
        Route::get('/education-categories/create', [\App\Http\Controllers\Admin\EducationCategoryAdminController::class, 'create'])->name('education-categories.create');
        Route::post('/education-categories', [\App\Http\Controllers\Admin\EducationCategoryAdminController::class, 'store'])->name('education-categories.store');
        Route::get('/education-categories/{educationCategory}/edit', [\App\Http\Controllers\Admin\EducationCategoryAdminController::class, 'edit'])->name('education-categories.edit');
        Route::put('/education-categories/{educationCategory}', [\App\Http\Controllers\Admin\EducationCategoryAdminController::class, 'update'])->name('education-categories.update');
        Route::delete('/education-categories/{educationCategory}', [\App\Http\Controllers\Admin\EducationCategoryAdminController::class, 'destroy'])->name('education-categories.destroy');
    });
});
