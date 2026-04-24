@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Advanced Analytics & Insights</h1>
            <p class="text-sm font-medium text-slate-500">Deep-dive into your spending patterns and financial behaviors</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('insights.index') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-magic mr-2"></i>
                <span>Generate AI Report</span>
            </a>
        </div>
    </div>

    <!-- Analytics Summary Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Expense Month -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Expense (This Month)</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600">
                    <i class="fas fa-wallet text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ $currencySymbol }} {{ number_format($currentMonthExpense, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">All categorized transactions</p>
            </div>
        </div>

        <!-- Income Month -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Income (This Month)</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <i class="fas fa-coins text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ $currencySymbol }} {{ number_format($currentMonthIncome, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Total monthly earnings</p>
            </div>
        </div>

        <!-- Savings Rate -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Savings Rate (6-mo)</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <i class="fas fa-piggy-bank text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($healthMetrics['savings_rate'], 1) }}%</h3>
                <div class="mt-2 h-1.5 w-full rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-blue-500" style="width: {{ min($healthMetrics['savings_rate'] * 4, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Debt Ratio -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Debt-to-Income</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i class="fas fa-balance-scale text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($healthMetrics['debt_to_income'], 1) }}%</h3>
                <div class="mt-2 h-1.5 w-full rounded-full bg-slate-100">
                    <div class="h-full rounded-full {{ $healthMetrics['debt_to_income'] < 30 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min($healthMetrics['debt_to_income'], 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Daily Spending Area -->
        <div class="lg:col-span-8 flex flex-col rounded-2xl bg-white p-6 shadow-premium">
            <div class="mb-6 flex items-center justify-between border-b border-slate-50 pb-4">
                <h2 class="text-lg font-bold text-slate-900">Daily Spending Pulse</h2>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Last 30 Days</span>
            </div>
            <div class="h-80 w-full">
                <canvas id="dailySpendingChart"></canvas>
            </div>
        </div>

        <!-- Seasonal Insights -->
        <div class="lg:col-span-4 flex flex-col rounded-2xl bg-slate-900 p-6 text-white shadow-soft">
            <div class="mb-6 flex items-center justify-between border-b border-white/10 pb-4">
                <h2 class="text-lg font-bold">Seasonal Intelligence</h2>
                <i class="fas fa-snowflake text-blue-400"></i>
            </div>
            <div class="space-y-6">
                @if($seasonalInsights['highest'])
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 text-primary-400 ring-1 ring-white/10">
                            <i class="fas fa-arrow-up text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Peak Month</p>
                            <p class="text-sm font-bold">{{ $seasonalInsights['highest']['label'] }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $currencySymbol }} {{ number_format($seasonalInsights['highest']['total'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 text-green-400 ring-1 ring-white/10">
                            <i class="fas fa-arrow-down text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Lowest Month</p>
                            <p class="text-sm font-bold">{{ $seasonalInsights['lowest']['label'] }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $currencySymbol }} {{ number_format($seasonalInsights['lowest']['total'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Growth Trend</span>
                        <span class="inline-flex items-center rounded-full bg-blue-500/10 px-2 py-0.5 text-[10px] font-bold text-blue-400">
                            AI Analyzed
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mt-4">
                        @if($seasonalInsights['trend'] === 'increasing')
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/20 text-red-400">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <p class="text-xs font-medium text-slate-300">Spending is trending <span class="text-red-400 font-bold">upward</span>. Review your discretionary buckets.</p>
                        @elseif($seasonalInsights['trend'] === 'decreasing')
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-500/20 text-green-400">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <p class="text-xs font-medium text-slate-300">Spending is trending <span class="text-green-400 font-bold">downward</span>. Great consistency!</p>
                        @else
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400">
                                <i class="fas fa-equals"></i>
                            </div>
                            <p class="text-xs font-medium text-slate-300">Your spending velocity is currently <span class="text-blue-400 font-bold">stable</span>.</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Weekly Spending Bar -->
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-premium">
            <div class="mb-6 flex items-center justify-between border-b border-slate-50 pb-4">
                <h2 class="text-lg font-bold text-slate-900">Weekly Velocity</h2>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Last 4 Weeks</span>
            </div>
            <div class="h-80 w-full">
                <canvas id="weeklySpendingChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-premium">
            <div class="mb-6 flex items-center justify-between border-b border-slate-50 pb-4">
                <h2 class="text-lg font-bold text-slate-900">Historical Comparison</h2>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">12 Month View</span>
            </div>
            <div class="h-80 w-full">
                <canvas id="monthlySpendingChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Predictions & Tips -->
        <div class="lg:col-span-7 rounded-2xl bg-white p-8 shadow-premium border border-slate-100">
            <div class="flex items-center gap-4 mb-8">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-premium">
                    <i class="fas fa-brain"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tabular-nums">Spending Forecasts</h2>
                    <p class="text-xs font-medium text-slate-400">Predictive insights based on historical volatility</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                @foreach($spendingPredictions as $prediction)
                <div class="p-4 rounded-xl bg-slate-50/50 border border-slate-100 flex flex-col group hover:bg-white hover:shadow-soft transition-all">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">{{ $prediction['label'] }}</span>
                    <span class="text-lg font-black text-slate-900 tracking-tight">{{ $currencySymbol }} {{ number_format($prediction['total'], 0, ',', '.') }}</span>
                    <div class="mt-2 h-1 w-full rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-primary-500 w-1/3"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="space-y-4">
                <h6 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Strategic Action Items</h6>
                @foreach($actionableTips as $tip)
                <div class="flex items-start gap-4 p-4 rounded-xl border border-dashed border-slate-200 hover:border-primary-300 hover:bg-primary-50/30 transition-all cursor-default">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 line-clamp-1">{{ $tip['title'] }}</p>
                        <p class="text-xs text-slate-500 leading-relaxed mt-1">{{ $tip['tip'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Health Metrics Radar/List -->
        <div class="lg:col-span-5 flex flex-col space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-premium border border-slate-100 flex-1">
                <h2 class="text-lg font-bold text-slate-900 mb-6">Financial Efficiency</h2>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Emergency Runway</span>
                            <span class="text-xs font-bold text-slate-900">{{ number_format($healthMetrics['emergency_fund_ratio'], 1) }} Months</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-green-500" style="width: {{ min($healthMetrics['emergency_fund_ratio'] * 16, 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Discretionary Spending</span>
                            <span class="text-xs font-bold text-slate-900">{{ number_format($discretionaryRatio, 1) }}%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ $discretionaryRatio }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Utility Persistence</span>
                            <span class="text-xs font-bold text-slate-900">82%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-primary-500" style="width: 82%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-primary-600 p-6 text-white shadow-premium">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary-200">Personalized Goal</span>
                    <i class="fas fa-trophy text-primary-300"></i>
                </div>
                <h3 class="text-xl font-bold leading-tight">Reduce monthly subscriptions by 15% next month.</h3>
                <p class="text-xs text-primary-200 mt-2 opacity-80">This could save you up to {{ $currencySymbol }} 450,000 annually.</p>
                <button class="mt-6 w-full rounded-xl bg-white px-4 py-2 text-sm font-bold text-primary-600 shadow-sm transition-all hover:bg-slate-50">
                    Create Automation Task
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    // Configuration shared for all charts
    const commonOptions = {
        maintainAspectRatio: false,
        legend: { display: false },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#1e293b",
            titleFontColor: '#1e293b',
            titleFontSize: 13,
            titleFontStyle: 'bold',
            borderColor: '#e2e8f0',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: true,
            intersect: false,
            mode: 'index'
        }
    };

    // 1. Daily Spending Chart
    var ctxDaily = document.getElementById("dailySpendingChart").getContext('2d');
    new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySpending['labels']) !!},
            datasets: [{
                label: "Daily Amount",
                lineTension: 0.4,
                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                borderColor: "#3b82f6",
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 6,
                data: {!! json_encode($dailySpending['values']) !!}
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                xAxes: [{ gridLines: { display: false }, ticks: { fontSize: 10, fontColor: '#94a3b8', maxTicksLimit: 10 } }],
                yAxes: [{ gridLines: { color: "#f1f5f9", drawBorder: false }, ticks: { fontSize: 10, fontColor: '#94a3b8', padding: 10 } }]
            }
        }
    });

    // 2. Weekly Spending Chart
    var ctxWeekly = document.getElementById("weeklySpendingChart").getContext('2d');
    new Chart(ctxWeekly, {
        type: 'bar',
        data: {
            labels: {!! json_encode($weeklySpending['labels']) !!},
            datasets: [{
                label: "Weekly Total",
                backgroundColor: "rgba(54, 185, 204, 0.8)",
                hoverBackgroundColor: "rgba(54, 185, 204, 1)",
                borderColor: "transparent",
                borderRadius: 8,
                data: {!! json_encode($weeklySpending['values']) !!}
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                xAxes: [{ gridLines: { display: false }, ticks: { fontSize: 10, fontColor: '#94a3b8' } }],
                yAxes: [{ gridLines: { color: "#f1f5f9", drawBorder: false }, ticks: { fontSize: 10, fontColor: '#94a3b8', padding: 10 } }]
            }
        }
    });

    // 3. Monthly Trend Chart
    var ctxMonthly = document.getElementById("monthlySpendingChart").getContext('2d');
    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlySpending, 'label')) !!},
            datasets: [{
                label: "Monthly Total",
                fill: true,
                lineTension: 0.4,
                backgroundColor: 'rgba(99, 102, 241, 0.05)',
                borderColor: "#6366f1",
                borderWidth: 3,
                pointRadius: 4,
                data: {!! json_encode(array_column($monthlySpending, 'total')) !!}
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                xAxes: [{ gridLines: { display: false }, ticks: { fontSize: 10, fontColor: '#94a3b8' } }],
                yAxes: [{ gridLines: { color: "#f1f5f9", drawBorder: false }, ticks: { fontSize: 10, fontColor: '#94a3b8', padding: 10 } }]
            }
        }
    });
</script>
@endpush
@endsection
