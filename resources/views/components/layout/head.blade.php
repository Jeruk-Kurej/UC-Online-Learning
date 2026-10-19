{{-- --- Head tags every page shares: charset, viewport, CSRF token, the title (slot) and the favicon --- --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $slot }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo-uco.png') }}">

{{-- --- Fonts: Poppins, as on online.ciputra.ac.id; icons: Bootstrap Icons --- --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
