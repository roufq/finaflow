@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">User Access Details</h1>
            <p class="text-sm font-medium text-slate-500">Configure core system access and operational status for <span class="text-slate-900 font-bold">{{ $user->name }}</span>.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
            <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
            Back
        </a>
    </div>

    <!-- Notifications -->
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
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <p class="text-sm font-bold text-rose-900">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Main Configuration Panel -->
        <div class="lg:col-span-8 space-y-8">
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Access Management Matrix</h3>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-black uppercase tracking-tighter {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">
                            {{ $user->is_active ? 'Status: Active' : 'Status: Inactive' }}
                        </span>
                    </div>
                </div>

                @if($user->hasRole('admin'))
                    <div class="rounded-2xl bg-primary-50 border border-primary-100 p-6 flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-primary-500 shadow-sm ring-1 ring-primary-100">
                            <i class="fas fa-user-shield text-lg text-slate-900"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">Privileged Account detected</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">Administrator access rights are immutable. This account has unrestricted access to all system modules and financial data protocols as defined in the core security policy.</p>
                        </div>
                    </div>
                @else
                    @php
                        $selectedPlan = old('plan', $activePlan !== 'custom' ? $activePlan : null);
                    @endphp
                    <form method="POST" action="{{ route('admin.users.permissions', $user) }}" class="space-y-10">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">Sidebar Access Protocols (SaaS Tier)</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed mt-1 font-medium italic">Select an operational tier to automatically propagate sidebar permissions. Custom adjustments will be overridden by the selected plan.</p>
                            </div>

                            @if($activePlan === 'custom')
                                <div class="rounded-2xl bg-amber-50 border border-amber-100 p-4 flex items-center gap-3">
                                    <i class="fas fa-sliders-h text-amber-500 text-xs"></i>
                                    <p class="text-[10px] font-bold text-amber-900 uppercase tracking-tight">Non-Standard configuration detected. Align with a tier to ensure system stability.</p>
                                </div>
                            @endif

                            <div x-data="{ selected: '{{ $selectedPlan }}' }" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($plans as $planKey => $plan)
                                    <label class="relative flex min-h-[160px] cursor-pointer group">
                                        <input type="radio" name="plan" value="{{ $planKey }}" class="sr-only" x-model="selected">
                                        <div class="w-full rounded-2xl border p-6 transition-all" :class="selected === '{{ $planKey }}' ? 'bg-white ring-2 ring-slate-900 shadow-premium border-transparent' : 'bg-slate-50 border-slate-100 hover:bg-white hover:shadow-soft'">
                                            <div class="flex justify-between items-start mb-4">
                                                <div>
                                                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $plan['label'] }}</h4>
                                                    <p class="text-[10px] text-slate-500 font-medium leading-tight mt-0.5">{{ $plan['description'] }}</p>
                                                </div>
                                                <div class="h-5 w-5 rounded-full border transition-all flex items-center justify-center shrink-0" :class="selected === '{{ $planKey }}' ? 'bg-slate-900 border-slate-900' : 'border-slate-300'">
                                                    <div class="h-1.5 w-1.5 rounded-full bg-white transition-opacity" :class="selected === '{{ $planKey }}' ? 'opacity-100' : 'opacity-0'"></div>
                                                </div>
                                            </div>
                                            
                                            <ul class="space-y-2">
                                                @foreach(array_slice($plan['features'], 0, 4) as $feature)
                                                    <li class="flex items-center gap-2 text-[10px] font-bold text-slate-600">
                                                        <i class="fas fa-check text-[8px] text-slate-300"></i>
                                                        {{ $feature }}
                                                    </li>
                                                @endforeach
                                                @if(count($plan['features']) > 4)
                                                    <li class="text-[9px] font-black text-slate-400 uppercase tracking-widest pl-4 mt-2">+ {{ count($plan['features']) - 4 }} More modules</li>
                                                @endif
                                            </ul>

                                            @if($activePlan === $planKey)
                                                <div class="mt-4 pt-4 border-t border-slate-200/50 flex items-center gap-1.5">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">Active Deployment</span>
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-slate-50">
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                                <i class="fas fa-layer-group mr-2 text-primary-400"></i>
                                Commit Access Plan
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Operational Status Section -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8">System Status Protocol</h3>
                
                <form method="POST" action="{{ route('admin.users.status', $user) }}" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex-1">
                            <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">Account Operational State</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed mt-1 font-medium italic">Toggle the user's ability to initialize sessions. Deactivated users will be locked out of the enterprise environment immediately.</p>
                        </div>
                        <div x-data="{ active: {{ $user->is_active ? 'true' : 'false' }} }" class="relative inline-flex items-center h-8 w-14 shrink-0 cursor-pointer group" @click="active = !active">
                            <input type="hidden" name="is_active" :value="active ? 1 : 0">
                            <!-- Switch Track -->
                            <div class="absolute inset-0 rounded-full transition-colors duration-300 ring-1 ring-inset" :class="active ? 'bg-emerald-100 ring-emerald-200' : 'bg-slate-100 ring-slate-200'"></div>
                            <!-- Switch Thumb -->
                            <div class="absolute left-1 top-1 h-6 w-6 rounded-full shadow-sm transition-all duration-300 ease-in-out" :class="active ? 'bg-emerald-600' : 'bg-slate-400'" :style="active ? 'transform: translateX(24px)' : 'transform: translateX(0)'"></div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="deactivation_message" class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Deactivation Response Message</label>
                            <span class="text-[9px] font-medium text-slate-400 uppercase tracking-tighter">Displayed upon failed authentication</span>
                        </div>
                        <textarea 
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 p-4 text-[11px] font-medium text-slate-700 placeholder:text-slate-400 focus:border-slate-900 focus:ring-0 transition-all min-h-[100px]" 
                            id="deactivation_message" 
                            name="deactivation_message" 
                            placeholder="Example: Your account is temporarily deactivated by terminal administration. Contact security for re-authorization."
                        >{{ old('deactivation_message', $user->deactivation_message) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-white px-8 py-3 text-sm font-extrabold text-slate-900 ring-1 ring-slate-200 shadow-soft transition-all hover:bg-slate-50 active:scale-95">
                            <i class="fas fa-power-off mr-2 text-slate-400"></i>
                            Update Operational State
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Identity Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10 overflow-hidden relative">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <i class="fas fa-fingerprint text-6xl"></i>
                </div>
                
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-10 relative z-10">Account Identity</h3>
                
                <div class="space-y-8 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-xl font-black shadow-lg">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-lg font-black tracking-tight leading-none">{{ $user->name }}</h4>
                            <p class="text-[11px] font-medium text-slate-400 lowercase mt-1">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 pt-8 border-t border-white/5">
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Assigned Roles</p>
                            <p class="text-xs font-bold">{{ $user->roles->pluck('name')->map(fn($n) => strtoupper($n))->implode(', ') ?: 'NO ROLES ASSIGNED' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Network Entry</p>
                            <p class="text-xs font-bold">{{ $user->created_at->format('M d, Y') }} <span class="text-slate-500 font-medium">({{ $user->created_at->diffForHumans() }})</span></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">System Unique ID</p>
                            <p class="text-[10px] font-mono text-slate-400 break-all select-all">#USER-{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-10 border-t border-white/5">
                    <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10">
                        <div class="flex items-center gap-3 mb-2 text-rose-400">
                            <i class="fas fa-shield-alt text-xs"></i>
                            <h5 class="text-[10px] font-black uppercase tracking-widest">Security Note</h5>
                        </div>
                        <p class="text-[10px] font-medium text-slate-500 leading-relaxed italic">Unauthorized access modifications may result in immediate terminal lockdown and audit trail generation.</p>
                    </div>
                </div>
            </div>

            <!-- Quick References -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Management Tools</h3>
                <div class="grid grid-cols-1 gap-3">
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 opacity-50 cursor-not-allowed group">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-user-edit text-slate-400"></i>
                            <span class="text-[11px] font-black text-slate-900 uppercase">Modify Profile</span>
                        </div>
                        <i class="fas fa-lock text-[10px] text-slate-300"></i>
                    </div>
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 opacity-50 cursor-not-allowed group">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-history text-slate-400"></i>
                            <span class="text-[11px] font-black text-slate-900 uppercase">Audit Logs</span>
                        </div>
                        <i class="fas fa-lock text-[10px] text-slate-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
