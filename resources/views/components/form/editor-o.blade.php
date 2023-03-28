@once
  @push('head')
      <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
      {{-- <script src="{{ asset('js/tinymce/plugins/tiny_mce_wiris/integration/WIRISplugins.js?viewer=image') }}"></script> --}}
      <script src="{{ asset('https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image') }}"></script>
  @endpush
@endonce

@push('head')
    <script>
        tinymce.init({
            selector: 'textarea#{{ $name }}',
            height: 200,
            menubar: false,
            toolbar: 'undo redo | bold italic underline strikethrough | numlist bullist | superscript subscript | outdent indent | image code table | tiny_mce_wiris_formulaEditor tiny_mce_wiris_formulaEditorChemistry {{ isset($full) ? "| alignleft aligncenter alignright alignjustify | h1 h2 h3 h4 h5 h6" : "" }}',
            plugins: 'code image lists table',
            table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
            // image_list: [
            //     { title: 'My image 1', value: 'https://www.example.com/my1.gif' },
            //     { title: 'My image 2', value: 'http://www.moxiecode.com/my2.gif' }
            // ],
            external_plugins: {
                tiny_mce_wiris: 'https://www.wiris.net/demo/plugins/tiny_mce/plugin.js'
            },
            statusbar: false
        })
    </script>
@endpush

@props(['name', 'value' => null, 'label' => null, 'full' => true])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>
    <textarea name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'border-gray-300 rounded-lg shadow-sm w-full leading-tight']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>