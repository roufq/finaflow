@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">Setup Two-Factor Authentication (TOTP)</h2>

    <div class="card mb-3">
        <div class="card-body">
            <p class="mb-2">Scan the QR code or enter this secret in your authenticator app:</p>
            <div class="mb-3">
                <img src="https://quickchart.io/qr?text={{ urlencode($otpAuth) }}&size=200" alt="QR code for TOTP" width="200" height="200" onerror="this.replaceWith(document.createTextNode('Failed to load QR, use the secret above.'));">
            </div>
            <div class="alert alert-secondary"><strong>Secret:</strong> {{ $secret }}</div>
            <p class="mb-2">URI (copy if needed):</p>
            <code class="d-block mb-3" style="word-break: break-all;">{{ $otpAuth }}</code>
            <p class="small text-muted mb-1">Use Google Authenticator, 1Password, or Authy. Enter the 6-digit code to verify.</p>

            <form method="POST" action="{{ route('twofactor.enable') }}">
                @csrf
                <input type="hidden" name="secret" value="{{ $secret }}">
                <div class="form-group">
                    <label for="code">6-digit code</label>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" maxlength="6" required>
                    @error('code')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Enable 2FA</button>
            </form>
        </div>
    </div>

    @if($twoFactorEnabled && $backupCodes)
        <div class="card">
            <div class="card-header">Backup Codes</div>
            <div class="card-body">
                <p class="small text-muted">Save these codes in a safe place. Each code can only be used once.</p>
                <div class="d-flex flex-wrap">
                    @foreach($backupCodes as $code)
                        <span class="badge badge-light mr-2 mb-2">{{ $code }}</span>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('twofactor.backup.regenerate') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Regenerate Backup Codes</button>
                </form>
                <form method="POST" action="{{ route('twofactor.disable') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Disable 2FA</button>
                </form>
            </div>
    </div>
@endif
</div>
@endsection
