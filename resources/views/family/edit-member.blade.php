@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-10 animate-fade-in pb-20">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-0.5 text-[10px] font-black text-primary-600 uppercase tracking-widest ring-1 ring-inset ring-primary-500/20">Operational Evolution</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Operative ID: #{{ str_pad($member->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 line-clamp-1">Refine operative profile</h1>
            <p class="text-sm font-medium text-slate-500">Update parameters for an existing unit participant</p>
        </div>
        <a href="{{ route('family.members') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
            <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
            Back to Registry
        </a>
    </div>

    <form method="POST" action="{{ route('family.members.update', $member) }}" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        @csrf
        @method('PUT')
        
        <div class="lg:col-span-2 space-y-8">
            <!-- Core Metadata Card -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden border-b-4 border-primary-500">
                <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-xl bg-slate-900 flex items-center justify-center text-white">
                            <i class="fas fa-user-edit text-xs"></i>
                        </div>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Profile Parameters</h2>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label for="name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Legal Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $member->name) }}" required
                               class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('name') ring-red-500 @enderror">
                        @error('name') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="relationship" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Relational Nexus *</label>
                            <select id="relationship" name="relationship" required
                                    class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                                @php $relationships = ['Spouse', 'Child', 'Parent', 'Sibling', 'Grandparent', 'Grandchild', 'Other']; @endphp
                                @foreach($relationships as $rel)
                                    <option value="{{ $rel }}" {{ old('relationship', $member->relationship) === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="date_of_birth" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Lifecycle Initiation</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($member->date_of_birth)->format('Y-m-d')) }}"
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="monthly_allowance" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Allocated Monthly Resource (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 font-bold text-xs uppercase">Rp</div>
                                <input type="number" id="monthly_allowance" name="monthly_allowance" value="{{ old('monthly_allowance', $member->monthly_allowance ?? 0) }}" min="0" step="1000"
                                       class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-black text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="current_balance" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Manual Ledger Override (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 font-bold text-xs uppercase">Rp</div>
                                <input type="number" id="current_balance" name="current_balance" value="{{ old('current_balance', $member->current_balance ?? 0) }}" min="0" step="1000"
                                       class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-black text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <label class="relative inline-flex items-center cursor-pointer group">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $member->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none ring-4 ring-transparent peer-checked:ring-primary-500/10 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 transition-all"></div>
                            <span class="ms-3 text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-900 transition-colors italic">Protocol: Operational Active</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Snapshot Card -->
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white/40 mb-6 italic underline decoration-primary-500/30">Operative Snapshot</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4 border-b border-white/5 pb-6">
                        <div class="h-16 w-16 rounded-2xl bg-white/10 flex items-center justify-center shadow-lg border border-white/10">
                            <span class="text-2xl font-black text-primary-400 uppercase italic font-serif leading-none">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-lg font-black text-white italic tracking-tight leading-none mb-1">{{ $member->name }}</p>
                            <span class="inline-flex rounded-md bg-white/5 px-2 py-0.5 text-[9px] font-black text-white/40 uppercase tracking-widest ring-1 ring-inset ring-white/10">{{ $member->relationship }}</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-[8px] font-black text-white/40 uppercase mb-1">Accumulated Balance</p>
                            <p class="text-sm font-black text-white">Rp {{ number_format($member->current_balance ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-[8px] font-black text-white/40 uppercase mb-1">Current Status</p>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="h-1.5 w-1.5 rounded-full {{ $member->is_active ? 'bg-green-500 animate-pulse' : 'bg-slate-500' }}"></span>
                                <span class="text-[9px] font-black text-white uppercase">{{ $member->is_active ? 'Online' : 'Offline' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Card -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 italic underline decoration-primary-500 decoration-2">Registry Authentication</h3>
                <p class="text-[10px] text-slate-400 uppercase font-bold leading-relaxed mb-8">Refining these parameters will immediately recalibrate unit-wide financial projections.</p>
                
                <div class="space-y-4">
                    <button type="submit" class="w-full rounded-2xl bg-primary-600 px-6 py-4 text-xs font-black uppercase tracking-widest text-white shadow-premium transition-all hover:bg-primary-700 active:scale-95">
                        Authorize Evolution
                    </button>
                    <a href="{{ route('family.members') }}" class="block w-full text-center mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors italic">Abort Modifications</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
