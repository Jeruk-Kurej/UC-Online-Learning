{{-- --- About builder field (App\View\Components\Cms\Field picks the classes) --- --}}
<div class="{{ $wrapperClass }}">
    <label class="{{ $labelClass }}">{{ $label }}</label>
    @if ($options)
        <select x-model="{{ $model }}" class="{{ $controlClass }}">
            @foreach ($options as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
    @elseif ($rows)
        <textarea x-model="{{ $model }}" rows="{{ $rows }}" class="{{ $controlClass }}"></textarea>
    @else
        <input type="text" x-model="{{ $model }}" class="{{ $controlClass }}">
    @endif
</div>
