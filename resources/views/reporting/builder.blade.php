@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .sortable-placeholder {
        border: 2px dashed #d1d3e2;
        border-radius: 0.35rem;
        min-height: 160px;
        margin-bottom: 1.5rem;
    }
</style>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('reporting.builder.title') }}</h1>
            <p class="text-muted mb-0 small">{{ __('reporting.builder.subtitle') }}</p>
        </div>
        @if($report)
        <form action="{{ route('reporting.reports.export', $report) }}" method="POST" class="form-inline">
            @csrf
            <select name="format" class="custom-select custom-select-sm mr-2">
                @foreach(trans('reporting.formats') as $key => $label)
                    <option value="{{ $key }}" @selected($key === $report->format)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary shadow-sm" type="submit">
                <i class="fas fa-download fa-sm text-white-50"></i> {{ __('reporting.buttons.export') }}
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('reporting.forms.name') }}</h6>
                </div>
                <div class="card-body">
                    @if($report)
                        <div class="mb-3">
                            <h5 class="font-weight-bold mb-1">{{ $report->name }}</h5>
                            <p class="text-muted mb-1 small">{{ $report->config['description'] ?? __('reporting.subtitle') }}</p>
                            <div class="d-flex flex-wrap gap-2 text-xs">
                                <span class="badge badge-light">{{ $report->schedule ? __('reporting.schedules.' . $report->schedule) : __('reporting.forms.schedule') }}</span>
                                <span class="badge badge-light">{{ __('reporting.formats.' . $report->format) }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-3">{{ __('reporting.dashboard.empty_reports') }}</p>
                        <form method="POST" action="{{ route('reporting.reports.store') }}">
                            @csrf
                            <div class="form-group">
                                <tags for="builder-name">{{ __('reporting.forms.name') }}</tags>
                                <input type="text" class="form-control" id="builder-name" name="name" required>
                            </div>
                            <div class="form-group">
                                <tags for="builder-schedule">{{ __('reporting.forms.schedule') }}</tags>
                                <select class="form-control" id="builder-schedule" name="schedule">
                                    <option value="">{{ __('reporting.forms.choose_report') }}</option>
                                    @foreach(trans('reporting.schedules') as $key => $label)
                                        <option value="{{ $key }}" @selected(old('schedule') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <tags for="builder-format">{{ __('reporting.forms.format') }}</tags>
                                <select class="form-control" id="builder-format" name="format">
                                    @foreach(trans('reporting.formats') as $key => $label)
                                        <option value="{{ $key }}" @selected(old('format', 'pdf') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <tags for="builder-description">{{ __('reporting.forms.description') }}</tags>
                                <textarea class="form-control" id="builder-description" rows="2" name="description"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">{{ __('reporting.forms.submit') }}</button>
                        </form>
                    @endif
                </div>
            </div>

            @if($report)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('reporting.builder.available_widgets') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ __('reporting.builder.instructions') }}</p>
                    <div class="widget-library">
                        @foreach($availableWidgets as $type => $widget)
                        <div class="widget-template border rounded p-3 mb-3" data-widget-type="{{ $type }}">
                            <div class="d-flex align-items-center mb-2">
                                <i class="{{ $widget['icon'] }} text-primary mr-2"></i>
                                <strong>{{ $widget['title'] }}</strong>
                            </div>
                            <p class="text-muted small mb-2">{{ $widget['description'] }}</p>
                            <button class="btn btn-sm btn-outline-primary add-widget" data-widget-type="{{ $type }}">
                                <i class="fas fa-plus"></i> {{ __('reporting.forms.add_widget') }}
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('reporting.builder.layout_preview') }}</h6>
                    <span class="badge badge-light">{{ $widgets->count() }} {{ __('reporting.dashboard.widgets') }}</span>
                </div>
                <div class="card-body">
                    @if($report)
                        <div id="widget-canvas" class="row" data-update-url="{{ route('reporting.widgets.positions') }}">
                            @forelse($widgets as $widget)
                            <div class="col-md-6 mb-4 report-widget" data-widget-id="{{ $widget->id }}" data-widget-size="{{ $widget->size }}">
                                <div class="card shadow-sm border-left-primary h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="widget-drag text-muted mr-2" title="Drag to reorder"><i class="fas fa-arrows-alt"></i></span>
                                            <strong>{{ $widget->config['title'] ?? ucfirst($widget->type) }}</strong>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <select class="custom-select custom-select-sm widget-size mr-2" data-widget-id="{{ $widget->id }}">
                                                @foreach(['small', 'medium', 'large'] as $size)
                                                    <option value="{{ $size }}" @selected($widget->size === $size)>{{ ucfirst($size) }}</option>
                                                @endforeach
                                            </select>
                                            <div class="btn-group btn-group-sm" role="group" aria-tags="Widget actions">
                                                <button type="button"
                                                    class="btn btn-outline-secondary edit-widget"
                                                    data-widget-id="{{ $widget->id }}"
                                                    data-widget-title="{{ $widget->config['title'] ?? '' }}"
                                                    data-widget-notes="{{ $widget->config['notes'] ?? '' }}"
                                                    data-widget-size="{{ $widget->size }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-outline-danger delete-widget"
                                                    data-widget-id="{{ $widget->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small mb-2">{{ trans('reporting.widgets.' . $widget->type . '.description') }}</p>
                                        <div class="d-flex justify-content-between text-xs text-muted">
                                            <span>{{ __('reporting.forms.widget_type') }}: {{ ucfirst(str_replace('_', ' ', $widget->type)) }}</span>
                                            <span>{{ __('reporting.forms.widget_size') }}: {{ ucfirst($widget->size) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                                <div class="col-12 text-center py-5 text-muted">
                                    <i class="fas fa-layer-group fa-3x mb-3"></i>
                                    <p>{{ __('reporting.builder.empty') }}</p>
                                </div>
                            @endforelse
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-layer-group fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">{{ __('reporting.dashboard.empty_reports') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($report)
<div class="modal fade" id="widgetEditModal" tabindex="-1" role="dialog" aria-labelledby="widgetEditModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="widgetEditModalLabel">{{ __('reporting.forms.widget_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-tags="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="widgetEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <tags for="widget-edit-title">{{ __('reporting.forms.widget_title') }}</tags>
                        <input type="text" name="title" id="widget-edit-title" class="form-control" maxlength="255">
                    </div>
                    <div class="form-group">
                        <tags for="widget-edit-notes">{{ __('reporting.forms.notes') }}</tags>
                        <textarea name="notes" id="widget-edit-notes" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <tags for="widget-edit-size">{{ __('reporting.forms.widget_size') }}</tags>
                        <select name="size" id="widget-edit-size" class="form-control">
                            @foreach(['small', 'medium', 'large'] as $size)
                                <option value="{{ $size }}">{{ ucfirst($size) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($report)
<script>
    (function () {
        const initBuilder = function () {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : '';
            const reportId = @json($report?->id);
            const widgetStoreUrl = @json(route('reporting.widgets.store'));
            const widgetUpdateUrlTemplate = @json(route('reporting.widgets.update', ['widget' => '__WIDGET__']));
            const widgetDeleteUrlTemplate = @json(route('reporting.widgets.destroy', ['widget' => '__WIDGET__']));

            const persistLayout = function () {
                const payload = [];
                $('#widget-canvas .report-widget').each(function (index, element) {
                    payload.push({
                        id: $(element).data('widget-id'),
                        position: index + 1,
                        size: $(element).find('.widget-size').val(),
                    });
                });

                if (!payload.length) {
                    return;
                }

                $.post($('#widget-canvas').data('update-url'), {
                    _token: token,
                    widgets: payload,
                });
            };

            $('#widget-canvas').sortable({
                handle: '.widget-drag',
                placeholder: 'sortable-placeholder col-md-6 mb-4',
                update: persistLayout,
            });

            $('.widget-size').on('change', persistLayout);

            $('.add-widget').on('click', function (event) {
                event.preventDefault();
                const type = $(this).data('widget-type');
                $.post(widgetStoreUrl, {
                    _token: token,
                    report_id: reportId,
                    type: type,
                }).done(function () {
                    window.location.reload();
                });
            });

            $('#widget-canvas').on('click', '.edit-widget', function () {
                const widgetId = $(this).data('widget-id');
                const title = $(this).data('widget-title') || '';
                const notes = $(this).data('widget-notes') || '';
                const size = $(this).data('widget-size') || 'medium';

                const action = widgetUpdateUrlTemplate.replace('__WIDGET__', widgetId);
                $('#widgetEditForm').attr('action', action);
                $('#widget-edit-title').val(title);
                $('#widget-edit-notes').val(notes);
                $('#widget-edit-size').val(size);
                $('#widgetEditModal').modal('show');
            });

            $('#widgetEditForm').on('submit', function (event) {
                event.preventDefault();
                const action = $(this).attr('action');
                const data = $(this).serialize();

                $.post(action, data).done(function () {
                    window.location.reload();
                }).fail(function () {
                    alert('Unable to update widget. Please try again.');
                });
            });

            $('#widget-canvas').on('click', '.delete-widget', function () {
                const widgetId = $(this).data('widget-id');
                if (!confirm('Delete this widget?')) {
                    return;
                }

                const action = widgetDeleteUrlTemplate.replace('__WIDGET__', widgetId);

                $.post(action, {
                    _token: token,
                    _method: 'DELETE',
                }).done(function () {
                    window.location.reload();
                }).fail(function () {
                    alert('Unable to delete widget. Please try again.');
                });
            });
        };

        const loadAndInit = function () {
            if (!window.jQuery) {
                window.setTimeout(loadAndInit, 50);
                return;
            }

            if (typeof window.$.fn.sortable === 'function') {
                initBuilder();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js';
            script.onload = initBuilder;
            script.onerror = initBuilder;
            document.body.appendChild(script);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadAndInit);
        } else {
            loadAndInit();
        }
    })();
</script>
@endif
@endsection
