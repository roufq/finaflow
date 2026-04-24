@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">User Management</h1>
            <p class="text-sm font-medium text-slate-500">Administrate platform access, roles, and account statuses</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Dashboard
            </a>
        </div>
    </div>

    @if(session('status'))
    <div class="relative overflow-hidden rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-sm font-bold text-emerald-900">{{ session('status') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="relative overflow-hidden rounded-2xl bg-rose-50 border border-rose-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-rose-500 shadow-sm ring-1 ring-rose-100">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <p class="text-sm font-bold text-rose-900">{{ session('error') }}</p>
    </div>
    @endif

    <!-- User Management Table -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <div class="flex flex-col gap-1">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Platform Directory</h2>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-[9px] font-black text-amber-600 ring-1 ring-amber-100 uppercase tracking-tighter">
                        Admin Quota: {{ $adminCount }}/2
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Security Monitored</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">User Identity</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Access Tier</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Governance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($users as $user)
                    @php
                        $isAdmin = $user->hasRole('admin');
                        $adminLimitReached = $adminCount >= 2 && ! $isAdmin;
                    @endphp
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white font-black text-xs shadow-soft">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 leading-none">{{ $user->name }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 mt-1">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-tight text-slate-600 ring-1 ring-slate-200">
                                {{ $user->roles->pluck('name')->implode(', ') ?: 'Standard User' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black text-emerald-600 ring-1 ring-emerald-100 uppercase">
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-400 ring-1 ring-slate-200 uppercase">
                                Suspended
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="relative">
                                        <select name="role" class="appearance-none rounded-xl border-none bg-slate-50 px-4 py-1.5 pr-8 text-[11px] font-bold text-slate-600 ring-1 ring-slate-200 focus:ring-2 focus:ring-primary-500/20">
                                            @foreach($roles as $role)
                                                <option value="{{ $role }}"
                                                    {{ $user->hasRole($role) ? 'selected' : '' }}
                                                    @if($role === 'admin' && $adminLimitReached) disabled @endif
                                                >{{ ucfirst($role) }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[8px] text-slate-400 pointer-events-none"></i>
                                    </div>
                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white shadow-premium hover:bg-slate-800 transition-all">
                                        <i class="fas fa-save text-[10px]"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.users.show', $user) }}" class="flex h-8 h-8 items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-primary-600 hover:border-primary-200 shadow-sm transition-all px-3 text-xs font-bold whitespace-nowrap">
                                    Manage Access
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-50 bg-slate-50/10 space-y-2">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-primary-400 mt-0.5 text-xs"></i>
                <p class="text-[11px] font-medium text-slate-500 leading-relaxed">
                    Maximum 2 system administrators allowed. Promotion is restricted once the quota is reached. Deactivating a user will immediately terminate their active session and redirect them to the authentication portal.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
