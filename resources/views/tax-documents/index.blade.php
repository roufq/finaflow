@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Tax Records</h1>
            <p class="text-sm font-medium text-slate-500">Secure digital archive for your annual tax filings and supporting documents</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('tax-documents.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Upload Document
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="relative overflow-hidden rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Document Ledger -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Document Storage</h2>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Encrypted Repository</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Fiscal Year</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Document Title</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Classification</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Ingested At</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($documents as $document)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-1 text-[10px] font-black text-white uppercase tracking-tighter">
                                {{ $document->year }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-500 shadow-sm ring-1 ring-rose-100">
                                    <i class="fas fa-file-pdf text-[10px]"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-900">{{ $document->title }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-tight">{{ $document->category ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-slate-400">{{ $document->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('tax-documents.show', $document) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600 transition-all" title="View Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ Storage::disk('public')->url($document->file_path) }}" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all" title="Download Document">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                                <a href="{{ route('tax-documents.edit', $document) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all" title="Edit Meta">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('tax-documents.destroy', $document) }}" method="POST" class="inline" onsubmit="return confirm('Securely delete this tax document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all" title="Delete Permanent">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                                    <i class="fas fa-file-invoice text-2xl"></i>
                                </div>
                                <p class="text-xs text-slate-400 font-medium italic">No tax documentation has been uploaded for storage.</p>
                                <a href="{{ route('tax-documents.create') }}" class="text-xs font-bold text-primary-600 hover:underline hover:text-primary-700 transition-colors">Start Uploading</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
