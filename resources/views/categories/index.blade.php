@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Category</h1>
            <p class="text-muted small">Kelola klasifikasi pemasukan dan expense Anda.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm px-4">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Add Category
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-4 d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-gray-800">Category List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless text-gray-700" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="py-3">Name Category</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Description</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr class="border-bottom border-light hover-bg-light">
                            <td class="px-4 py-3 align-middle">{{ $loop->iteration }}</td>
                            <td class="py-3 align-middle font-weight-bold text-gray-800">{{ $category->name }}</td>
                            <td class="py-3 align-middle">
                                @if($category->type === 'income')
                                    <span class="badge badge-pill font-weight-normal px-3 py-1" style="background-color: rgba(34, 197, 94, 0.1); color: #16a34a;">Pemasukan</span>
                                @else
                                    <span class="badge badge-pill font-weight-normal px-3 py-1" style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626;">Expense</span>
                                @endif
                            </td>
                            <td class="py-3 align-middle text-muted">{{ $category->description ?: '-' }}</td>
                            <td class="px-4 py-3 align-middle text-right">
                                <div class="btn-group shadow-sm rounded-lg" role="group">
                                    <a href="{{ route('categories.show', $category) }}" class="btn btn-light btn-sm text-primary" title="Lihat"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-light btn-sm text-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm text-danger" title="Delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
