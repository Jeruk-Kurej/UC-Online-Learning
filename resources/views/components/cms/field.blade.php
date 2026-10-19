{{-- --- About builder field bound to Alpine with x-model: a text input, a textarea (rows) or a select (options) --- --}}
@props(['label', 'model', 'rows' => null, 'options' => []])

<div>
    <label class="field-label">{{ $label }}</label>
    @if ($options)
        <select x-model="{{ $model }}" class="field-input">
            @foreach ($options as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
    @elseif ($rows)
        <textarea x-model="{{ $model }}" rows="{{ $rows }}" class="field-input"></textarea>
    @else
        <input type="text" x-model="{{ $model }}" class="field-input">
    @endif
</div>
