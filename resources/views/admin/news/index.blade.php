@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Manage Financial News</h1>
            <p class="text-muted small mb-0">Curate or add news so halaman publik no kosong.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="btn btn-sm btn-primary">Create News</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-header">News</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Source</th>
                            <th>Category</th>
                            <th>Published</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->source ?? '-' }}</td>
                                <td>{{ $item->category ?? '-' }}</td>
                                <td>{{ $item->published_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.news.show', $item) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                    <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.news.destroy', $item) }}" class="d-inline ml-2" onsubmit="return confirm('Delete news?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No news yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($news->hasPages())
            <div class="card-footer">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
