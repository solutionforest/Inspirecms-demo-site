@env(['production', 'local'])
  @php
      $gtagId = config('services.google.gtag_id', '');
  @endphp
  @if (!empty($gtagId))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagId }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', @js($gtagId));
    </script>
  @endif
@endenv
