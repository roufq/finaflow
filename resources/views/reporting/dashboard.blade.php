@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('reporting.dashboard.title') }}</h1>
            <p class="text-muted mb-0 small">{{ __('reporting.dashboard.subtitle') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reporting.builder') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-project-diagram fa-sm text-white-50"></i>
                {{ __('reporting.buttons.open_builder') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('reporting.metrics.income') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($metrics['income'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                {{ __('reporting.metrics.expenses') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($metrics['expenses'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                                Net Flow</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($metrics['net_flow'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
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
                                {{ __('reporting.metrics.goals') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $metrics['active_goals'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('reporting.dashboard.reports') }}</h6>
                    <span class="badge badge-primary">{{ $metrics['reports'] ?? 0 }}</span>
                </div>
                <div class="card-body">
                    @if($reports->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('reporting.forms.name') }}</th>
                                        <th>{{ __('reporting.forms.schedule') }}</th>
                                        <th>{{ __('reporting.forms.format') }}</th>
                                        <th class="text-center">{{ __('reporting.dashboard.widgets') }}</th>
                                        <th class="text-right">{{ __('reporting.buttons.export') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $report->name }}</div>
                                            <small class="text-muted">{{ $report->config['description'] ?? '' }}</small>
                                        </td>
                                        <td>{{ $report->schedule ? __('reporting.schedules.' . $report->schedule) : '—' }}</td>
                                        <td>{{ __('reporting.formats.' . $report->format) }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-light">{{ $report->widgets_count }}</span>
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{ route('reporting.builder', $report) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('reporting.reports.export', $report) }}" method="POST" class="d-inline-flex">
                                                    @csrf
                                                    <div class="input-group input-group-sm">
                                                        <select name="format" class="custom-select custom-select-sm">
                                                            @foreach(trans('reporting.formats') as $formatKey => $formatLabel)
                                                                <option value="{{ $formatKey }}" @selected($formatKey === $report->format)>{{ $formatLabel }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="submit">
                                                                <i class="fas fa-download"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">{{ __('reporting.dashboard.empty_reports') }}</p>
                            <a href="#quick-report" class="btn btn-sm btn-primary">{{ __('reporting.dashboard.create_quick') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4" id="quick-report">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('reporting.dashboard.create_quick') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reporting.reports.store') }}">
                        @csrf
                        <div class="form-group">
                            <tags for="report-name">{{ __('reporting.forms.name') }}</tags>
                            <input type="text" class="form-control" id="report-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <tags for="report-schedule">{{ __('reporting.forms.schedule') }}</tags>
                            <select class="form-control" id="report-schedule" name="schedule">
                                <option value="">{{ __('reporting.forms.choose_report') }}</option>
                                @foreach(trans('reporting.schedules') as $key => $label)
                                    <option value="{{ $key }}" @selected(old('schedule') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <tags for="report-format">{{ __('reporting.forms.format') }}</tags>
                            <select class="form-control" id="report-format" name="format">
                                @foreach(trans('reporting.formats') as $key => $label)
                                    <option value="{{ $key }}" @selected(old('format', 'pdf') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <tags for="report-description">{{ __('reporting.forms.description') }}</tags>
                            <textarea class="form-control" id="report-description" rows="2" name="description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">{{ __('reporting.forms.submit') }}</button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('reporting.dashboard.widgets') }}</h6>
                </div>
                <div class="card-body">
                    @if($widgets->count())
                        <ul class="list-group list-group-flush">
                            @foreach($widgets as $widget)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="font-weight-bold">{{ $widget->config['title'] ?? ucfirst($widget->type) }}</div>
                                        <small class="text-muted">{{ trans('reporting.widgets.' . $widget->type . '.description') }}</small>
                                    </div>
                                    <span class="badge badge-light text-uppercase">{{ $widget->size }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">{{ __('reporting.dashboard.empty_widgets') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
