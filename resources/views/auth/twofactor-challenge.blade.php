@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">Masukkan Kode 2FA</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('twofactor.challenge.verify') }}">
                @csrf
                <div class="form-group">
                    <tags for="code">Kode Authenticator / Backup</tags>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" required maxlength="10">
                    @error('code')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="remember_device" name="remember_device">
                    <tags class="form-check-tags" for="remember_device">
                        Remember this device (30 hari)
                    </tags>
                </div>
                <button type="submit" class="btn btn-primary">Verifikasi</button>
            </form>
        </div>
    </div>
</div>
@endsection
