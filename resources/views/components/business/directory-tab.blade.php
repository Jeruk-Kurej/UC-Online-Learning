{{-- --- One of the Entrepreneurs / Intrapreneurs tabs above the directory; businessDirectory.isActiveTab() marks the open one --- --}}
@props(['type', 'label'])

<a href="{{ route('businesses.index', ['view' => $type]) }}" @click.prevent="updateType(@js($type), $el.href)" class="tab" :class="{ 'tab-active': isActiveTab(@js($type)) }" :aria-current="isActiveTab(@js($type)) ? 'page' : null">{{ $label }}</a>
