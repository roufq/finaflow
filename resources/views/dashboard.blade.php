@extends('layouts.app')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('dashboard.title') }}</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
            class="fas fa-download fa-sm text-white-50"></i> {{ __('dashboard.generate_report') }}</a>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Income Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            {{ __('dashboard.total_income_monthly') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Expense Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            {{ __('dashboard.total_expense_monthly') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Balance Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            {{ __('dashboard.net_balance_monthly') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($netBalance, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Savings Rate Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ __('dashboard.savings_rate') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($totalIncome > 0)
                                {{ number_format(($netBalance / $totalIncome) * 100, 1) }}%
                            @else
                                0.0%
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cash Flow Management Section -->
<div class="row">
    <!-- Burn Rate Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            {{ __('dashboard.burn_rate_monthly') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($burnRate['average_monthly'], 0, ',', '.') }}</div>
                        <div class="text-xs text-muted">
                            {{ __('dashboard.trend') }}: <span class="text-{{ $burnRate['trend'] === 'increasing' ? 'danger' : ($burnRate['trend'] === 'decreasing' ? 'success' : 'warning') }}">
                                {{ ucfirst($burnRate['trend']) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fire fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Runway Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ __('dashboard.cash_runway') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($cashRunway['runway_months'])
                                {{ $cashRunway['runway_months'] }} {{ __('dashboard.months') }}
                            @else
                                {{ __('dashboard.unlimited') }}
                            @endif
                        </div>
                        <div class="text-xs text-muted">
                            {{ __('dashboard.until') }}: {{ $cashRunway['estimated_date'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Fund Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-{{ $emergencyFund['status'] === 'excellent' ? 'success' : ($emergencyFund['status'] === 'good' ? 'info' : ($emergencyFund['status'] === 'warning' ? 'warning' : 'danger')) }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $emergencyFund['status'] === 'excellent' ? 'success' : ($emergencyFund['status'] === 'good' ? 'info' : ($emergencyFund['status'] === 'warning' ? 'warning' : 'danger')) }} text-uppercase mb-1">
                            {{ __('dashboard.emergency_fund') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($emergencyFund['total_amount'], 0, ',', '.') }}</div>
                        <div class="text-xs text-muted">
                            {{ $emergencyFund['coverage_months'] }} {{ __('dashboard.month_coverage') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Cash Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ __('dashboard.total_cash') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($cashRunway['total_cash'], 0, ',', '.') }}</div>
                        <div class="text-xs text-muted">
                            {{ __('dashboard.all_active_accounts') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->

<div class="row">

    <!-- Cash Flow Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard.cash_flow_overview') }}</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">{{ __('dashboard.dropdown_header') }}:</div>
                        <a class="dropdown-item" href="#">{{ __('dashboard.action') }}</a>
                        <a class="dropdown-item" href="#">{{ __('dashboard.another_action') }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">{{ __('dashboard.something_else') }}</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Balances -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header -->
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard.account_balances') }}</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                @if($accountBalances->count() > 0)
                    @foreach($accountBalances as $account)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="font-weight-bold">{{ $account->name }}</span>
                                <br>
                                <small class="text-muted">{{ $account->type_label }}</small>
                            </div>
                            <span class="font-weight-bold text-{{ $account->balance >= 0 ? 'success' : 'danger' }}">
                                Rp {{ number_format($account->balance, 0, ',', '.') }}
                            </span>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-2">
                        @endif
                    @endforeach
                @else
                    <p class="text-muted mb-0">{{ __('dashboard.no_accounts_created') }}</p>
                    <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm mt-2">{{ __('dashboard.create_first_account') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cash Flow Projections Section -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard.cash_flow_projections') }}</h6>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="projectionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="daily-tab" data-toggle="tab" href="#daily" role="tab">{{ __('dashboard.daily_30_days') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="weekly-tab" data-toggle="tab" href="#weekly" role="tab">{{ __('dashboard.weekly_12_weeks') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab">{{ __('dashboard.monthly_6_months') }}</a>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="projectionTabsContent">
                    <!-- Daily Projections -->
                    <div class="tab-pane fade show active" id="daily" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.date') }}</th>
                                        <th>{{ __('dashboard.projected_income') }}</th>
                                        <th>{{ __('dashboard.projected_expense') }}</th>
                                        <th>{{ __('dashboard.net_cash_flow') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cashFlowProjections['daily'] as $projection)
                                        <tr>
                                            <td>{{ $projection['date'] }}</td>
                                            <td class="text-success">Rp {{ number_format($projection['projected_income'], 0, ',', '.') }}</td>
                                            <td class="text-danger">Rp {{ number_format($projection['projected_expense'], 0, ',', '.') }}</td>
                                            <td class="font-weight-bold ${{ $projection['projected_net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($projection['projected_net'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Weekly Projections -->
                    <div class="tab-pane fade" id="weekly" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.week') }}</th>
                                        <th>{{ __('dashboard.period') }}</th>
                                        <th>{{ __('dashboard.projected_income') }}</th>
                                        <th>{{ __('dashboard.projected_expense') }}</th>
                                        <th>{{ __('dashboard.net_cash_flow') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cashFlowProjections['weekly'] as $projection)
                                        <tr>
                                            <td>{{ $projection['week'] }}</td>
                                            <td>{{ $projection['period'] }}</td>
                                            <td class="text-success">Rp {{ number_format($projection['projected_income'], 0, ',', '.') }}</td>
                                            <td class="text-danger">Rp {{ number_format($projection['projected_expense'], 0, ',', '.') }}</td>
                                            <td class="font-weight-bold ${{ $projection['projected_net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($projection['projected_net'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Monthly Projections -->
                    <div class="tab-pane fade" id="monthly" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.month') }}</th>
                                        <th>{{ __('dashboard.projected_income') }}</th>
                                        <th>{{ __('dashboard.projected_expense') }}</th>
                                        <th>{{ __('dashboard.net_cash_flow') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cashFlowProjections['monthly'] as $projection)
                                        <tr>
                                            <td>{{ $projection['month'] }}</td>
                                            <td class="text-success">Rp {{ number_format($projection['projected_income'], 0, ',', '.') }}</td>
                                            <td class="text-danger">Rp {{ number_format($projection['projected_expense'], 0, ',', '.') }}</td>
                                            <td class="font-weight-bold ${{ $projection['projected_net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($projection['projected_net'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Burn Rate Analysis -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard.burn_rate_analysis') }}</h6>
            </div>
            <div class="card-body">
                <div class="chart-area mb-4">
                    <canvas id="burnRateChart" style="height: 300px;"></canvas>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-left-danger">
                            <div class="card-body">
                                <h6>{{ __('dashboard.average_monthly_burn_rate') }}</h6>
                                <h4 class="text-danger">Rp {{ number_format($burnRate['average_monthly'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6>{{ __('dashboard.current_month_burn_rate') }}</h6>
                                <h4 class="text-warning">Rp {{ number_format($burnRate['current'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <h6>{{ __('dashboard.burn_rate_trend') }}</h6>
                                <h4 class="text-{{ $burnRate['trend'] === 'increasing' ? 'danger' : ($burnRate['trend'] === 'decreasing' ? 'success' : 'info') }}">
                                    {{ ucfirst($burnRate['trend']) }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cash Runway Scenarios -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard.cash_runway_scenarios') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($cashRunway['scenarios'] as $scenario => $data)
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-{{ $scenario === 'conservative' ? 'danger' : ($scenario === 'optimistic' ? 'success' : 'warning') }}">
                                <div class="card-body">
                                    <h6 class="card-title text-capitalize">{{ $scenario }}</h6>
                                    <p class="card-text">
                                        <strong>{{ __('dashboard.runway') }}:</strong> {{ $data['months'] }} {{ __('dashboard.months') }}<br>
                                        <strong>{{ __('dashboard.days') }}:</strong> {{ $data['days'] }} {{ __('dashboard.days') }}<br>
                                        <strong>{{ __('dashboard.date') }}:</strong> {{ $data['date'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cash Flow Chart
    const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
    const cashFlowData = @json($cashFlow);
    const cashFlowLabels = cashFlowData.map(item => item.month);
    const cashFlowValues = cashFlowData.map(item => item.net);

    new Chart(cashFlowCtx, {
        type: 'line',
        data: {
            labels: cashFlowLabels,
            datasets: [{
                label: '{{ __('dashboard.net_cash_flow') }}',
                data: cashFlowValues,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: '{{ __('dashboard.monthly_cash_flow') }}'
                }
            }
        }
    });

    // Burn Rate Chart
    const burnRateCtx = document.getElementById('burnRateChart').getContext('2d');
    const burnRateData = @json($burnRate['monthly_history']);
    const burnRateLabels = burnRateData.map(item => item.month);
    const burnRateValues = burnRateData.map(item => item.burn_rate);

    new Chart(burnRateCtx, {
        type: 'bar',
        data: {
            labels: burnRateLabels,
            datasets: [{
                label: '{{ __('dashboard.burn_rate') }}',
                data: burnRateValues,
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: '{{ __('dashboard.monthly_burn_rate_analysis') }}'
                }
            }
        }
    });
});
</script>
