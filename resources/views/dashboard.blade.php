@extends('layouts.app')

@php
    $userName = explode(' ', Auth::user()->name)[0];
    $currentDate = now()->format('M d, Y');
@endphp

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Welcome back, {{ $userName }}!</h1>
            <p class="text-sm font-medium text-slate-500">Overview for {{ $currentDate }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-download mr-2 text-slate-400"></i>
                Export Report
            </button>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                New Transaction
            </a>
        </div>
    </div>

    <!-- 2FA Suggestion Alert -->
    @if (! Auth::user()->hasValidTwoFactorSecret())
    <div class="relative overflow-hidden rounded-2xl bg-amber-50 border border-amber-100 p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-amber-500 shadow-sm ring-1 ring-amber-100">
                <i class="fas fa-shield-alt text-xl"></i>
            </div>
            <div class="flex-1">
                <h6 class="text-base font-bold text-amber-900">Security Recommendation: Enable 2FA</h6>
                <p class="text-sm text-amber-700 opacity-80">Protect your financial data with an extra layer of security. We highly recommend activating Two-Factor Authentication.</p>
            </div>
            <a href="{{ route('twofactor.setup') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-amber-700">
                Set up 2FA
            </a>
        </div>
    </div>
    @endif

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Net Worth -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Net Worth</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900 leading-tight">
                    {{ $currencySymbol }} {{ number_format($totalCash, 0, ',', '.') }}
                </h3>
                <div class="mt-1 flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-bold text-green-600">
                        {{ $netWorthTrend >= 0 ? '+' : '' }}{{ number_format($netWorthTrend, 1) }}%
                    </span>
                    <span class="text-xs text-slate-400">vs last month</span>
                </div>
            </div>
            <div class="mt-6 h-12 w-full">
                <canvas id="netWorthSparkline"></canvas>
            </div>
        </div>

        <!-- Cash Inflow -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Cash Inflow</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <i class="fas fa-arrow-down text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">
                    {{ $currencySymbol }} {{ number_format($totalIncome, 0, ',', '.') }}
                </h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Total monthly income</p>
            </div>
        </div>

        <!-- Cash Outflow -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Outflow</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600">
                    <i class="fas fa-arrow-up text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">
                    {{ $currencySymbol }} {{ number_format($totalExpense, 0, ',', '.') }}
                </h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Monthly expenditures</p>
            </div>
        </div>

        <!-- Financial Health Score -->
        <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft transition-all ring-1 ring-white/10">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Health Score</span>
                <i class="fas fa-heartbeat text-primary-400"></i>
            </div>
            <div class="mt-4 flex flex-col items-center">
                <div class="text-4xl font-extrabold tracking-tight">84<span class="text-lg text-primary-400">/100</span></div>
                <div class="mt-3 w-full rounded-full bg-white/10 p-1">
                    <div class="h-1.5 rounded-full bg-primary-500" style="width: 84%"></div>
                </div>
                <p class="mt-3 text-[10px] font-bold uppercase tracking-tighter text-slate-400 text-center">Your financial health is stable</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Expenses Breakdown -->
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-premium">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Expense Distribution</h2>
                <span class="text-xs font-semibold text-slate-400">Last 30 Days</span>
            </div>
            <div class="flex flex-1 flex-col items-center justify-center sm:flex-row">
                <div class="relative flex h-48 w-48 items-center justify-center">
                    <canvas id="expensesDonutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Spent</span>
                        <span class="text-sm font-extrabold text-slate-900 leading-none">
                            {{ $currencySymbol }} {{ number_format($totalExpense, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div id="expensesLegend" class="mt-6 flex flex-1 flex-col gap-2 sm:mt-0 sm:ml-8">
                    <!-- Legend generated by JS -->
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="flex flex-col rounded-2xl bg-white shadow-premium overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Recent Transactions</h2>
                <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-primary-600 hover:text-primary-700">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentTransactions->take(5) as $transaction)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $transaction->type == 'expense' ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500' }}">
                                        <i class="fas {{ $transaction->type == 'expense' ? 'fa-minus' : 'fa-plus' }} text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-900">{{ $transaction->description }}</p>
                                        <p class="text-xs text-slate-400 font-medium">{{ $transaction->category->name ?? 'Uncategorized' }} • {{ $transaction->transaction_date->format('M d') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-right">
                                <span class="text-sm font-extrabold {{ $transaction->type == 'expense' ? 'text-slate-900' : 'text-green-600' }}">
                                    {{ $transaction->type == 'expense' ? '-' : '+' }}
                                    {{ $currencySymbol }} {{ number_format($transaction->amount, 0, ',', '.') }}
                                </span>
                                <p class="text-[10px] font-bold text-slate-300 uppercase">{{ $transaction->account->name ?? '-' }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="p-10 text-center text-slate-400 italic">No recent transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    // Net Worth Sparkline
    const ctxSpark = document.getElementById('netWorthSparkline').getContext('2d');
    new Chart(ctxSpark, {
        type: 'line',
        data: {
            labels: Array.from({length: 12}, (_, i) => i + 1),
            datasets: [{
                data: [120, 115, 125, 130, 128, 140, 145, 138, 150, 160, 155, 165],
                borderColor: '#3b82f6',
                borderWidth: 2,
                fill: true,
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return null;
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0)');
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.08)');
                    return gradient;
                },
                pointRadius: 0,
                tension: 0.4
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                xAxes: [{ display: false }],
                yAxes: [{ display: false }]
            },
            tooltips: { enabled: false }
        }
    });

    // Expenses Donut Chart
    const ctxDonut = document.getElementById('expensesDonutChart').getContext('2d');
    const expenseData = @json($expenseCategories);
    
    const chartData = {
        labels: expenseData.map(d => d.name),
        datasets: [{
            data: expenseData.map(d => d.total),
            backgroundColor: [
                '#3b82f6', '#818cf8', '#60a5fa', '#f87171', '#fb923c', '#fbbf24', '#2dd4bf'
            ],
            hoverBackgroundColor: [
                '#2563eb', '#6366f1', '#3b82f6', '#ef4444', '#f97316', '#f59e0b', '#14b8a6'
            ],
            borderWidth: 0,
            weight: 0.5
        }]
    };

    new Chart(ctxDonut, {
        type: 'doughnut',
        data: chartData,
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 82,
            legend: { display: false },
            animation: { animateScale: true, animateRotate: true }
        }
    });

    // Premium Legend Generation
    const legendContainer = document.getElementById('expensesLegend');
    const total = expenseData.reduce((acc, curr) => acc + parseFloat(curr.total), 0);
    
    expenseData.slice(0, 5).forEach((item, index) => {
        const percent = total > 0 ? ((item.total / total) * 100).toFixed(0) : 0;
        const color = chartData.datasets[0].backgroundColor[index % chartData.datasets[0].backgroundColor.length];
        
        const legendItem = document.createElement('div');
        legendItem.className = 'flex items-center justify-between group cursor-default';
        legendItem.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="h-2 w-2 rounded-full transition-transform group-hover:scale-125" style="background-color: ${color}"></div>
                <span class="text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors">${item.name}</span>
            </div>
            <span class="text-xs font-extrabold text-slate-900">${percent}%</span>
        `;
        legendContainer.appendChild(legendItem);
    });
</script>
@endpush
