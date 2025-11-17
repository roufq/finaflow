@extends('layouts.app')

@section('title', 'Edit Tag')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Tag</h1>
        <a href="{{ route('tags.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Informasi Tag</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('tags.update', $tag) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Nama Tag <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $tag->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="color">Warna Tag</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-palette"></i>
                                    </span>
                                </div>
                                <input type="color" class="form-control @error('color') is-invalid @enderror"
                                       id="color" name="color" value="{{ old('color', $tag->color) }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $tag->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save fa-sm text-white-50"></i> Update Tag
                            </button>
                            <a href="{{ route('tags.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times fa-sm text-white-50"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Preview Tag</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <span id="tag-preview" class="badge badge-primary px-3 py-2"
                              style="font-size: 1.1rem;">
                            {{ $tag->name }}
                        </span>
                    </div>
                    <hr>
                    <p class="text-muted small">
                        Tag akan muncul seperti di atas pada transaksi yang diberi tag ini.
                    </p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Statistik Tag</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12">
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $tag->getTransactionsCount() }}</div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Transaksi
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted small">
                        Tag ini telah digunakan pada {{ $tag->getTransactionsCount() }} transaksi.
                        Mengubah nama tag tidak akan mempengaruhi transaksi yang sudah ada.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const colorInput = document.getElementById('color');
    const tagPreview = document.getElementById('tag-preview');

    function updatePreview() {
        const name = nameInput.value || 'Nama Tag';
        const color = colorInput.value;

        tagPreview.textContent = name;
        tagPreview.style.backgroundColor = color;

        // Adjust text color for better contrast
        const rgb = hexToRgb(color);
        const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
        tagPreview.style.color = brightness > 128 ? 'black' : 'white';
    }

    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    nameInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);

    // Initial preview
    updatePreview();
});
</script>
@endsection
