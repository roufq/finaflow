@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('rewards.title') }}</h1>
        <a href="{{ route('rewards.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('rewards.buttons.add_reward') }}
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('rewards.summary.total_points') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPoints, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
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
                                {{ __('rewards.summary.cashback_earned') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalCashback, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                {{ __('rewards.summary.cards_tracked') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rewards->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('rewards.summary.best_card') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bestCard ?: __('rewards.summary.none') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rewards Cards -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('rewards.cards.section_title') }}</h6>
        </div>
        <div class="card-body">
            @if($rewards->count() > 0)
                <div class="row">
                    @foreach($rewards as $reward)
                    <div class="col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            {{ $reward->card_type }}</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reward->reward_type }}</div>
                                        <div class="text-xs text-muted mt-2">
                                            {{ __('rewards.cards.points_earned') }}:
                                            <span class="font-weight-bold">{{ number_format($reward->points_earned, 0, ',', '.') }}</span><br>
                                            {{ __('rewards.cards.points_redeemed') }}:
                                            {{ number_format($reward->points_redeemed, 0, ',', '.') }}<br>
                                            {{ __('rewards.cards.cashback') }}:
                                            Rp {{ number_format($reward->cashback_amount, 0, ',', '.') }}<br>
                                            {{ __('rewards.cards.expires') }}:
                                            {{ $reward->expiry_date ? $reward->expiry_date->format('d M Y') : __('rewards.summary.none') }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-info mb-2" onclick="viewDetails({{ $reward->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteReward({{ $reward->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-credit-card fa-4x text-warning mb-3"></i>
                    <h4>{{ __('rewards.cards.empty_title') }}</h4>
                    <p class="text-muted">{{ __('rewards.cards.empty_body') }}</p>
                    <a href="{{ route('rewards.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus"></i> {{ __('rewards.buttons.add_first_card') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Loyalty Programs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('rewards.loyalty.section_title') }}</h6>
        </div>
        <div class="card-body">
            @if($loyaltyPrograms->count() > 0)
                <div class="row">
                    @foreach($loyaltyPrograms as $program)
                    <div class="col-md-4 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-center">
                                    <h6 class="font-weight-bold">{{ $program->program_name }}</h6>
                                    <div class="h4 mb-0 text-info">{{ number_format($program->points_balance, 0, ',', '.') }}</div>
                                    <div class="text-xs text-muted">{{ __('rewards.loyalty.points_balance') }}</div>
                                    <div class="mt-2">
                                        <span class="badge badge-info">{{ $program->tier_level }}</span>
                                    </div>
                                    @if($program->benefits)
                                    <div class="text-xs mt-2">{{ $program->benefits }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-3">
                    <p class="text-muted">{{ __('rewards.loyalty.empty') }}</p>
                    <a href="{{ route('rewards.create-loyalty') }}" class="btn btn-sm btn-info">{{ __('rewards.buttons.add_loyalty') }}</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Optimization Tips -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Optimization Tips</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach (['cashback', 'loyalty'] as $type)
                            @php($tip = trans('rewards.tips.' . $type))
                            <div class="col-md-6">
                                <h6>{{ $tip['title'] }}</h6>
                                <ul>
                                    @foreach ($tip['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function viewDetails(id) {
    // Implement view details functionality
    alert(@json(__('rewards.messages.view_placeholder')));
}

function deleteReward(id) {
    if (confirm(@json(__('rewards.messages.delete_confirm')))) {
        // Implement delete functionality
        alert(@json(__('rewards.messages.delete_placeholder')));
    }
}
</script>
@endsection
