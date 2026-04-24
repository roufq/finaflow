@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('privacy.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">Manage your data fortress and security protocols for maximum capital confidentiality.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
            <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
            {{ __('forms.labels.back') }}
        </a>
    </div>

    @if(session('success'))
    <div class="relative overflow-hidden rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Main Privacy Matrix -->
        <div class="lg:col-span-8 space-y-8">
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8">{{ __('privacy.data_privacy_preferences') }}</h3>
                
                <form method="POST" action="{{ route('privacy.update') }}" class="space-y-10">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-8">
                        <!-- Data Analytics -->
                        <div class="flex items-start justify-between gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:bg-white hover:shadow-soft">
                            <div class="flex-1">
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ __('privacy.data_analytics') }}</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed mt-1 font-medium">{{ __('privacy.data_analytics_description') }}</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="data_analytics" value="0">
                                <input type="checkbox" name="data_analytics" value="1" class="peer sr-only" {{ $privacySettings->data_analytics ? 'checked' : '' }}>
                                <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-focus:ring-2 peer-focus:ring-primary-500/20"></div>
                            </label>
                        </div>

                        <!-- Behavioral Insights -->
                        <div class="flex items-start justify-between gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:bg-white hover:shadow-soft">
                            <div class="flex-1">
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ __('privacy.behavioral_insights') }}</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed mt-1 font-medium">{{ __('privacy.behavioral_insights_description') }}</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="behavioral_insights" value="0">
                                <input type="checkbox" name="behavioral_insights" value="1" class="peer sr-only" {{ $privacySettings->behavioral_insights ? 'checked' : '' }}>
                                <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-focus:ring-2 peer-focus:ring-primary-500/20"></div>
                            </label>
                        </div>

                        <!-- Third Party Sharing -->
                        <div class="flex items-start justify-between gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:bg-white hover:shadow-soft">
                            <div class="flex-1">
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ __('privacy.third_party_sharing') }}</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed mt-1 font-medium">{{ __('privacy.third_party_sharing_description') }}</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="third_party_sharing" value="0">
                                <input type="checkbox" name="third_party_sharing" value="1" class="peer sr-only" {{ $privacySettings->third_party_sharing ? 'checked' : '' }}>
                                <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-focus:ring-2 peer-focus:ring-primary-500/20"></div>
                            </label>
                        </div>

                        <!-- Data Anonymization -->
                        <div class="flex items-start justify-between gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:bg-white hover:shadow-soft">
                            <div class="flex-1">
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ __('privacy.data_anonymization') }}</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed mt-1 font-medium">{{ __('privacy.data_anonymization_description') }}</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="data_anonymization" value="0">
                                <input type="checkbox" name="data_anonymization" value="1" class="peer sr-only" {{ $privacySettings->data_anonymization ? 'checked' : '' }}>
                                <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-focus:ring-2 peer-focus:ring-primary-500/20"></div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-50 flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                            <i class="fas fa-shield-alt mr-2 text-primary-400"></i>
                            {{ __('privacy.save_privacy_settings') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Management Protocols -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8">{{ __('privacy.account_management') }}</h3>
                <div class="grid grid-cols-1 gap-10 md:grid-cols-2">
                    <!-- Data Export -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ __('privacy.data_export') }}</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">{{ __('privacy.data_export_description') }}</p>
                        <a href="{{ route('privacy.export-data') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-50 px-6 py-2.5 text-xs font-bold text-slate-600 ring-1 ring-slate-200 transition-all hover:bg-slate-100 hover:text-slate-900">
                            <i class="fas fa-download mr-2 text-[10px]"></i>
                            {{ __('privacy.export_my_data') }}
                        </a>
                    </div>

                    <!-- Account Termination -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ __('privacy.account_deletion') }}</h4>
                        @if($privacySettings->account_deletion)
                            <div class="rounded-2xl bg-rose-50 p-4 border border-rose-100">
                                <p class="text-[10px] font-black text-rose-900 uppercase mb-1">{{ __('privacy.deletion_requested') }}</p>
                                <p class="text-[10px] text-rose-700 leading-tight mb-4">{{ __('privacy.deletion_pending_message') }}</p>
                                <form method="POST" action="{{ route('privacy.cancel-deletion') }}">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-black text-rose-600 uppercase hover:underline">
                                        <i class="fas fa-undo mr-1"></i> {{ __('privacy.cancel_deletion_request') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ __('privacy.account_deletion_description') }}</p>
                            <button @click="$dispatch('open-modal', 'delete-account')" class="inline-flex items-center justify-center rounded-xl bg-rose-50 px-6 py-2.5 text-xs font-bold text-rose-600 ring-1 ring-rose-100 transition-all hover:bg-rose-100">
                                <i class="fas fa-trash-alt mr-2 text-[10px]"></i>
                                {{ __('privacy.request_account_deletion') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Information & Policy -->
        <div class="lg:col-span-4 space-y-8">
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-8">{{ __('privacy.privacy_information') }}</h3>
                
                <div class="space-y-10">
                    <div>
                        <h4 class="text-[10px] font-black text-white uppercase tracking-widest mb-4">{{ __('privacy.what_we_collect') }}</h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <span class="h-1.5 w-1.5 rounded-full bg-primary-500 shrink-0 mt-1.5"></span>
                                <span class="text-[11px] font-medium text-slate-400">{{ __('privacy.financial_transaction_data') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="h-1.5 w-1.5 rounded-full bg-primary-500 shrink-0 mt-1.5"></span>
                                <span class="text-[11px] font-medium text-slate-400">{{ __('privacy.account_budget_info') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="h-1.5 w-1.5 rounded-full bg-primary-500 shrink-0 mt-1.5"></span>
                                <span class="text-[11px] font-medium text-slate-400">{{ __('privacy.behavioral_insights') }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-10 border-t border-white/5">
                        <h4 class="text-[10px] font-black text-white uppercase tracking-widest mb-4">{{ __('privacy.your_rights') }}</h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <span class="h-1 w-2 rounded-full bg-emerald-500 shrink-0 mt-2"></span>
                                <span class="text-[11px] font-medium text-slate-400">{{ __('privacy.access_data') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="h-1 w-2 rounded-full bg-emerald-500 shrink-0 mt-2"></span>
                                <span class="text-[11px] font-medium text-slate-400">{{ __('privacy.opt_out_sharing') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10 rounded-2xl bg-white/5 p-5 ring-1 ring-white/10">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-shield-alt text-primary-400 text-xs"></i>
                        <h5 class="text-[10px] font-black uppercase text-white">{{ __('privacy.security') }}</h5>
                    </div>
                    <p class="text-[10px] font-medium text-slate-500 leading-relaxed italic">{{ __('privacy.security_note') }}</p>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100 text-center">
                <i class="fas fa-file-contract text-slate-200 text-3xl mb-4"></i>
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-2">{{ __('privacy.need_more_info') }}</h4>
                <div class="flex flex-col gap-1">
                    <a href="#" class="text-[10px] font-black text-primary-600 uppercase hover:underline italic">{{ __('privacy.view_full_privacy_policy') }}</a>
                    <a href="#" class="text-[10px] font-black text-slate-400 uppercase hover:text-slate-600 transition-colors">{{ __('privacy.contact_data_protection_officer') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deletion Modal (Alpine.js powered) -->
<div x-data="{ open: false }" @open-modal.window="if($event.detail === 'delete-account') open = true" class="relative z-50" x-show="open" x-cloak>
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white p-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">{{ __('privacy.confirm_account_deletion') }}</h3>
                        <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="rounded-2xl bg-rose-50 p-5 ring-1 ring-rose-100">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-exclamation-triangle text-rose-500"></i>
                            <h4 class="text-[11px] font-black text-rose-900 uppercase">{{ __('privacy.warning') }}</h4>
                        </div>
                        <p class="text-[11px] font-bold text-rose-700 leading-relaxed">{{ __('privacy.deletion_warning') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">{{ __('privacy.before_proceeding') }}:</p>
                        <ul class="space-y-3">
                            @foreach([__('privacy.export_data_first'), __('privacy.family_data_deleted'), __('privacy.subscription_info_removed'), __('privacy.lose_premium_access')] as $item)
                            <li class="flex items-center gap-3 text-[11px] font-bold text-slate-600">
                                <i class="fas fa-check text-[8px] text-slate-300"></i>
                                {{ $item }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="pt-6 border-t border-slate-50 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <button @click="open = false" class="px-6 py-3 text-sm font-bold text-slate-500">{{ __('forms.labels.cancel') }}</button>
                        <form method="POST" action="{{ route('privacy.request-deletion') }}">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto rounded-xl bg-rose-600 px-8 py-3.5 text-sm font-extrabold text-white shadow-lg transition-all hover:bg-rose-700 active:scale-95">
                                <i class="fas fa-trash-alt mr-2"></i>
                                {{ __('privacy.yes_delete_account') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
