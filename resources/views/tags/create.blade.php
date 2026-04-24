@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Create Tag</h1>
            <p class="text-sm font-medium text-slate-500">Define a new metadata label for granular transaction tracking.</p>
        </div>
        <a href="{{ route('tags.index') }}" class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Form Area -->
        <div class="lg:col-span-8">
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <form action="{{ route('tags.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label for="name" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Tag Label <span class="text-rose-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Business, Personal, Family" required
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('name') ring-rose-500 @enderror">
                            @error('name') <p class="text-[10px] font-bold text-rose-500 ml-1 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Color -->
                        <div class="space-y-2">
                            <label for="color" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Visual Marker</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                    <div id="color-indicator" class="h-6 w-6 rounded-lg shadow-sm border border-white/20" style="background-color: {{ old('color', '#6366f1') }}"></div>
                                </div>
                                <input type="color" id="color" name="color" value="{{ old('color', '#6366f1') }}"
                                       class="w-full h-[52px] rounded-2xl border-none bg-slate-50 pl-14 pr-5 py-2 cursor-pointer ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Operational Context (Optional)</label>
                        <textarea id="description" name="description" rows="4" placeholder="Describe when the AI should apply this tag..."
                                  class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-medium text-slate-900 ring-1 ring-slate-200 transition-all placeholder:text-slate-300 focus:bg-white focus:ring-4 focus:ring-primary-500/10">{{ old('description') }}</textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end border-t border-slate-50">
                        <a href="{{ route('tags.index') }}" class="inline-flex items-center justify-center rounded-2xl px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                            Discard
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                            <i class="fas fa-save mr-2 text-primary-400"></i>
                            Save Tag Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview & Tips Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Preview Card -->
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10 overflow-hidden relative group">
                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-primary-500/10 blur-2xl group-hover:bg-primary-500/20 transition-colors"></div>
                
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-8">Interface Preview</h3>
                <div class="flex flex-col items-center justify-center py-6">
                    <span id="tag-preview" class="inline-flex items-center rounded-xl px-5 py-2 text-sm font-black uppercase tracking-tighter shadow-lg transition-all duration-300 transform group-hover:scale-110" style="background-color: #6366f1; color: white;">
                        Tag Label
                    </span>
                    <p class="mt-8 text-[10px] text-slate-500 text-center font-bold leading-relaxed px-4">
                        This marker will appear across your ledger and AI reports to categorize transactions at a granular level.
                    </p>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6">Strategic Tips</h3>
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                            <i class="fas fa-lightbulb text-xs"></i>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500 leading-relaxed">Tags are non-hierarchical. One transaction can have multiple tags for complex filtering.</p>
                    </li>
                    <li class="flex gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500">
                            <i class="fas fa-palette text-xs"></i>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500 leading-relaxed">Use distinct colors for high-priority categories like <span class="font-bold text-rose-500">Tax Deductible</span> or <span class="font-bold text-emerald-500">Investments</span>.</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const colorInput = document.getElementById('color');
    const tagPreview = document.getElementById('tag-preview');
    const colorIndicator = document.getElementById('color-indicator');

    function updatePreview() {
        const name = nameInput.value || 'Tag Label';
        const color = colorInput.value;

        tagPreview.textContent = name;
        tagPreview.style.backgroundColor = color;
        colorIndicator.style.backgroundColor = color;

        // Adjust text color for better contrast
        const rgb = hexToRgb(color);
        if (rgb) {
            const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
            tagPreview.style.color = brightness > 128 ? '#1e293b' : 'white';
        }
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
@endpush
@endsection
