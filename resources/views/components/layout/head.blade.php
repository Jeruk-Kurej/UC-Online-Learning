{{-- --- Head tags every page shares: charset, viewport, CSRF token, the title (slot) and the favicon --- --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $slot }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo-uco.png') }}">
