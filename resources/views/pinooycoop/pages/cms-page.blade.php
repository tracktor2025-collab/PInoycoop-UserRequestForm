<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>{{ $page->title }}</title>
  <link rel="stylesheet" href="{{ asset('pinooycoop/plugins/bootstrap/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('pinooycoop/plugins/fontawesome/css/all.css') }}">
  <link rel="stylesheet" href="{{ asset('pinooycoop/plugins/icofont/icofont.css') }}">
  <link rel="stylesheet" href="{{ asset('pinooycoop/css/style.css') }}">
</head>
<body data-spy="scroll" data-target="#mainNav">
  @include('pinooycoop.partials.nav')

  @php
    $isHeadline = $page->template === 'headline';
    $isEvent = $page->template === 'event';
  @endphp

  <div class="page-banner-area page-contact" id="page-banner" @if($isHeadline && $page->image_data_uri) style="background-image:url('{{ $page->image_data_uri }}'); background-size:cover; background-position:center;" @endif>
    <div class="overlay dark-overlay"></div>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 m-auto text-center col-sm-12 col-md-12">
          <div class="banner-content content-padding">
            <h1 class="text-white">{{ $page->title }}</h1>
            <p class="text-white">{{ $page->template_label }}</p>
            <p>{{ optional($page->published_at)->format('M d, Y') ?? optional($page->updated_at)->format('M d, Y') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section class="section-padding">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          @if ($page->image_data_uri && ! $isHeadline)
            <div class="mb-4">
              <img src="{{ $page->image_data_uri }}" alt="{{ $page->title }}" class="img-fluid rounded w-100">
            </div>
          @endif
          @if ($isEvent)
            <div class="mb-4">
              <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($page->title) }}" class="btn btn-main" target="_blank" rel="noopener noreferrer">Open Map</a>
            </div>
          @endif
          {!! nl2br(e($page->content ?? '')) !!}
        </div>
      </div>
    </div>
  </section>

  @include('pinooycoop.partials.footer')

  <script src="{{ asset('pinooycoop/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('pinooycoop/plugins/bootstrap/bootstrap.min.js') }}"></script>
  <script src="{{ asset('pinooycoop/js/script.js') }}"></script>
</body>
</html>
