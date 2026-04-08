<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>readme: {{ $title ?? 'сервис микроблогов' }}</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @stack('styles')
  </head>
  <body class="{{ $bodyClass ?? 'page' }}">
    @include('partials.sprite')

    @if (($headerType ?? 'default') === 'main')
      @include('partials.header-main')
    @else
      @include('partials.header', [
          'activeGuestPage' => $activeGuestPage ?? null,
          'searchQuery' => $searchQuery ?? request('query'),
      ])
    @endif

    @yield('content')

    @include('partials.footer', ['main' => (bool) ($footerMain ?? false)])

    @stack('modals')

    @if (($useDropzone ?? false) === true)
      <script src="{{ asset('libs/dropzone.js') }}"></script>
    @endif

    @if (($useDropzoneHelper ?? false) === true)
      <script src="{{ asset('js/dropzone-settings.js') }}"></script>
    @endif

    @if (($useMainScript ?? false) === true)
      <script src="{{ asset('js/main.js') }}"></script>
    @endif

    @stack('scripts')
  </body>
</html>
