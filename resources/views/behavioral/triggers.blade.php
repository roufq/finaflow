@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('behavioral.triggers.title') }}</h1>
        <a href="{{ route('behavioral.triggers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('behavioral.triggers.add_trigger') }}
        </a>
    </div>

    <!-- Triggers List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.triggers.title') }}</h6>
        </div>
        <div class="card-body">
            @if($triggers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>{{ __('behavioral.triggers.trigger_type') }}</th>
                                <th>{{ __('behavioral.triggers.description') }}</th>
                                <th>{{ __('behavioral.triggers.frequency') }}</th>
                                <th>{{ __('behavioral.triggers.amount_threshold') }}</th>
                                <th>{{ __('behavioral.triggers.last_detected') }}</th>
                                <th>{{ __('behavioral.triggers.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($triggers as $trigger)
                            <tr>
                                <td>{{ $trigger->trigger_type }}</td>
                                <td>{{ $trigger->description }}</td>
                                <td>
                                    <span class="badge badge-warning">{{ $trigger->frequency }} times</span>
                                </td>
                                <td>Rp {{ number_format($trigger->amount_threshold, 0, ',', '.') }}</td>
                                <td>{{ $trigger->updated_at->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteTrigger({{ $trigger->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                    <h4>{{ __('behavioral.triggers.no_triggers') }}</h4>
                    <p class="text-muted">{{ __('behavioral.triggers.auto_detection') }}</p>
                    <a href="{{ route('behavioral.triggers.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus"></i> {{ __('behavioral.triggers.add_manual') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Insights Card -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.insights.title') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('behavioral.insights.common_triggers') }}</h6>
                            <ul>
                                <li><strong>{{ __('behavioral.triggers.emotional_spending') }}:</strong> {{ __('behavioral.insights.emotional_desc') }}</li>
                                <li><strong>{{ __('behavioral.triggers.social_pressure') }}:</strong> {{ __('behavioral.insights.social_desc') }}</li>
                                <li><strong>{{ __('behavioral.triggers.impulse_buying') }}:</strong> {{ __('behavioral.insights.impulse_desc') }}</li>
                                <li><strong>{{ __('behavioral.triggers.reward_shopping') }}:</strong> {{ __('behavioral.insights.reward_desc') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('behavioral.insights.tips_title') }}</h6>
                            <ul>
                                <li>{{ __('behavioral.insights.tip_1') }}</li>
                                <li>{{ __('behavioral.insights.tip_2') }}</li>
                                <li>{{ __('behavioral.insights.tip_3') }}</li>
                                <li>{{ __('behavioral.insights.tip_4') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function deleteTrigger(id) {
    if (confirm('{{ __('behavioral.triggers.confirm_delete') }}')) {
        // Implement delete functionality
        alert('{{ __('behavioral.triggers.delete_implement') }}');
    }
}
</script>
@endsection
