@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Goal: {{ $goal->name }}</h1>
        <a href="{{ route('goals.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Goal</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('goals.update', $goal) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Nama Goal *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $goal->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $goal->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="category">Kategori *</label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="emergency_fund" {{ old('category', $goal->category) == 'emergency_fund' ? 'selected' : '' }}>Dana Darurat</option>
                                    <option value="vacation" {{ old('category', $goal->category) == 'vacation' ? 'selected' : '' }}>Liburan</option>
                                    <option value="house_down_payment" {{ old('category', $goal->category) == 'house_down_payment' ? 'selected' : '' }}>Uang Muka Rumah</option>
                                    <option value="car_purchase" {{ old('category', $goal->category) == 'car_purchase' ? 'selected' : '' }}>Pembelian Mobil</option>
                                    <option value="education" {{ old('category', $goal->category) == 'education' ? 'selected' : '' }}>Pendidikan</option>
                                    <option value="retirement" {{ old('category', $goal->category) == 'retirement' ? 'selected' : '' }}>Pensiun</option>
                                    <option value="investment" {{ old('category', $goal->category) == 'investment' ? 'selected' : '' }}>Investasi</option>
                                    <option value="debt_payoff" {{ old('category', $goal->category) == 'debt_payoff' ? 'selected' : '' }}>Pelunasan Hutang</option>
                                    <option value="business" {{ old('category', $goal->category) == 'business' ? 'selected' : '' }}>Bisnis</option>
                                    <option value="other" {{ old('category', $goal->category) == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="type">Tipe Goal *</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="short_term" {{ old('type', $goal->type) == 'short_term' ? 'selected' : '' }}>Jangka Pendek (≤ 1 tahun)</option>
                                    <option value="medium_term" {{ old('type', $goal->type) == 'medium_term' ? 'selected' : '' }}>Jangka Menengah (1-5 tahun)</option>
                                    <option value="long_term" {{ old('type', $goal->type) == 'long_term' ? 'selected' : '' }}>Jangka Panjang (> 5 tahun)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="target_amount">Target Jumlah (Rp) *</label>
                                <input type="number" class="form-control @error('target_amount') is-invalid @enderror" id="target_amount" name="target_amount" value="{{ old('target_amount', $goal->target_amount) }}" min="0" required>
                                @error('target_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="current_amount">Jumlah Saat Ini (Rp) *</label>
                                <input type="number" class="form-control @error('current_amount') is-invalid @enderror" id="current_amount" name="current_amount" value="{{ old('current_amount', $goal->current_amount) }}" min="0" required>
                                @error('current_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="target_date">Target Tanggal *</label>
                                <input type="date" class="form-control @error('target_date') is-invalid @enderror" id="target_date" name="target_date" value="{{ old('target_date', $goal->target_date->format('Y-m-d')) }}" required>
                                @error('target_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $goal->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="completed" {{ old('status', $goal->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="paused" {{ old('status', $goal->status) == 'paused' ? 'selected' : '' }}>Ditunda</option>
                                <option value="cancelled" {{ old('status', $goal->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Goal</button>
                            <a href="{{ route('goals.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
