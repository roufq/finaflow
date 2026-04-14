@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Net Worth Dashboard</h1>
        <a href="{{ route('dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
        </a>
    </div>

    <!-- Net Worth Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Net Worth</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($netWorth, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Assets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format(array_sum($assetBreakdown), 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Liabilities</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format(array_sum($liabilityBreakdown), 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Health Score</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $healthScore['percentage'] }}%</div>
                            <div class="text-xs">{{ $healthScore['grade'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heartbeat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset vs Liability Breakdown -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Asset Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="assetBreakdownChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($assetAllocation as $type => $data)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ getChartColor($loop->index) }}"></i> {{ $data['tags'] }}: {{ $data['percentage'] }}%
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Financial Health Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $debtToAssetRatio }}%</h4>
                                <small class="text-muted">Debt-to-Asset Ratio</small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar {{ $debtToAssetRatio < 40 ? 'bg-success' : ($debtToAssetRatio < 60 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width: {{ min($debtToAssetRatio, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-center">
                                <h4 class="text-success">{{ $savingsRate }}%</h4>
                                <small class="text-muted">Savings Rate</small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar {{ $savingsRate >= 20 ? 'bg-success' : ($savingsRate >= 10 ? 'bg-info' : 'bg-warning') }}"
                                         style="width: {{ min($savingsRate * 5, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="text-center">
                                <h4 class="text-info">{{ $emergencyFundRatio }}</h4>
                                <small class="text-muted">Emergency Fund (Months)</small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar {{ $emergencyFundRatio >= 6 ? 'bg-success' : ($emergencyFundRatio >= 3 ? 'bg-info' : 'bg-warning') }}"
                                         style="width: {{ min($emergencyFundRatio * 16.67, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-center">
                                <h4 class="text-warning">{{ $healthScore['score'] }}/100</h4>
                                <small class="text-muted">Health Score</small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar {{ $healthScore['percentage'] >= 80 ? 'bg-success' : ($healthScore['percentage'] >= 60 ? 'bg-info' : 'bg-warning') }}"
                                         style="width: {{ $healthScore['percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Worth History Chart -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Net Worth History</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="netWorthHistoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown Tables -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Assets</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td><strong>Cash & Accounts</strong></td>
                                    <td class="text-right">Rp {{ number_format($assetBreakdown['cash'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Investments</strong></td>
                                    <td class="text-right">Rp {{ number_format($assetBreakdown['investments'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Physical Assets</strong></td>
                                    <td class="text-right">Rp {{ number_format($assetBreakdown['physical_assets'], 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total Assets</strong></td>
                                    <td class="text-right"><strong>Rp {{ number_format(array_sum($assetBreakdown), 0, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Liabilities</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td><strong>Debts</strong></td>
                                    <td class="text-right">Rp {{ number_format($liabilityBreakdown['debts'], 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total Liabilities</strong></td>
                                    <td class="text-right"><strong>Rp {{ number_format(array_sum($liabilityBreakdown), 0, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Asset Breakdown Chart
var ctxAsset = document.getElementById("assetBreakdownChart");
var assetBreakdownChart = new Chart(ctxAsset, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($assetAllocation as $type => $data)
            "{{ $data['tags'] }}",
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
            hoverBackgroundColor: [
                @foreach($assetAllocation as $key => $value)
                '{{ getChartColor($loop->index) }}',
                @endforeach
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                tags: function(tooltipItem, data) {
                    var tags = data.labels[tooltipItem.index] || '';
                    var value = data.datasets[0].data[tooltipItem.index];
                    return tags + ': Rp ' + value.toLocaleString('id-ID');
                }
            }
        },
        legend: {
            display: false
        },
        cutoutPercentage: 80,
    },
});

// Net Worth History Chart
var ctxNetWorth = document.getElementById("netWorthHistoryChart");
var netWorthHistoryChart = new Chart(ctxNetWorth, {
    type: 'line',
    data: {
        labels: [
            @foreach($netWorthHistory as $month)
            "{{ $month['month'] }}",
            @endforeach
        ],
        datasets: [{
            tags: "Net Worth",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [
                @foreach($netWorthHistory as $month)
                {{ $month['net_worth'] }},
                @endforeach
            ],
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                tags: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].tags || '';
                    return datasetLabel + ': Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                }
            }
        }
    }
});
</script>
@endsection

@php
function getChartColor($index) {
    $colors = [
        '#4e73df', // Primary blue
        '#1cc88a', // Success green
        '#36b9cc', // Info cyan
        '#f6c23e', // Warning yellow
        '#e74a3b', // Danger red
        '#6f42c1', // Purple
        '#fd7e14', // Orange
        '#20c997', // Teal
    ];
    return $colors[$index % count($colors)];
}
@endphp
