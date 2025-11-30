@extends('layouts.app')

@section('content')
@php
    $savingsRate = $totalIncome > 0 ? ($netBalance / $totalIncome) * 100 : 0;
    $alerts = [];

    if ($netBalance < 0) {
        $alerts[] = [
            'level' => 'danger',
            'title' => 'Cash flow defisit bulan ini',
            'detail' => 'Segera kurangi pengeluaran atau tambah pemasukan untuk menutup gap.',
        ];
    }

    if ($totalIncome > 0 && $burnRate['average_monthly'] > ($totalIncome * 0.9)) {
        $alerts[] = [
            'level' => 'danger',
            'title' => 'Burn rate mendekati total income',
            'detail' => 'Prioritaskan pemotongan pos besar dan cek pengeluaran anomali.',
        ];
    }

    if ($savingsRate < 20 && $totalIncome > 0) {
        $alerts[] = [
            'level' => 'warning',
            'title' => 'Rasio tabungan di bawah 20%',
            'detail' => 'Targetkan minimal 20% dengan memindahkan surplus ke tabungan/investasi.',
        ];
    }

    if ($cashRunway['runway_months'] !== null && $cashRunway['runway_months'] < 3) {
        $alerts[] = [
            'level' => 'danger',
            'title' => 'Cash runway kurang dari 3 bulan',
            'detail' => 'Bangun buffer kas dengan menahan pengeluaran non-esensial.',
        ];
    } elseif ($cashRunway['runway_months'] !== null && $cashRunway['runway_months'] < 6) {
        $alerts[] = [
            'level' => 'warning',
            'title' => 'Cash runway belum mencapai 6 bulan',
            'detail' => 'Tambahkan setoran rutin ke dana darurat untuk perpanjang runway.',
        ];
    }

    if ($emergencyFund['coverage_months'] < 3) {
        $alerts[] = [
            'level' => 'danger',
            'title' => 'Dana darurat < 3 bulan pengeluaran',
            'detail' => 'Fokus isi dana darurat hingga minimal 3 bulan.',
        ];
    } elseif ($emergencyFund['coverage_months'] < 6) {
        $alerts[] = [
            'level' => 'warning',
            'title' => 'Dana darurat < 6 bulan pengeluaran',
            'detail' => 'Naikkan setoran bulanan untuk capai 6 bulan proteksi.',
        ];
    }

    if ($accountBalances->count() === 0) {
        $alerts[] = [
            'level' => 'info',
            'title' => 'Belum ada akun keuangan',
            'detail' => 'Tambahkan akun bank/cash agar saldo dan runway akurat.',
        ];
    }
