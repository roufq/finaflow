@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">{{ __('profile.title') }}</h1>
            <p class="mb-0 text-muted">{{ __('profile.subtitle') }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ __('forms.labels.back') }} {{ __('navigation.dashboard') }}
            </a>
        </div>
    </div>

    @if (session('profileUpdated'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('profileUpdated') }}
            <button type="button" class="close" data-dismiss="alert" aria-tags="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('passwordUpdated'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-shield-alt"></i> {{ session('passwordUpdated') }}
            <button type="button" class="close" data-dismiss="alert" aria-tags="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('profile.basic_information') }}</h6>
                    <span class="badge badge-pill badge-success">
                        <i class="fas fa-user-circle"></i>
                    </span>
                </div>
                <div class="card-body">
                    @php
                        $avatarUrl = $user->avatar_path ? asset('storage/'.$user->avatar_path) : asset('img/undraw_profile.svg');
                    @endphp
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <tags for="name">{{ __('forms.labels.name') }}</tags>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="email">{{ __('forms.labels.email') }}</tags>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="avatar">Profile Photo</tags>
                            <div class="d-flex align-items-center">
                                <img src="{{ $avatarUrl }}" alt="Profile avatar" class="rounded-circle mr-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <div class="flex-fill">
                                    <input type="file" id="avatar" name="avatar" class="form-control-file @error('avatar') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted d-block">JPG/PNG up to 2MB.</small>
                                    @error('avatar')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('profile.actions.update_profile') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">{{ __('profile.security') }}</h6>
                    <i class="fas fa-lock text-danger"></i>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('profile.security_description') }}</p>
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <tags for="current_password">{{ __('forms.labels.current_password') }}</tags>
                            <input type="password" id="current_password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="new_password">{{ __('forms.labels.new_password') }}</tags>
                            <input type="password" id="new_password" name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror" required>
                            @error('new_password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="new_password_confirmation">{{ __('forms.labels.confirm_new_password') }}</tags>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-shield-alt"></i> {{ __('profile.actions.update_password') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="text-uppercase text-muted small">{{ __('profile.profile_completion') }}</span>
                            <h3 class="mb-0">{{ $profileCompletion }}%</h3>
                        </div>
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $profileCompletion }}%;"
                            aria-valuenow="{{ $profileCompletion }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted d-block">{{ __('profile.completion_hint') }}</small>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('profile.stats') }}</h6>
                </div>
                <div class="card-body">
                    @php
                        $statIcons = [
                            'accounts' => 'fas fa-university',
                            'goals' => 'fas fa-bullseye',
                            'subscriptions' => 'fas fa-sync-alt',
                        ];
                    @endphp
                    @foreach ($profileStats as $key => $value)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <span class="text-muted text-uppercase small">{{ __('profile.stats_labels.' . $key) }}</span>
                                <h4 class="mb-0">{{ number_format($value) }}</h4>
                            </div>
                            <span class="badge badge-light">
                                <i class="{{ $statIcons[$key] ?? 'fas fa-chart-line' }} text-primary"></i>
                            </span>
                        </div>
                    @endforeach

                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>{{ __('profile.details.currency') }}:</strong>
                            {{ optional($settings)->currency_symbol ?? '—' }}
                        </li>
                        <li class="mb-2">
                            <strong>{{ __('profile.details.start_month') }}:</strong>
                            {{ optional($settings)->start_month ? ucfirst(optional($settings)->start_month) : '—' }}
                        </li>
                        <li>
                            <strong>{{ __('profile.details.risk_profile') }}:</strong>
                            {{ optional($settings)->risk_profile ? ucfirst(optional($settings)->risk_profile) : '—' }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">{{ __('profile.shortcuts') }}</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('privacy.settings') }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-shield-alt"></i> {{ __('profile.actions.open_privacy') }}
                    </a>
                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-block mb-2">
                        <i class="fas fa-cogs"></i> {{ __('profile.actions.open_settings') }}
                    </a>
                    <button class="btn btn-outline-dark btn-block" type="button" disabled>
                        <i class="fas fa-list"></i> {{ __('profile.actions.open_activity') }}
                    </button>
                    <small class="text-muted d-block mt-2">{{ __('profile.activity_placeholder') }}</small>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">{{ __('profile.security') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-key text-warning"></i> {{ __('profile.security_tips.tip_one') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-user-shield text-warning"></i> {{ __('profile.security_tips.tip_two') }}
                        </li>
                        <li>
                            <i class="fas fa-bell text-warning"></i> {{ __('profile.security_tips.tip_three') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
