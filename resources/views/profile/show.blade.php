@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('profile.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">{{ __('profile.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                {{ __('common.back') }}
            </a>
        </div>
    </div>

    @if (session('profileUpdated'))
    <div class="relative overflow-hidden rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-sm font-bold text-emerald-900">{{ session('profileUpdated') }}</p>
    </div>
    @endif

    @if (session('passwordUpdated'))
    <div class="relative overflow-hidden rounded-2xl bg-blue-50 border border-blue-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-blue-500 shadow-sm ring-1 ring-blue-100">
            <i class="fas fa-shield-alt"></i>
        </div>
        <p class="text-sm font-bold text-blue-900">{{ session('passwordUpdated') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Main Form Area -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Basic Information Card -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-500 text-white shadow-soft">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('profile.basic_information') }}</h3>
                    </div>
                </div>
                <div class="p-8">
                    @php
                        $avatarUrl = $user->avatar_path ? asset('storage/'.$user->avatar_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF';
                    @endphp
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col md:flex-row gap-8 items-start">
                            <div class="relative group">
                                <img src="{{ $avatarUrl }}" alt="Profile avatar" class="h-24 w-24 rounded-3xl object-cover ring-4 ring-slate-50 shadow-soft transition-transform group-hover:scale-105">
                                <label for="avatar" class="absolute -bottom-2 -right-2 flex h-8 w-8 cursor-pointer items-center justify-center rounded-xl bg-primary-600 text-white shadow-premium transition-all hover:bg-primary-500 active:scale-90">
                                    <i class="fas fa-camera text-xs"></i>
                                    <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*">
                                </label>
                            </div>
                            <div class="flex-1 space-y-6 w-full">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1.5">
                                        <label for="name" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('forms.labels.name') }}</label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border-none bg-slate-100/50 px-5 py-3.5 text-sm font-bold text-slate-900 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10" required>
                                        @error('name') <p class="text-[10px] font-bold text-rose-500 ml-1 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="email" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('forms.labels.email') }}</label>
                                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border-none bg-slate-100/50 px-5 py-3.5 text-sm font-bold text-slate-900 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10" required>
                                        @error('email') <p class="text-[10px] font-bold text-rose-500 ml-1 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="flex justify-start">
                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                                        <i class="fas fa-save mr-2 text-primary-400"></i>
                                        {{ __('profile.actions.update_profile') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Card -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-500 text-white shadow-soft">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('profile.security') }}</h3>
                    </div>
                </div>
                <div class="p-8">
                    <p class="text-xs font-medium text-slate-500 mb-8">{{ __('profile.security_description') }}</p>
                    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1.5">
                                <label for="current_password" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('forms.labels.current_password') }}</label>
                                <input type="password" id="current_password" name="current_password" class="w-full rounded-2xl border-none bg-slate-100/50 px-5 py-3.5 text-sm font-bold text-slate-900 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10" required>
                                @error('current_password') <p class="text-[10px] font-bold text-rose-500 ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label for="new_password" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('forms.labels.new_password') }}</label>
                                <input type="password" id="new_password" name="new_password" class="w-full rounded-2xl border-none bg-slate-100/50 px-5 py-3.5 text-sm font-bold text-slate-900 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10" required>
                                @error('new_password') <p class="text-[10px] font-bold text-rose-500 ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label for="new_password_confirmation" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('forms.labels.confirm_new_password') }}</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full rounded-2xl border-none bg-slate-100/50 px-5 py-3.5 text-sm font-bold text-slate-900 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10" required>
                            </div>
                        </div>

                        <div class="flex justify-start">
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-6 py-3 text-sm font-bold text-white shadow-premium transition-all hover:bg-rose-500 active:scale-95">
                                <i class="fas fa-shield-alt mr-2"></i>
                                {{ __('profile.actions.update_password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Area -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Completion Card -->
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('profile.profile_completion') }}</span>
                    <i class="fas fa-user-check text-primary-400"></i>
                </div>
                <h3 class="text-4xl font-extrabold tracking-tight mb-4">{{ $profileCompletion }}<span class="text-lg text-primary-400">%</span></h3>
                <div class="w-full rounded-full bg-white/10 p-1 mb-4">
                    <div class="h-1.5 rounded-full bg-primary-500 transition-all duration-1000" style="width: {{ $profileCompletion }}%"></div>
                </div>
                <p class="text-[10px] font-bold text-slate-400 leading-relaxed">{{ __('profile.completion_hint') }}</p>
            </div>

            <!-- Stats Card -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6">{{ __('profile.stats') }}</h3>
                <div class="space-y-6">
                    @foreach ($profileStats as $key => $value)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-primary-600 group-hover:bg-primary-50 transition-colors">
                                <i class="{{ ['accounts' => 'fas fa-university', 'goals' => 'fas fa-bullseye', 'subscriptions' => 'fas fa-sync-alt'][$key] ?? 'fas fa-chart-line' }} text-[10px]"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-500">{{ __('profile.stats_labels.' . $key) }}</span>
                        </div>
                        <span class="text-sm font-black text-slate-900">{{ number_format($value) }}</span>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-8 pt-8 border-t border-slate-50 space-y-4">
                    <div class="flex justify-between text-xs font-medium">
                        <span class="text-slate-400">{{ __('profile.details.currency') }}</span>
                        <span class="text-slate-900 font-bold">{{ optional($settings)->currency_symbol ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-medium">
                        <span class="text-slate-400">{{ __('profile.details.risk_profile') }}</span>
                        <span class="text-slate-900 font-bold">{{ optional($settings)->risk_profile ? ucfirst(optional($settings)->risk_profile) : '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Shortcuts Card -->
            <div class="rounded-3xl bg-primary-600 p-8 text-white shadow-premium">
                <h3 class="text-[10px] font-black text-primary-200 uppercase tracking-widest mb-6">{{ __('profile.shortcuts') }}</h3>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('privacy.settings') }}" class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 text-xs font-bold text-white transition-all hover:bg-white/20 active:scale-95">
                        <i class="fas fa-user-shield text-primary-200"></i>
                        {{ __('profile.actions.open_privacy') }}
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 text-xs font-bold text-white transition-all hover:bg-white/20 active:scale-95">
                        <i class="fas fa-sliders text-primary-200"></i>
                        {{ __('profile.actions.open_settings') }}
                    </a>
                    <a href="{{ route('activity-log.index') }}" class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 text-xs font-bold text-white transition-all hover:bg-white/20 active:scale-95">
                        <i class="fas fa-timeline text-primary-200"></i>
                        Activity Audit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