@endphp
<!-- Page Heading -->
<div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(180deg, #f8f9fb 0%, #ffffff 100%);">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-lg-4 mb-3 mb-lg-0">
                <h1 class="h4 mb-1 text-gray-800">{{ __('dashboard.title') }}</h1>
                <div class="small text-muted">Periode: {{ $periodDate->translatedFormat('F Y') }}</div>
            </div>
            <div class="col-lg-8">
                <div class="row align-items-end">
                    <div class="col-md-7 mb-2">
                        <form class="form-row" method="GET" action="{{ route('dashboard') }}">
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <select name="month" class="form-control form-control-sm" aria-label="Pilih bulan">
                                    @foreach(range(1, 12) as $monthOption)
                                        <option value="{{ $monthOption }}" {{ $monthOption === (int) $periodDate->format('n') ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($monthOption)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <input type="number" name="year" class="form-control form-control-sm" min="2000" max="2100" value="{{ $periodDate->format('Y') }}" aria-label="Pilih tahun">
                            </div>
                            <div class="col-6 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">Terapkan</button>
                            </div>
                            <div class="col-6 mt-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm btn-block">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5 mb-2">
                        <a href="#" class="btn btn-sm btn-primary shadow-sm d-inline-flex align-items-center w-100">
                            <i class="fas fa-download fa-sm text-white-50 mr-1"></i> {{ __('dashboard.generate_report') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap align-items-center mb-4">
    @php
        $savingsBadge = $savingsRate >= 20 ? 'success' : ($savingsRate >= 10 ? 'warning' : 'danger');
        $runwayValue = $cashRunway['runway_months'] ?? null;
        $runwayBadge = is_null($runwayValue) ? 'success' : ($runwayValue >= 6 ? 'success' : ($runwayValue >= 3 ? 'warning' : 'danger'));
        $emergencyBadge = $emergencyFund['coverage_months'] >= 6 ? 'success' : ($emergencyFund['coverage_months'] >= 3 ? 'warning' : 'danger');
        $burnBadge = $burnRate['trend'] === 'decreasing' ? 'success' : ($burnRate['trend'] === 'stable' ? 'warning' : 'danger');
    @endphp
    <div class="badge badge-{{ $savingsBadge }} badge-pill mr-2 mb-2">
        Savings Rate: {{ number_format($savingsRate, 1) }}%
    </div>
    <div class="badge badge-{{ $runwayBadge }} badge-pill mr-2 mb-2">
        Runway: {{ $runwayValue ? $runwayValue.' bulan' : 'Stabil' }}
    </div>
    <div class="badge badge-{{ $emergencyBadge }} badge-pill mr-2 mb-2">
        Dana Darurat: {{ $emergencyFund['coverage_months'] }} bln
    </div>
    <div class="badge badge-{{ $burnBadge }} badge-pill mr-2 mb-2 text-capitalize">
        Burn Trend: {{ $burnRate['trend'] }}
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card shadow-sm border-left-primary h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Health Score</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $debtHealth['score'] }}/100</div>
                <small class="text-muted">Semakin tinggi semakin sehat</small>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card shadow-sm border-left-info h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Expense / Income</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $debtHealth['expense_to_income'] }}%</div>
                <small class="text-muted">Ideal di bawah 70%</small>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card shadow-sm border-left-warning h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Debt / Income</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $debtHealth['debt_to_income'] }}%</div>
                <small class="text-muted">Ideal di bawah 30-40%</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form class="form-inline flex-wrap align-items-center w-100" method="GET" action="{{ route('transactions.index') }}">
            <div class="input-group input-group-sm mr-md-2 mb-2 w-100 w-md-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari transaksi, kategori, atau nominal..." aria-label="Pencarian cepat transaksi">
            </div>
            <button type="submit" class="btn btn-primary btn-sm mb-2 w-100 w-sm-auto">
                <i class="fas fa-search mr-1"></i> Cari Cepat
            </button>
            <small class="text-muted ml-md-2 mb-2 d-block">Pencarian akan membuka daftar transaksi.</small>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-8 mb-4 mb-xl-0">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Prioritas Finansial</h6>
                <span class="badge badge-pill badge-{{ count($alerts) > 0 ? 'danger' : 'success' }}">
                    {{ count($alerts) > 0 ? count($alerts).' alert' : 'Sehat' }}
                </span>
            </div>
            <div class="card-body">
                @if(count($alerts) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($alerts as $alert)
                            <div class="list-group-item d-flex align-items-start justify-content-between">
                                <div class="mr-3">
                                    <div class="font-weight-bold text-{{ $alert['level'] }}">{{ $alert['title'] }}</div>
                                    <div class="small text-muted">{{ $alert['detail'] }}</div>
                                </div>
                                <span class="badge badge-{{ $alert['level'] }} text-uppercase">{{ $alert['level'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mb-0 text-success font-weight-bold">Semua metrik utama dalam kondisi baik.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap">
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm mr-2 mb-2 w-100 w-sm-auto">
                        <i class="fas fa-plus-circle mr-1"></i> Catat transaksi
                    </a>
                    <a href="{{ route('budgets.create') }}" class="btn btn-outline-primary btn-sm mr-2 mb-2 w-100 w-sm-auto">
                        <i class="fas fa-calculator mr-1"></i> Buat anggaran
                    </a>
                    <a href="{{ route('goals.create') }}" class="btn btn-outline-success btn-sm mr-2 mb-2 w-100 w-sm-auto">
                        <i class="fas fa-bullseye mr-1"></i> Tambah goal
                    </a>
                    <a href="{{ route('bank-integrations.create') }}" class="btn btn-outline-info btn-sm mr-2 mb-2 w-100 w-sm-auto">
                        <i class="fas fa-university mr-1"></i> Sambungkan bank
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-warning btn-sm mr-2 mb-2 w-100 w-sm-auto">
                        <i class="fas fa-sync-alt mr-1"></i> Tinjau langganan
                    </a>
                </div>
            </div>
        </div>
    </div>
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
                <div class="chart-area chart-wrapper loading" style="height: clamp(220px, 30vh, 420px);">
                    <div class="skeleton-box"></div>
                    <canvas id="cashFlowChart" data-cash-flow='@json($cashFlow)'></canvas>
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
                                            <td class="font-weight-bold {{ $projection['projected_net'] >= 0 ? 'text-success' : 'text-danger' }}">
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
                                            <td class="font-weight-bold {{ $projection['projected_net'] >= 0 ? 'text-success' : 'text-danger' }}">
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
                                            <td class="font-weight-bold {{ $projection['projected_net'] >= 0 ? 'text-success' : 'text-danger' }}">
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
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard.burn_rate_analysis') }}</h6>
                <span class="badge badge-light text-capitalize">{{ ucfirst($burnRate['trend']) }}</span>
            </div>
            <div class="card-body">
                <div class="chart-area chart-wrapper loading mb-4">
                    <div class="skeleton-box"></div>
                    <canvas id="burnRateChart" style="height: clamp(220px, 30vh, 420px);" data-burn-rate='@json($burnRate['monthly_history'])'></canvas>
                    <div id="burnRateEmpty" class="text-center text-muted py-5 d-none">
                        <i class="fas fa-chart-line fa-2x mb-3 text-gray-300"></i>
                        <p class="mb-0">{{ __('dashboard.no_data') ?? 'No burn rate data yet.' }}</p>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded h-100">
                            <div class="text-muted small mb-1">{{ __('dashboard.average_monthly_burn_rate') }}</div>
                            <div class="h5 text-danger mb-0">Rp {{ number_format($burnRate['average_monthly'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded h-100">
                            <div class="text-muted small mb-1">{{ __('dashboard.current_month_burn_rate') }}</div>
                            <div class="h5 text-warning mb-0">Rp {{ number_format($burnRate['current'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded h-100">
                            <div class="text-muted small mb-1">{{ __('dashboard.burn_rate_trend') }}</div>
                            <div class="h5 text-{{ $burnRate['trend'] === 'increasing' ? 'danger' : ($burnRate['trend'] === 'decreasing' ? 'success' : 'info') }} mb-0">
                                {{ ucfirst($burnRate['trend']) }}
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

<script>
(function () {
    const cashFlowData = @json($cashFlow);
    const burnRateHistory = @json($burnRate['monthly_history']);
    const isMobileViewport = window.matchMedia('(max-width: 767.98px)').matches;

    const initCharts = function () {
        if (!window.Chart) {
            return;
        }

        const cashFlowCanvas = document.getElementById('cashFlowChart');
        if (cashFlowCanvas) {
            cashFlowCanvas.closest('.chart-wrapper')?.classList.remove('loading');
            const ctx = cashFlowCanvas.getContext('2d');
            const labels = cashFlowData.map(item => item.month);
            const values = cashFlowData.map(item => item.net);

            if (cashFlowCanvas._chartInstance) {
                cashFlowCanvas._chartInstance.destroy();
            }

            cashFlowCanvas._chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ __('dashboard.net_cash_flow') }}',
                        data: values,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: !isMobileViewport, position: 'top' },
                        title: { display: true, text: '{{ __('dashboard.monthly_cash_flow') }}' }
                    }
                }
            });
        }

        const burnCanvas = document.getElementById('burnRateChart');
        const emptyState = document.getElementById('burnRateEmpty');
        if (burnCanvas) {
            burnCanvas.closest('.chart-wrapper')?.classList.remove('loading');
            const labels = burnRateHistory.map(item => item.month);
            const values = burnRateHistory.map(item => item.burn_rate);
            const hasData = values.some(value => Number(value) !== 0);

            if (!hasData) {
                burnCanvas.classList.add('d-none');
                if (emptyState) {
                    emptyState.classList.remove('d-none');
                }
                return;
            }

            burnCanvas.classList.remove('d-none');
            if (emptyState) {
                emptyState.classList.add('d-none');
            }

            const ctx = burnCanvas.getContext('2d');

            if (burnCanvas._chartInstance) {
                burnCanvas._chartInstance.destroy();
            }

            burnCanvas._chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ __('dashboard.burn_rate') }}',
                        data: values,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + Number(value).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: !isMobileViewport, position: 'top' },
                        title: { display: true, text: '{{ __('dashboard.monthly_burn_rate_analysis') }}' }
                    }
                }
            });
        }
    };

    initCharts();
})();
</script>
@endsection
