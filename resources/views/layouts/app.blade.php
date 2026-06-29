<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-surface-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FinaFlow Pro - Smart Wealth Management</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans text-slate-800 antialiased" data-page="{{ $page ?? '' }}" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden bg-surface-50">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-cloak 
             class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden" 
             @click="sidebarOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Sidebar -->
        <aside id="sidebar" 
               class="fixed inset-y-0 left-0 z-50 w-72 transform bg-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="flex h-full flex-col border-r border-slate-100">
                <!-- Brand -->
                <div class="flex h-20 shrink-0 items-center px-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-500 text-white shadow-premium">
                            <i class="fas fa-chart-line text-lg"></i>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900">FinaFlow<span class="text-primary-500">.</span></span>
                    </a>
                </div>

                <!-- Navigation (Scrollable) -->
                <nav id="sidebar-nav" class="flex-1 space-y-8 overflow-y-auto px-6 py-4 custom-scrollbar">
                    
                    <!-- Overview -->
                    <div>
                        <h3 class="px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Intelligence</h3>
                        <ul class="mt-4 space-y-1">
                            @can('access dashboard')
                            <li>
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('dashboard') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-th-large w-5 opacity-70"></i>
                                    <span>Control Center</span>
                                </a>
                            </li>
                            @endcan
                            @can('view reports')
                            <li>
                                <a href="{{ route('analytics.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('analytics.index') || request()->routeIs('reports.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-chart-pie w-5 opacity-70"></i>
                                    <span>Analytics</span>
                                </a>
                            </li>
                            @endcan
                            <li>
                                <a href="{{ route('activity-log.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('activity-log.index') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-history w-5 opacity-70"></i>
                                    <span>Activity Logs</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Core Finance -->
                    <div>
                        <h3 class="px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Liquidity</h3>
                        <ul class="mt-4 space-y-1">
                            @can('manage accounts')
                            <li>
                                <a href="{{ route('accounts.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('accounts.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-university w-5 opacity-70"></i>
                                    <span>Cash Accounts</span>
                                </a>
                            </li>
                            @endcan
                            @can('manage transactions')
                            <li>
                                <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('transactions.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-exchange-alt w-5 opacity-70"></i>
                                    <span>Transactions</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('transfers.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('transfers.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-sync-alt w-5 opacity-70"></i>
                                    <span>Transfers</span>
                                </a>
                            </li>
                            @endcan
                            @can('manage budgets')
                            <li>
                                <a href="{{ route('budgets.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('budgets.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-piggy-bank w-5 opacity-70"></i>
                                    <span>Budgets</span>
                                </a>
                            </li>
                            @endcan
                            @can('manage goals')
                            <li>
                                <a href="{{ route('goals.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('goals.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-bullseye w-5 opacity-70"></i>
                                    <span>Goals & Milestones</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>

                    <!-- Wealth & Liabilities -->
                    <div>
                        <h3 class="px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Wealth Stack</h3>
                        <ul class="mt-4 space-y-1">
                            @can('view reports')
                            <li>
                                <a href="{{ route('net-worth.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('net-worth.index') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-briefcase w-5 opacity-70"></i>
                                    <span>Net Worth Summary</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('investments.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('investments.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-chart-line w-5 opacity-70"></i>
                                    <span>Investments</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('assets.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('assets.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-gem w-5 opacity-70"></i>
                                    <span>Physical Assets</span>
                                </a>
                            </li>
                            @endcan
                            @can('manage budgets')
                            <li>
                                <a href="{{ route('debts.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('debts.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-hand-holding-usd w-5 opacity-70"></i>
                                    <span>Debt Management</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>

                    <!-- Engagement & Family -->
                    @can('manage family')
                    <div>
                        <h3 class="px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Community</h3>
                        <ul class="mt-4 space-y-1">
                            <li>
                                <a href="{{ route('family.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('family.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-users w-5 opacity-70"></i>
                                    <span>Family Connect</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('subscriptions.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('subscriptions.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-calendar-check w-5 opacity-70"></i>
                                    <span>Subscriptions</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('rewards.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('rewards.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-medal w-5 opacity-70"></i>
                                    <span>Loyalty & Perks</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endcan

                    <!-- AI & Behavioral -->
                    <div>
                        <h3 class="px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Optimization</h3>
                        <ul class="mt-4 space-y-1">
                            <li>
                                <a href="{{ route('insights.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('insights.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-magic w-5 opacity-70"></i>
                                    <span>AI Assistant</span>
                                </a>
                            </li>
                            @can('manage behavioral')
                            <li>
                                <a href="{{ route('behavioral.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('behavioral.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-brain w-5 opacity-70"></i>
                                    <span>Self Engineering</span>
                                </a>
                            </li>
                            @endcan
                            @can('manage automations')
                            <li>
                                <a href="{{ route('automations.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('automations.*') || request()->routeIs('bank-integrations.*') || request()->routeIs('api-integrations.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-cogs w-5 opacity-70"></i>
                                    <span>Automation Suite</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('integrations.telegram-bot') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('integrations.telegram-bot') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fab fa-telegram w-5 opacity-70"></i>
                                    <span>Telegram Assistant</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('email-sources.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('email-sources.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-envelope-open-text w-5 opacity-70"></i>
                                    <span>Email Parser</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>

                    <!-- Knowledge Base -->
                    <div>
                        <h3 class="px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Eduction</h3>
                        <ul class="mt-4 space-y-1">
                            <li>
                                <a href="{{ route('education.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('education.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-graduation-cap w-5 opacity-70"></i>
                                    <span>Academy</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('coaching.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('coaching.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-chalkboard-teacher w-5 opacity-70"></i>
                                    <span>Coaching</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- System -->
                    <div>
                        <h3 class="px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Operations</h3>
                        <ul class="mt-4 space-y-1">
                            <li>
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('settings.*') || request()->routeIs('categories.*') || request()->routeIs('tags.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-sliders-h w-5 opacity-70"></i>
                                    <span>Global Settings</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('privacy.settings') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('privacy.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-user-lock w-5 opacity-70"></i>
                                    <span>Privacy & Data</span>
                                </a>
                            </li>
                            @can('manage users')
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-slate-50 {{ request()->routeIs('admin.users.*') ? 'sidebar-item-active shadow-sm' : 'text-slate-600' }}">
                                    <i class="fas fa-shield-alt w-5 opacity-70"></i>
                                    <span>Identity MGMT</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </nav>

                <!-- User Profile (Sidebar Bottom) -->
                <div class="border-t border-slate-100 p-6 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.02)]">
                    <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-4 transition-all hover:bg-slate-100 group">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white font-bold text-primary-500 shadow-sm border border-slate-100 group-hover:scale-105 transition-transform">
                            {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900 leading-none mb-1">{{ Auth::user()->name ?? 'Guest' }}</p>
                            <div class="flex items-center gap-1.5">
                                <div class="h-1 w-1 rounded-full bg-green-500"></div>
                                <p class="truncate text-[9px] uppercase font-bold tracking-widest text-slate-400">Node Administrator</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Logout" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-all hover:bg-red-50 hover:text-red-500">
                                <i class="fas fa-power-off text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-1 flex-col overflow-hidden">
            
            <!-- Topbar (Executive Glass) -->
            <header class="glass flex h-20 shrink-0 items-center justify-between px-6 lg:px-10 z-40">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="flex h-11 w-11 items-center justify-center rounded-2xl text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-900 lg:hidden">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <div class="relative hidden xl:block">
                        <form action="{{ url()->current() }}" method="GET">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Omni-search financial logs..." class="h-12 w-96 rounded-2xl border-none outline-none bg-slate-100/80 px-11 text-sm font-medium transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 active:scale-[0.99]">
                        </form>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Notifications -->
                    @php
                        $unreadNotifications = Auth::user()->unreadNotifications;
                        $hasUnread = $unreadNotifications->count() > 0;
                    @endphp
                    <div class="relative" x-data="{ open: false, hasUnread: {{ $hasUnread ? 'true' : 'false' }} }">
                        <button @click="open = !open" @click.away="open = false" class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-500 transition-all hover:bg-slate-50 hover:text-slate-900 hover:shadow-premium hover:-translate-y-0.5 active:scale-95">
                            <i class="far fa-bell text-base"></i>
                            @if($hasUnread)
                                <span class="absolute right-4 top-4 h-2 w-2 rounded-full bg-red-500 ring-4 ring-white animate-pulse"></span>
                            @endif
                        </button>

                        <div x-show="open" x-cloak 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 mt-3 w-80 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-100 z-50">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">Intelligence Feed</h4>
                                @if($hasUnread)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[9px] font-bold text-primary-500 uppercase cursor-pointer hover:underline">Clear All</button>
                                </form>
                                @endif
                            </div>
                            
                            <div class="space-y-1 max-h-96 overflow-y-auto custom-scrollbar">
                                @forelse($unreadNotifications->take(5) as $notification)
                                    <div class="p-3 rounded-2xl bg-primary-50 border border-primary-100/50 group transition-all hover:bg-white hover:shadow-soft">
                                        <div class="flex gap-3">
                                            <div class="h-8 w-8 rounded-xl bg-white flex items-center justify-center text-primary-500 shadow-sm shrink-0">
                                                <i class="fas {{ $notification->data['type'] === 'warning' ? 'fa-exclamation-triangle text-amber-500' : 'fa-info-circle text-primary-500' }} text-xs"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-[11px] font-bold text-slate-900 leading-tight">{{ $notification->data['title'] ?? 'New Notification' }}</p>
                                                <p class="text-[9px] text-slate-500 mt-1 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                                <div class="mt-2 flex items-center justify-between">
                                                    <span class="text-[8px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-[9px] font-bold text-primary-500 hover:underline">Mark Read</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                    <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mb-3">
                                        <i class="fas fa-check-circle text-xs"></i>
                                    </div>
                                    <p class="text-[11px] font-bold text-slate-900 leading-tight">All caught up</p>
                                    <p class="text-[10px] text-slate-400 mt-1 leading-relaxed">No new alerts at this time.</p>
                                </div>
                                @endforelse
                                
                                <div class="mt-4 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-[10px] font-bold text-slate-400 hover:text-primary-500 transition-colors uppercase tracking-widest">View History</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="h-8 w-px bg-slate-200 mx-2"></div>
                    
                    <!-- Profile Menu Trigger -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 rounded-2xl p-1 pr-4 transition-all hover:bg-white hover:shadow-premium group active:scale-95">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white font-bold text-xs ring-4 ring-slate-100 transition-transform group-hover:rotate-6">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="hidden text-left lg:block">
                                <p class="text-[11px] font-bold text-slate-900 leading-none mb-0.5">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <p class="text-[9px] font-extrabold text-primary-500 uppercase tracking-tighter">System Core</p>
                            </div>
                        </button>

                        <div x-show="open" x-cloak 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 mt-3 w-56 rounded-2xl bg-white p-2 shadow-soft ring-1 ring-slate-100 z-50">
                            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[11px] font-black text-slate-900 uppercase tracking-tight transition-all hover:bg-slate-50">
                                <i class="fas fa-user-circle text-slate-400"></i>
                                Core Profile
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[11px] font-black text-slate-900 uppercase tracking-tight transition-all hover:bg-slate-50 text-slate-400">
                                <i class="fas fa-sliders-h"></i>
                                Preferences
                            </a>
                            <div class="my-2 h-px bg-slate-50"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-[11px] font-black text-rose-600 uppercase tracking-tight transition-all hover:bg-rose-50">
                                    <i class="fas fa-power-off"></i>
                                    Terminate Session
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto bg-surface-50 custom-scrollbar p-6 lg:p-10">
                <div class="animate-fade-in max-w-[1600px] mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('sidebar-nav');
            if (sidebar) {
                // Restore scroll position
                const scrollPos = sessionStorage.getItem('sidebarScrollPos');
                if (scrollPos) {
                    sidebar.scrollTop = scrollPos;
                }

                // Save scroll position before leaving page
                window.addEventListener('beforeunload', function() {
                    sessionStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
                });
            }
        });
    </script>
</body>
</html>
