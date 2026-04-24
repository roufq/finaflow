@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Telegram Bot Control</h1>
            <p class="text-sm font-medium text-slate-500">Autonomous financial data ingestion via high-performance chat interface</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Core Integration Engine -->
        <div class="lg:col-span-8 space-y-8">
            <div class="rounded-3xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
                <!-- Branding Header -->
                <div class="p-8 flex items-center gap-6 text-white" style="background: linear-gradient(135deg, #0088cc 0%, #00acee 100%);">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md shadow-soft ring-1 ring-white/30">
                        <i class="fab fa-telegram-plane text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tight leading-none">FinaFlow Chat Assistant</h2>
                        <p class="text-sm font-medium text-white/80 mt-2">Elevate your operational speed with cross-platform data entry</p>
                    </div>
                </div>
                
                <div class="p-8 pt-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Semantic Processing Engine</h3>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed mb-6">
                                Fitur ini memungkinkan Anda untuk mencatat expense harian langsung melalui aplikasi Telegram tanpa perlu membuka dashboard FinaFlow. 
                                Sistem AI kami akan memproses narasi teks Anda secara otomatis menjadi transaksi valid.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-5 w-5 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-[10px]">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-tight">Zero-Latency Submission</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-5 w-5 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-[10px]">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-tight">AI Category Clustering</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-5 w-5 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-[10px]">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-tight">Multi-layered Encryption</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-center text-center">
                            <span class="px-3 py-1 bg-white rounded-full text-[8px] font-black text-primary-600 uppercase tracking-widest shadow-sm mb-6 border border-slate-100">Interaction Logic Example</span>
                            <div class="w-full space-y-3 text-left">
                                <div class="p-3 bg-white rounded-xl shadow-soft border border-slate-100">
                                    <p class="text-[9px] font-black text-primary-600 uppercase mb-1">Incoming Transmission:</p>
                                    <code class="text-[10px] font-mono font-bold text-slate-900">Nasi Goreng 25000</code>
                                </div>
                                <div class="p-3 bg-slate-900 rounded-xl shadow-soft">
                                    <p class="text-[9px] font-black text-sky-400 uppercase mb-1">Bot Response:</p>
                                    <code class="text-[10px] font-mono font-bold text-white italic">💰 Transactions Processed Successfully!</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-10 border-t border-slate-50">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">Real-time Connection Topology</h3>
                        
                        @if(!$isBotConfigured)
                            <div class="p-6 rounded-2xl bg-amber-50 border border-amber-100 flex items-center gap-5">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-amber-500 shadow-sm ring-1 ring-amber-200">
                                    <i class="fas fa-exclamation-triangle text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-amber-900 uppercase tracking-tight">Configuration Mismatch Detected</h4>
                                    <p class="text-[10px] text-amber-700 font-medium leading-relaxed mt-1">Bot variables missing in system environment. Initialize <code>TELEGRAM_BOT_TOKEN</code> in your secure configuration.</p>
                                </div>
                            </div>
                        @elseif($user->telegram_id)
                            <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-100 flex flex-col md:flex-row md:items-center gap-6">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-200 relative">
                                    <i class="fas fa-link text-xl"></i>
                                    <span class="absolute top-0 right-0 h-4 w-4 bg-emerald-500 rounded-full border-2 border-white animate-pulse"></span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-black text-emerald-900 uppercase tracking-tight leading-none">Operational Link Established</h4>
                                    <p class="text-[11px] text-emerald-700 font-medium mt-2">Active Node ID: <span class="font-bold font-mono">{{ $user->telegram_id }}</span></p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="https://t.me/{{ $telegramBotName }}" target="_blank" class="h-10 px-6 flex items-center justify-center rounded-xl bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest shadow-soft hover:bg-emerald-500 transition-all">Launch Chat</a>
                                    <form action="{{ route('integrations.telegram-bot.disconnect') }}" method="POST" onsubmit="return confirm('Purge the current Telegram pairing session?')">
                                        @csrf
                                        <button type="submit" class="h-10 px-6 flex items-center justify-center rounded-xl bg-white text-rose-600 border border-rose-100 text-[10px] font-black uppercase tracking-widest hover:bg-rose-50 transition-all">Sever Link</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="p-8 rounded-2xl bg-slate-900 text-white flex flex-col items-center text-center">
                                <div class="h-16 w-16 rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center text-white/20 mb-6">
                                    <i class="fab fa-telegram-plane text-3xl"></i>
                                </div>
                                <h4 class="text-lg font-black uppercase tracking-tight">Protocol Authorization Required</h4>
                                <p class="text-sm text-white/60 max-w-sm mt-3 mb-8">Synchronize your Telegram account to activate conversational financial tracking.</p>
                                
                                <div class="flex flex-col items-center gap-4 w-full max-w-xs">
                                    <a href="https://t.me/{{ $telegramBotName }}?start={{ $telegramToken }}" target="_blank" class="w-full h-14 flex items-center justify-center rounded-2xl bg-sky-500 text-white font-black uppercase tracking-widest shadow-soft hover:bg-sky-400 transition-all">
                                        Establish Proxy Link
                                    </a>
                                    <p class="text-[9px] font-bold text-white/30 uppercase tracking-widest">Execute /start protocol after redirect</p>
                                </div>

                                <div class="mt-10 pt-8 border-t border-white/5 w-full text-left">
                                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-4">Fallback Manual Routing:</p>
                                    <div class="p-4 bg-black/30 rounded-xl border border-white/5 font-mono text-[10px] space-y-2">
                                        <p><span class="text-white/20">01.</span> Primary Search: <span class="text-sky-400">@ {{ $telegramBotName }}</span></p>
                                        <p><span class="text-white/20">02.</span> Transmission: <span class="text-emerald-400">/start {{ $telegramToken }}</span></p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Foreground Performance Ledger -->
            <div class="rounded-3xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Integration Forensic Trace</h3>
                    <div class="flex h-2 w-2 rounded-full bg-slate-200"></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-50 text-[10px] font-bold uppercase text-slate-400 tracking-widest">
                                <th class="px-6 py-4">Event Type</th>
                                <th class="px-6 py-4">Contextual Narrative</th>
                                <th class="px-6 py-4 text-right">Temporal Stamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($activities ?? [] as $activity)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    @php
                                        $activityAction = $activity->action ?? '';
                                        $color = \Illuminate\Support\Str::contains($activityAction, 'Connected') ? 'emerald' : 
                                                (\Illuminate\Support\Str::contains($activityAction, 'Disconnected') ? 'rose' : 
                                                (\Illuminate\Support\Str::contains($activityAction, 'Conflict') ? 'amber' : 'blue'));
                                    @endphp
                                    <span class="inline-flex items-center rounded-lg bg-{{ $color }}-50 px-2 py-1 text-[9px] font-black uppercase text-{{ $color }}-600 ring-1 ring-inset ring-{{ $color }}-100">
                                        {{ $activityAction }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs font-bold text-slate-900 leading-tight">{{ $activity->description ?? 'No description available' }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ optional($activity->created_at)->diffForHumans() ?? 'Unknown time' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-[10px] font-medium text-slate-300 italic uppercase tracking-widest">Inaugural trace pending activity detection.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Infrastructure & Instructions -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Infrastructure Configuration (BYOB) -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <div class="flex items-center gap-3 mb-8">
                    <div class="h-8 w-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <i class="fas fa-robot text-sm"></i>
                    </div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Secure Bot Override</h3>
                </div>
                
                <p class="text-[10px] text-slate-500 font-medium leading-relaxed mb-8">Deploy your proprietary Telegram Bot protocols by inputting credentials from <strong>@BotFather</strong> below.</p>
                
                <form action="{{ route('integrations.telegram-bot.settings') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Bot API Token</label>
                        <input type="password" name="telegram_bot_token" placeholder="123456:ABC-DEF..." value="{{ $user->telegram_bot_token }}" class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 focus:ring-1 focus:ring-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Unique Username</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-300">@</span>
                            <input type="text" name="telegram_bot_username" placeholder="finaflow_bot" value="{{ $user->telegram_bot_username }}" class="w-full h-11 rounded-xl bg-slate-50 border-transparent pl-8 pr-4 text-xs font-bold text-slate-900 focus:ring-1 focus:ring-primary-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Communication Bridge (Optional)</label>
                        <input type="text" name="telegram_proxy_url" placeholder="https://gateway.internal/hook" value="{{ $user->telegram_proxy_url }}" class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 focus:ring-1 focus:ring-primary-500 outline-none">
                        <p class="mt-1.5 text-[9px] text-slate-400 font-medium leading-tight italic">Redirects transmissions through a secured proxy gateway.</p>
                    </div>
                    <button type="submit" class="w-full h-12 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-soft hover:bg-slate-800 transition-all">Save Parameters</button>
                </form>

                @if($user->telegram_bot_token)
                <div class="mt-8 pt-8 border-t border-slate-50">
                    <form action="{{ route('integrations.telegram-bot.webhook.set') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full h-10 border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-sync-alt text-[9px]"></i>
                            Propagate Webhook
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Execution Manual -->
            <div class="rounded-3xl bg-slate-50 p-8 border border-slate-100">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8">Operational Directives</h3>
                <div class="space-y-8">
                    <div class="flex gap-4">
                        <div class="h-1.5 w-1.5 mt-1.5 rounded-full bg-primary-500"></div>
                        <div>
                            <h4 class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">Handshake Protocol</h4>
                            <p class="text-[10px] text-slate-500 font-medium leading-relaxed mt-1">Initiate command <strong>/start</strong> to pair your biometric chat identity with the FinaFlow core.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="h-1.5 w-1.5 mt-1.5 rounded-full bg-primary-500"></div>
                        <div>
                            <h4 class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">Unified Submission</h4>
                            <p class="text-[10px] text-slate-500 font-medium leading-relaxed mt-1">Transmit: <code>[Label] [Magnitude]</code>. For example: "Bensin 20000" to auto-log expenses.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="h-1.5 w-1.5 mt-1.5 rounded-full bg-primary-500"></div>
                        <div>
                            <h4 class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">Multilateral Account Steering</h4>
                            <p class="text-[10px] text-slate-500 font-medium leading-relaxed mt-1">Append account alias for precise routing. Example: "Bensin 20000 Mandiri".</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-4 rounded-xl bg-white border border-slate-200 flex items-start gap-3">
                    <i class="fas fa-info-circle text-primary-500 mt-0.5 text-xs"></i>
                    <p class="text-[9px] font-bold text-slate-600 leading-relaxed uppercase">Assistant utilizes the <strong>Primary Active Ledger</strong> as the default liquidity source.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
