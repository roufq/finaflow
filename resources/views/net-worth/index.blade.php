@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Net Worth Dashboard</h1>
            <p class="text-sm font-medium text-slate-500">Comprehensive overview of your financial equity</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back to Dashboard
            </a>
            <button class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Add Asset/Debt
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Net Worth Card -->
        <div class="group relative overflow-hidden rounded-2xl bg-slate-900 p-6 text-white shadow-soft transition-all ring-1 ring-white/10 hover:shadow-premium">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Net Worth</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-primary-400">
                    <i class="fas fa-wallet text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold tracking-tight">Rp {{ number_format($netWorth, 0, ',', '.') }}</h3>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-tighter text-slate-400">Calculated Real-time</span>
                </div>
            </div>
        </div>

        <!-- Total Assets -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Assets</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <i class="fas fa-plus-circle text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format(array_sum($assetBreakdown), 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Cash, Investments, & Physical</p>
            </div>
        </div>

        <!-- Total Liabilities -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Liabilities</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i class="fas fa-minus-circle text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format(array_sum($liabilityBreakdown), 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">All outstanding debts</p>
            </div>
        </div>

        <!-- Health Score Card -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Health Score</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                    <i class="fas fa-heartbeat text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1">
                    <h3 class="text-2xl font-bold text-slate-900">{{ $healthScore['percentage'] }}%</h3>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">{{ $healthScore['grade'] }}</span>
                </div>
                <div class="mt-2 h-1.5 w-full rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-primary-500" style="width: {{ $healthScore['percentage'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Asset Breakdown Chart -->
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-premium">
            <div class="mb-6 flex items-center justify-between border-b border-slate-50 pb-4">
                <h2 class="text-lg font-bold text-slate-900">Asset Allocation</h2>
                <span class="text-xs font-semibold text-slate-400">By Type</span>
            </div>
            <div class="flex flex-col items-center justify-center sm:flex-row gap-8">
                <div class="relative flex h-56 w-56 items-center justify-center shrink-0">
                    <canvas id="assetBreakdownChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Assets</span>
                        <span class="text-sm font-extrabold text-slate-900">
                            {{ number_format(array_sum($assetBreakdown) / 1000000, 1) }}M
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 w-full">
                    @foreach($assetAllocation as $type => $data)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full" style="background-color: {{ getChartColor($loop->index) }}"></div>
                            <span class="text-xs font-bold text-slate-600">{{ $data['label'] }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs font-extrabold text-slate-900">Rp {{ number_format($data['amount'], 0, ',', '.') }}</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ $data['percentage'] }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Financial Health Metrics -->
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-premium">
            <div class="mb-6 flex items-center justify-between border-b border-slate-50 pb-4">
                <h2 class="text-lg font-bold text-slate-900">Health Indicators</h2>
                <span class="text-xs font-semibold text-slate-400">Compliance & Ratios</span>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <!-- Ratio 1 -->
                <div class="p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Debt-to-Asset</p>
                    <h4 class="text-xl font-bold text-slate-900">{{ number_format($debtToAssetRatio, 1) }}%</h4>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-white shadow-inner">
                        <div class="h-full rounded-full {{ $debtToAssetRatio < 40 ? 'bg-green-500' : ($debtToAssetRatio < 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                             style="width: {{ min($debtToAssetRatio, 100) }}%"></div>
                    </div>
                </div>
                <!-- Ratio 2 -->
                <div class="p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Savings Rate</p>
                    <h4 class="text-xl font-bold text-slate-900">{{ number_format($savingsRate, 1) }}%</h4>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-white shadow-inner">
                        <div class="h-full rounded-full {{ $savingsRate >= 20 ? 'bg-green-500' : ($savingsRate >= 10 ? 'bg-blue-500' : 'bg-amber-500') }}"
                             style="width: {{ min($savingsRate * 5, 100) }}%"></div>
                    </div>
                </div>
                <!-- Ratio 3 -->
                <div class="p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Emergency Fund</p>
                    <h4 class="text-xl font-bold text-slate-900">{{ $emergencyFundRatio }} <span class="text-xs text-slate-400">mo</span></h4>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-white shadow-inner">
                        <div class="h-full rounded-full {{ $emergencyFundRatio >= 6 ? 'bg-green-500' : ($emergencyFundRatio >= 3 ? 'bg-blue-500' : 'bg-amber-500') }}"
                             style="width: {{ min($emergencyFundRatio * 16.67, 100) }}%"></div>
                    </div>
                </div>
                <!-- Ratio 4 -->
                <div class="p-4 rounded-2xl bg-slate-900 border border-white/5 shadow-soft">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Aggregate Score</p>
                    <h4 class="text-xl font-bold text-white">{{ $healthScore['score'] }}<span class="text-xs text-slate-500">/100</span></h4>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-white/10">
                        <div class="h-full rounded-full bg-primary-500" style="width: {{ $healthScore['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
            <div class="mt-6 p-4 rounded-xl bg-primary-50/30 border border-primary-100/50">
                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-primary-900">Optimization Tip</p>
                        <p class="text-[11px] text-primary-700 leading-relaxed mt-0.5">
                            @if($debtToAssetRatio > 40)
                                Prioritize high-interest debt repayment to improve your Health Score. Focus on accounts with APR above 15%.
                            @elseif($savingsRate < 20)
                                Try increasing your automated savings by 2% next month to hit the "Elite" target.
                            @else
                                Your financial resilience is exceptional. Consider diversifying into higher-yield assets.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Chart -->
    <div class="rounded-2xl bg-white p-6 shadow-premium">
        <div class="mb-6 flex items-center justify-between border-b border-slate-50 pb-4">
            <h2 class="text-lg font-bold text-slate-900">Net Worth Growth</h2>
            <div class="flex gap-2">
                <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-[10px] font-bold text-primary-600">12 Months Timeline</span>
            </div>
        </div>
        <div class="h-80 w-full">
            <canvas id="netWorthHistoryChart"></canvas>
        </div>
    </div>

    <!-- Detailed Breakdown Tables -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Assets List -->
        <div class="flex flex-col rounded-2xl bg-white shadow-premium overflow-hidden border border-slate-50">
            <div class="p-5 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Asset Details</h2>
                <i class="fas fa-shield-alt text-green-500"></i>
            </div>
            <div class="divide-y divide-slate-100">
                <div class="flex items-center justify-between p-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-500">
                            <i class="fas fa-university text-xs"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700">Cash & Bank Accounts</span>
                    </div>
                    <span class="text-sm font-extrabold text-slate-900">Rp {{ number_format($assetBreakdown['cash'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between p-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 text-purple-500">
                            <i class="fas fa-chart-pie text-xs"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700">Investment Portfolio</span>
                    </div>
                    <span class="text-sm font-extrabold text-slate-900">Rp {{ number_format($assetBreakdown['investments'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between p-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-50 text-orange-500">
                            <i class="fas fa-home text-xs"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700">Physical & Other Assets</span>
                    </div>
                    <span class="text-sm font-extrabold text-slate-900">Rp {{ number_format($assetBreakdown['physical_assets'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between p-5 bg-slate-900">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Asset Liquidity</span>
                    <span class="text-lg font-black text-white">Rp {{ number_format(array_sum($assetBreakdown), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Liabilities List -->
        <div class="flex flex-col rounded-2xl bg-white shadow-premium overflow-hidden border border-slate-50">
            <div class="p-5 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Liability Details</h2>
                <i class="fas fa-exclamation-triangle text-amber-500"></i>
            </div>
            <div class="divide-y divide-slate-100">
                <div class="flex items-center justify-between p-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-500">
                            <i class="fas fa-credit-card text-xs"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700">Total Outstanding Debts</span>
                    </div>
                    <span class="text-sm font-extrabold text-slate-900">Rp {{ number_format($liabilityBreakdown['debts'], 0, ',', '.') }}</span>
                </div>
                <!-- Placeholder for future more granular liability tracking -->
                <div class="flex items-center justify-center p-12 text-slate-300 italic opacity-50">
                    <p class="text-xs">No further liability breakdowns available at this tier.</p>
                </div>
                <div class="mt-auto flex items-center justify-between p-5 bg-slate-900">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Liability Exposure</span>
                    <span class="text-lg font-black text-white">Rp {{ number_format(array_sum($liabilityBreakdown), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Asset Breakdown Chart
var ctxAsset = document.getElementById("assetBreakdownChart").getContext('2d');
var assetBreakdownChart = new Chart(ctxAsset, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($assetAllocation as $type => $data)
            "{{ $data['label'] }}",
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($assetAllocation as $type => $data)
                {{ $data['amount'] }},
                @endforeach
            ],
            backgroundColor: [
                @foreach($assetAllocation as $key => $value)
                '{{ getChartColor($loop->index) }}',
                @endforeach
            ],
            borderWidth: 0,
            weight: 0.1
        }],
    },
    options: {
        maintainAspectRatio: false,
        cutoutPercentage: 88,
        legend: { display: false },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#1e293b",
            titleMarginBottom: 10,
            titleFontColor: '#1e293b',
            titleFontSize: 12,
            titleFontStyle: 'bold',
            borderColor: '#e2e8f0',
            borderWidth: 1,
            xPadding: 12,
            yPadding: 12,
            displayColors: true,
            callbacks: {
                label: function(tooltipItem, data) {
                    var label = data.labels[tooltipItem.index] || '';
                    var value = data.datasets[0].data[tooltipItem.index];
                    return ' ' + label + ': Rp ' + value.toLocaleString('id-ID');
                }
            }
        }
    },
});

// Net Worth History Chart
var ctxNetWorth = document.getElementById("netWorthHistoryChart").getContext('2d');
var netWorthHistoryChart = new Chart(ctxNetWorth, {
    type: 'line',
    data: {
        labels: [
            @foreach($netWorthHistory as $month)
            "{{ $month['month'] }}",
            @endforeach
        ],
        datasets: [{
            label: "Net Worth",
            lineTension: 0.4,
            backgroundColor: (context) => {
                const chart = context.chart;
                const {ctx, chartArea} = chart;
                if (!chartArea) return null;
                const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');
                return gradient;
            },
            borderColor: "#3b82f6",
            borderWidth: 3,
            pointRadius: 4,
            pointBackgroundColor: "#fff",
            pointBorderColor: "#3b82f6",
            pointBorderWidth: 2,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: "#3b82f6",
            pointHoverBorderColor: "#fff",
            pointHoverBorderWidth: 2,
            data: [
                @foreach($netWorthHistory as $month)
                {{ $month['net_worth'] }},
                @endforeach
            ],
        }],
    },
    options: {
        maintainAspectRatio: false,
        legend: { display: false },
        scales: {
            xAxes: [{
                gridLines: { display: false, drawBorder: false },
                ticks: { fontSize: 10, fontColor: '#94a3b8', maxTicksLimit: 7, padding: 10 }
            }],
            yAxes: [{
                gridLines: { color: "#f1f5f9", drawBorder: false, borderDash: [5, 5] },
                ticks: {
                    fontSize: 10,
                    fontColor: '#94a3b8',
                    padding: 10,
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                    }
                }
            }],
        },
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
            displayColors: false,
            intersect: false,
            mode: 'index',
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return ' ' + datasetLabel + ': Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                }
            }
        }
    }
});
</script>

@php
function getChartColor($index) {
    $colors = [
        '#3b82f6', // blue-500
        '#10b981', // emerald-500
        '#8b5cf6', // violet-500
        '#f59e0b', // amber-500
        '#ef4444', // red-500
        '#06b6d4', // cyan-500
        '#f43f5e', // rose-500
    ];
    return $colors[$index % count($colors)];
}
@endphp
@endpush
@endsection
