{{-- --- Head tags every page shares: charset, viewport, CSRF token, the title (slot) and the favicon --- --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $slot }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo-uco-64.png') }}">

{{-- --- Fonts: Poppins and Bootstrap Icons come from this site (resources/css/base/fonts.css); the two weights used above the fold are preloaded --- --}}
<link rel="preload" href="{{ Vite::asset('node_modules/@fontsource/poppins/files/poppins-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{ Vite::asset('node_modules/@fontsource/poppins/files/poppins-latin-600-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
