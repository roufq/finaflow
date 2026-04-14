@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('coaching.title') }}</h1>
            <p class="text-muted mb-0 small">{{ __('coaching.subtitle') }}</p>
        </div>
        <a href="{{ route('coaching.export') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-file-export fa-sm text-primary-50"></i> {{ __('coaching.export.button') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-tags="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('coaching.plan.current_plan') }}</h6>
                    @if($currentPlan)
                        <span class="badge badge-light text-uppercase">{{ $currentPlan->month_tags }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($currentPlan)
                        <div class="mb-4">
                            <h5 class="font-weight-bold text-gray-800">{{ __('coaching.plan.priorities') }}</h5>
                            <div>
                                @foreach($currentPlan->focus_priorities ?? [] as $priority)
                                    <span class="badge badge-info mr-1 mb-1">{{ Str::title(str_replace('_', ' ', $priority)) }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-4">
                            <h5 class="font-weight-bold text-gray-800">{{ __('coaching.plan.recommended_actions') }}</h5>
                            <div class="row">
                                @foreach($currentPlan->recommended_actions ?? [] as $action)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="font-weight-bold mb-1">{{ $action['title'] }}</h6>
                                            <p class="text-muted small mb-0">{{ $action['description'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-2">
                                    <p class="text-muted text-xs mb-1">{{ __('coaching.plan.savings_target') }}</p>
                                    <strong>Rp {{ number_format($currentPlan->savings_target, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-2">
                                    <p class="text-muted text-xs mb-1">{{ __('coaching.plan.debt_target') }}</p>
                                    <strong>Rp {{ number_format($currentPlan->debt_repayment_target, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-2">
                                    <p class="text-muted text-xs mb-1">{{ __('coaching.plan.investment_target') }}</p>
                                    <strong>Rp {{ number_format($currentPlan->investment_target, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                        @if($currentPlan->summary_notes)
                            <p class="text-muted small mt-2 mb-0">{{ $currentPlan->summary_notes }}</p>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-2">{{ __('coaching.plan.no_plan') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('coaching.plan.create_new') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('coaching.action-plan.store') }}">
                        @csrf
                        <div class="form-group">
                            <tags class="text-xs text-muted text-uppercase">{{ __('coaching.plan.plan_month') }}</tags>
                            <input type="month" class="form-control" name="plan_month" value="{{ old('plan_month', now()->format('Y-m')) }}" required>
                        </div>
                        <div class="form-group">
                            <tags class="text-xs text-muted text-uppercase">{{ __('coaching.plan.summary_notes') }}</tags>
                            <textarea class="form-control" rows=2 name="summary_notes">{{ old('summary_notes') }}</textarea>
                        </div>
                        <div class="form-group">
                            <tags class="text-xs text-muted text-uppercase">{{ __('coaching.plan.targets') }}</tags>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control" name="savings_target" placeholder="{{ __('coaching.plan.savings_target') }}">
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control" name="debt_repayment_target" placeholder="{{ __('coaching.plan.debt_target') }}">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control" name="investment_target" placeholder="{{ __('coaching.plan.investment_target') }}">
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="generateTasks" name="generate_tasks" value="1" checked>
                            <tags class="custom-control-tags" for="generateTasks">{{ __('coaching.plan.generate_tasks') }}</tags>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">{{ __('coaching.plan.create_new') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($currentPlan)
    <div class="row">
        <div class="col-xl-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('coaching.tasks.title') }}</h6>
                    <button class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#newTaskForm">
                        <i class="fas fa-plus"></i> {{ __('coaching.tasks.add_task') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse mb-4" id="newTaskForm">
                        <form method="POST" action="{{ route('coaching.tasks.store') }}">
                            @csrf
                            <input type="hidden" name="action_plan_id" value="{{ $currentPlan->id }}">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <tags class="text-xs text-muted text-uppercase">{{ __('coaching.tasks.add_task') }}</tags>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <tags class="text-xs text-muted text-uppercase">Week</tags>
                                    <input type="number" class="form-control" min="1" max="6" name="week_index" value="1">
                                </div>
                                <div class="form-group col-md-4">
                                    <tags class="text-xs text-muted text-uppercase">{{ __('coaching.tasks.due_date') }}</tags>
                                    <input type="date" class="form-control" name="due_date">
                                </div>
                            </div>
                            <div class="form-group">
                                <tags class="text-xs text-muted text-uppercase">{{ __('coaching.tasks.notes') }}</tags>
                                <textarea class="form-control" rows="2" name="notes"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('coaching.tasks.add_task') }}</button>
                        </form>
                    </div>

                    @if($currentPlan->tasks->isEmpty())
                        <p class="text-muted mb-0">{{ __('coaching.plan.generate_tasks') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('coaching.tasks.add_task') }}</th>
                                        <th>{{ __('coaching.tasks.due_date') }}</th>
                                        <th>{{ __('coaching.tasks.status') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($currentPlan->tasks->sortBy('due_date') as $task)
                                        <tr>
                                            <td>{{ $task->week_index }}</td>
                                            <td>
                                                <strong>{{ $task->title }}</strong>
                                                @if($task->notes)
                                                    <p class="text-muted small mb-0">{{ $task->notes }}</p>
                                                @endif
                                            </td>
                                            <td>{{ optional($task->due_date)->format('d M Y') }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('coaching.tasks.update', $task) }}" class="form-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                                        @foreach(['pending','in_progress','completed','skipped'] as $status)
                                                            <option value="{{ $status }}" @selected($task->status === $status)>{{ Str::title(str_replace('_',' ',$status)) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="notes" value="{{ $task->notes }}">
                                                </form>
                                            </td>
                                            <td>
                                                @if($task->status === 'completed')
                                                    <span class="badge badge-success">{{ __('coaching.tasks.status') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('coaching.export.title') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ __('coaching.export.description') }}</p>
                    <a href="{{ route('coaching.export') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download"></i> {{ __('coaching.export.button') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-xl-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('coaching.lessons.title') }}</h6>
                </div>
                <div class="card-body">
                    @if($lessons->isEmpty())
                        <p class="text-muted mb-0">{{ __('coaching.plan.no_plan') }}</p>
                    @else
                        <div class="row">
                            @foreach($lessons as $lesson)
                                @php($progress = $lessonProgress->get($lesson->id))
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100 d-flex flex-column">
                                        <h5 class="font-weight-bold">{{ $lesson->title }}</h5>
                                        @if($lesson->persona_tags)
                                            <div class="mb-2">
                                                @foreach($lesson->persona_tags as $tag)
                                                    <span class="badge badge-light border mr-1 mb-1">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <p class="text-xs text-muted mb-1">{{ __('coaching.lessons.duration') }}: {{ $lesson->duration_minutes }} min · {{ __('coaching.lessons.format') }}: {{ ucfirst($lesson->format) }}</p>
                                        <p class="text-muted small flex-grow-1">{{ Str::limit($lesson->summary, 120) }}</p>
                                        <form method="POST" action="{{ route('coaching.lessons.update', $lesson) }}">
                                            @csrf
                                            <div class="form-group">
                                                <tags class="text-xs text-muted text-uppercase">{{ __('coaching.lessons.status') }}</tags>
                                                <select name="status" class="form-control form-control-sm">
                                                    @foreach(['not_started','in_progress','completed'] as $status)
                                                        <option value="{{ $status }}" @selected(optional($progress)->status === $status)>{{ Str::title(str_replace('_',' ',$status)) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <tags class="text-xs text-muted text-uppercase">{{ __('coaching.lessons.score') }}</tags>
                                                <input type="number" class="form-control form-control-sm" name="comprehension_score" value="{{ optional($progress)->comprehension_score }}" min="0" max="100">
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('coaching.lessons.status') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('coaching.journal.title') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('coaching.journal.store') }}" class="mb-4">
                        @csrf
                        <div class="form-group">
                            <tags class="text-xs text-muted text-uppercase">{{ __('coaching.journal.reflection') }}</tags>
                            <textarea class="form-control" rows="2" name="reflection" required></textarea>
                        </div>
                        <div class="form-group">
                            <tags class="text-xs text-muted text-uppercase">{{ __('coaching.journal.commitment') }}</tags>
                            <textarea class="form-control" rows="2" name="commitment"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <tags class="text-xs text-muted text-uppercase">{{ __('coaching.journal.mood') }}</tags>
                                <input type="text" class="form-control" name="mood" placeholder="Calm, anxious, etc.">
                            </div>
                            <div class="form-group col-md-4">
                                <tags class="text-xs text-muted text-uppercase">Habit</tags>
                                <select class="form-control" name="habit_id">
                                    <option value="">{{ __('coaching.filters.all') }}</option>
                                    @foreach($habits as $habit)
                                        <option value="{{ $habit->id }}">{{ $habit->habit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <tags class="text-xs text-muted text-uppercase">Trigger</tags>
                                <select class="form-control" name="spending_trigger_id">
                                    <option value="">{{ __('coaching.filters.all') }}</option>
                                    @foreach($triggers as $trigger)
                                        <option value="{{ $trigger->id }}">{{ $trigger->trigger_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">{{ __('coaching.journal.log_entry') }}</button>
                    </form>

                    <h6 class="font-weight-bold text-gray-800">{{ __('coaching.journal.recent_entries') }}</h6>
                    @forelse($journalEntries as $entry)
                        <div class="border-bottom py-2">
                            <p class="mb-1 font-weight-bold">{{ $entry->logged_at->format('d M Y') }}</p>
                            <p class="text-muted small mb-1">{{ Str::limit($entry->reflection, 120) }}</p>
                            @if($entry->commitment)
                                <p class="text-xs text-success mb-0"><strong>{{ __('coaching.journal.commitment') }}:</strong> {{ $entry->commitment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('coaching.plan.no_plan') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
