@props(['book' => null, 'categories'])

<div class="space-y-5 max-w-2xl">
    <div>
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Title</label>
        <input type="text" name="title" value="{{ old('title', $book?->title) }}" required
               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Author</label>
            <input type="text" name="author" value="{{ old('author', $book?->author) }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">ISBN</label>
            <input type="text" name="isbn" value="{{ old('isbn', $book?->isbn) }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Category</label>
            <select name="category_id" class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="">Uncategorized</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (int) old('category_id', $book?->category_id) === $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Price (GHS)</label>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $book?->price) }}" required
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Description</label>
        <textarea name="description" rows="4"
                  class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                  style="border-radius: 2px;">{{ old('description', $book?->description) }}</textarea>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Cover Image</label>
            @if($book?->hasCover())
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ $book->coverUrl() }}" alt="" class="w-16 h-24 object-cover" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                    <label class="inline-flex items-center gap-2 text-xs text-red-500">
                        <input type="checkbox" name="remove_cover_image" value="1" style="border-radius: 2px;">
                        Remove current cover
                    </label>
                </div>
            @endif
            <input type="file" name="cover_image" accept="image/png,image/jpeg,image/webp"
                   class="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-3 file:px-4 file:py-2 file:border-0 file:rounded-[2px] file:bg-purple-600 file:text-white file:text-sm file:font-semibold file:cursor-pointer">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">JPG, PNG, or WebP, up to 4MB. Shown on the customer catalog and product page.</p>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Preview PDF (optional)</label>
            @if($book?->hasPreview())
                <div class="flex items-center gap-3 mb-3">
                    <a href="{{ $book->previewUrl() }}" target="_blank" rel="noopener" class="text-sm text-purple-600 dark:text-purple-400 font-medium">View current preview &rarr;</a>
                    <label class="inline-flex items-center gap-2 text-xs text-red-500">
                        <input type="checkbox" name="remove_preview_pdf" value="1" style="border-radius: 2px;">
                        Remove
                    </label>
                </div>
            @endif
            <input type="file" name="preview_pdf" accept="application/pdf"
                   class="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-3 file:px-4 file:py-2 file:border-0 file:rounded-[2px] file:bg-purple-600 file:text-white file:text-sm file:font-semibold file:cursor-pointer">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">PDF, up to 20MB. A sample chapter or excerpt customers can open from the product page.</p>
        </div>
    </div>

    <button type="submit"
            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
            style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
        {{ $book ? 'Save Changes' : 'Add Book' }}
    </button>
</div>
