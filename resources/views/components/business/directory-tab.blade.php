{{-- --- One side of the Entrepreneurs / Intrapreneurs switch of the directory; businessDirectory.isActiveTab() marks the open one --- --}}
@props(['type', 'label'])

<a href="{{ route('businesses.index', ['view' => $type]) }}" @click.prevent="updateType(@js($type), $el.href)" class="segment" :class="{ 'segment-active': isActiveTab(@js($type)) }" :aria-current="isActiveTab(@js($type)) ? 'page' : null">{{ $label }}</a>
