@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Advanced Analytics &amp; Insights</h1>
        <a href="{{ route('dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Expense (This Month)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($currentMonthExpense, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
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
                                Income (This Month)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($currentMonthIncome, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                                Savings Rate (6-mo avg)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($healthMetrics['savings_rate'], 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
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
                                Debt-to-Income Ratio</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($healthMetrics['debt_to_income'], 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Spending (Last 30 Days)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailySpendingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Seasonal Spending Insights</h6>
                </div>
                <div class="card-body">
                    @if($seasonalInsights['highest'])
                        <p><strong>Highest month:</strong> {{ $seasonalInsights['highest']['label'] }} (Rp {{ number_format($seasonalInsights['highest']['total'], 0, ',', '.') }})</p>
                        <p><strong>Lowest month:</strong> {{ $seasonalInsights['lowest']['label'] }} (Rp {{ number_format($seasonalInsights['lowest']['total'], 0, ',', '.') }})</p>
                        <p><strong>Average monthly spending:</strong> Rp {{ number_format($seasonalInsights['average'], 0, ',', '.') }}</p>
                        <p><strong>Trend:</strong>
                            @if($seasonalInsights['trend'] === 'increasing')
                                Spending is increasing compared to earlier months.
                            @elseif($seasonalInsights['trend'] === 'decreasing')
                                Spending is decreasing compared to earlier months.
                            @else
                                Spending is relatively stable over the last year.
                            @endif
                        </p>
                    @else
                        <p>Not enough data to analyze seasonal patterns yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Weekly Spending (Last 12 Weeks)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="weeklySpendingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Spending (Last 12 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlySpendingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Peer Comparison (Anonymous)</h6>
                </div>
                <div class="card-body">
                    <p><strong>Your average monthly spending (6 months):</strong>
                        Rp {{ number_format($peerComparison['user_average'], 0, ',', '.') }}</p>
                    <p><strong>Peer average monthly spending:</strong>
                        Rp {{ number_format($peerComparison['peer_average'], 0, ',', '.') }}</p>
                    <p><strong>Difference:</strong>
                        {{ $peerComparison['difference_percentage'] >= 0 ? '+' : '' }}{{ $peerComparison['difference_percentage'] }}%</p>
                    <p>
                        @if($peerComparison['relative'] === 'above')
                            You spend more than peers on average. Review major spending categories to identify optimization opportunities.
                        @elseif($peerComparison['relative'] === 'below')
                            You spend less than peers on average. Ensure you are not under-investing in important areas (health, education, etc.).
                        @else
                            Your spending is similar to peers. Focus on aligning spending with your personal goals and values.
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">AI-inspired Spending Predictions</h6>
                </div>
                <div class="card-body">
                    @if(count($spendingPredictions))
                        <p>Based on your last 12 months of spending, the following are simple trend-based projections for the next 3 months:</p>
                        <ul>
                            @foreach($spendingPredictions as $prediction)
                                <li>{{ $prediction['label'] }}: Rp {{ number_format($prediction['total'], 0, ',', '.') }}</li>
                            @endforeach
                        </ul>
                        <p class="text-muted mb-0">
                            These are statistical projections (not official tax or financial advice). Use them as guidance to plan budgets and cash flow.
                        </p>
                    @else
                        <p>Not enough historical data to generate predictions yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Financial Health Metrics</h6>
                </div>
                <div class="card-body">
                    <p><strong>Savings rate (6-mo avg):</strong>
                        {{ number_format($healthMetrics['savings_rate'], 1) }}%</p>
                    <p><strong>Debt-to-income ratio:</strong>
                        {{ number_format($healthMetrics['debt_to_income'], 1) }}%</p>
                    <p><strong>Emergency fund coverage:</strong>
                        {{ number_format($healthMetrics['emergency_fund_ratio'], 1) }} months of expenses</p>
                    <p><strong>Credit score (self-reported):</strong>
                        @if($healthMetrics['credit_score'])
                            {{ $healthMetrics['credit_score'] }}
                        @else
                            Not set. Update your credit score in Settings to improve the analysis.
                        @endif
                    </p>
                    <p><strong>Risk profile:</strong>
                        {{ ucfirst($riskProfile) }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personalized Financial Advice</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($advice as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tax Optimization Overview ({{ $taxInsights['year'] }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Total income (year-to-date):</strong>
                                Rp {{ number_format($taxInsights['total_income'], 0, ',', '.') }}</p>
                            <p><strong>Total expenses (year-to-date):</strong>
                                Rp {{ number_format($taxInsights['total_expense'], 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Estimated deductible expenses:</strong>
                                Rp {{ number_format($taxInsights['deductible_expenses'], 0, ',', '.') }}</p>
                            <p><strong>Estimated taxable income:</strong>
                                Rp {{ number_format($taxInsights['taxable_income'], 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            @if($taxInsights['current_bracket'])
                                <p><strong>Current bracket (rough):</strong>
                                    {{ $taxInsights['current_bracket']['rate'] }}%
                                    @if($taxInsights['current_bracket']['limit'])
                                        up to Rp {{ number_format($taxInsights['current_bracket']['limit'], 0, ',', '.') }}
                                    @else
                                        (no upper limit)
                                    @endif
                                </p>
                                <p><strong>Simple estimated tax (flat on taxable income):</strong>
                                    Rp {{ number_format($taxInsights['estimated_tax'], 0, ',', '.') }}</p>
                                @if($taxInsights['next_bracket'] && $taxInsights['current_bracket']['limit'])
                                    <p>
                                        To reach the next bracket ({{ $taxInsights['next_bracket']['rate'] }}%),
                                        taxable income would need to exceed
                                        Rp {{ number_format($taxInsights['current_bracket']['limit'], 0, ',', '.') }}.
                                    </p>
                                @endif
                            @else
                                <p>Not enough data to estimate your tax bracket.</p>
                            @endif
                        </div>
                    </div>
                    <p class="text-muted mb-0">
                        This section provides high-level tax optimization insights based on your categorized transactions.
                        Always confirm details with a tax professional and keep supporting documents for deductible expenses.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Daily Spending Chart
    var ctxDaily = document.getElementById("dailySpendingChart");
    var dailySpendingChart = new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySpending['labels']) !!},
            datasets: [{
                label: "Daily Expense",
                lineTension: 0.3,
                backgroundColor: "rgba(231, 74, 59, 0.05)",
                borderColor: "rgba(231, 74, 59, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(231, 74, 59, 1)",
                pointBorderColor: "rgba(231, 74, 59, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(231, 74, 59, 1)",
                pointHoverBorderColor: "rgba(231, 74, 59, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: {!! json_encode($dailySpending['values']) !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 10
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
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
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                    }
                }
            }
        }
    });

    // Weekly Spending Chart
    var ctxWeekly = document.getElementById("weeklySpendingChart");
    var weeklySpendingChart = new Chart(ctxWeekly, {
        type: 'bar',
        data: {
            labels: {!! json_encode($weeklySpending['labels']) !!},
            datasets: [{
                label: "Weekly Expense",
                backgroundColor: "rgba(78, 115, 223, 0.8)",
                borderColor: "rgba(78, 115, 223, 1)",
                data: {!! json_encode($weeklySpending['values']) !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
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
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                    }
                }
            }
        }
    });

    // Monthly Spending Chart
    var ctxMonthly = document.getElementById("monthlySpendingChart");
    var monthlySpendingChart = new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlySpending, 'label')) !!},
            datasets: [{
                label: "Monthly Expense",
                lineTension: 0.3,
                backgroundColor: "rgba(54, 185, 204, 0.05)",
                borderColor: "rgba(54, 185, 204, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(54, 185, 204, 1)",
                pointBorderColor: "rgba(54, 185, 204, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(54, 185, 204, 1)",
                pointHoverBorderColor: "rgba(54, 185, 204, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: {!! json_encode(array_column($monthlySpending, 'total')) !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 12
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
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
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                    }
                }
            }
        }
    });
</script>
@endsection

