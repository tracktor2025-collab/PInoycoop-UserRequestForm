<!DOCTYPE html>

<!--
 // WEBSITE: https://themefisher.com
 // TWITTER: https://twitter.com/themefisher
 // FACEBOOK: https://facebook.com/themefisher
 // GITHUB: https://github.com/themefisher/
-->

<html lang="en">
<head>
  <base href="{{ url('/pinooycoop') }}/">

  <!-- ** Basic Page Needs ** -->
  <meta charset="utf-8">
  <title>News &amp; Events - MASS-SPECC</title>

  <!-- ** Mobile Specific Metas ** -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Business Bootstrap Template">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="author" content="Themefisher">
  <meta name="generator" content="Themefisher Promodise Template v1.0">
  
  <!-- theme meta -->
  <meta name="theme-name" content="promodise" />
  
  <!-- bootstrap.min css -->
  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <!-- fontawesome css -->
  <link rel="stylesheet" href="plugins/fontawesome/css/all.css">
  <!-- Icofont -->
  <link rel="stylesheet" href="plugins/icofont/icofont.css">

  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="css/style.css">
  <!--Favicon-->
  <link rel="icon" href="images/favicon.png" type="image/x-icon">
  <style>
    body.news-events-page {
      background: #f5f8fc;
      color: #16324f;
    }
    .news-shell {
      padding: 7rem 0 4rem;
    }
    .news-page-head {
      display: flex;
      justify-content: space-between;
      align-items: end;
      gap: 1.5rem;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(22, 50, 79, 0.12);
    }
    .news-page-kicker,
    .segment-label {
      color: #0b80bb;
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: .18em;
      text-transform: uppercase;
      margin-bottom: .65rem;
    }
    .news-page-title {
      font-size: clamp(2rem, 4vw, 3.3rem);
      line-height: 1.05;
      margin-bottom: .35rem;
    }
    .news-page-copy {
      max-width: 620px;
      color: #59708b;
      margin-bottom: 0;
    }
    .news-board {
      display: grid;
      grid-template-columns: minmax(0, 1.55fr) minmax(320px, .9fr);
      gap: 1rem;
      align-items: start;
    }
    .headline-panel,
    .feature-panel,
    .feed-panel,
    .event-panel {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(22, 50, 79, 0.08);
      border-radius: 24px;
      box-shadow: 0 24px 60px rgba(15, 23, 42, 0.07);
    }
    .headline-panel,
    .feed-panel {
      padding: 1.25rem;
      --news-feed-card-height: 168px;
    }
    .feature-panel,
    .event-panel {
      padding: 1.1rem;
    }
    .panel-heading-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: .85rem;
    }
    .panel-heading-row .segment-label {
      margin-bottom: 0;
    }
    .panel-view-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 34px;
      padding: .45rem .75rem;
      border-radius: 999px;
      background: rgba(0, 167, 225, .1);
      color: #0b80bb;
      font-size: .74rem;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      position: relative;
      z-index: 4;
      white-space: nowrap;
    }
    .panel-view-link:hover {
      background: #0b80bb;
      color: #fff;
    }
    .headline-carousel {
      border-radius: 20px;
      overflow: hidden;
      background: linear-gradient(135deg, #0f2742 0%, #173f67 60%, #0b80bb 100%);
      color: #fff;
      height: 360px;
    }
    .headline-carousel .carousel-inner,
    .headline-carousel .carousel-item {
      height: 100%;
    }
    .headline-slide {
      height: 100%;
      position: relative;
    }
    .headline-slide::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, rgba(10, 20, 33, 0.84) 0%, rgba(10, 20, 33, 0.55) 45%, rgba(10, 20, 33, 0.08) 100%);
      z-index: 1;
    }
    .headline-image,
    .mini-thumb {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    .headline-copy {
      position: absolute;
      inset: 0;
      z-index: 2;
      display: flex;
      flex-direction: column;
      justify-content: end;
      padding: 2rem;
      max-width: 62%;
    }
    .headline-copy h2,
    .headline-copy a {
      color: #fff;
    }
    .headline-copy h2 {
      font-size: clamp(1.55rem, 2.4vw, 2.45rem);
      line-height: 1.08;
      margin-bottom: .9rem;
    }
    .headline-copy p {
      color: rgba(255, 255, 255, 0.82);
      font-size: .95rem;
      max-width: 560px;
    }
    .headline-link {
      align-self: flex-start;
      color: #fff;
      font-weight: 600;
      border-bottom: 1px solid rgba(255, 255, 255, 0.4);
      padding-bottom: .15rem;
    }
    .headline-carousel .carousel-indicators {
      margin-bottom: 1rem;
      justify-content: center;
    }
    .headline-carousel .carousel-indicators li {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      border: 0;
      background: rgba(255, 255, 255, 0.4);
    }
    .headline-carousel .carousel-indicators .active {
      background: #fff;
    }
    .stack-grid {
      display: grid;
      gap: 1rem;
    }
    .news-sidebar {
      display: grid;
      gap: 1rem;
      align-self: start;
      position: sticky;
      top: 92px;
    }
    .mini-list {
      display: grid;
      gap: .85rem;
    }
    .mini-article {
      position: relative;
      display: grid;
      grid-template-columns: 172px minmax(0, 1fr);
      gap: 1rem;
      align-items: stretch;
      padding: .85rem;
      border-radius: 18px;
      background: #fff;
      border: 1px solid rgba(22, 50, 79, 0.08);
      transition: transform .18s ease, box-shadow .18s ease;
    }
    .mini-article:hover {
      transform: translateY(-2px);
      box-shadow: 0 18px 38px rgba(15, 23, 42, 0.08);
    }
    .mini-article.is-brief {
      grid-template-columns: 1fr;
      padding: .95rem 1rem;
    }
    .mini-article h4,
    .mini-article h5 {
      margin-bottom: .45rem;
      line-height: 1.3;
    }
    .mini-article p {
      margin-bottom: 0;
      color: #5f738b;
      font-size: .95rem;
      line-height: 1.75;
    }
    .mini-thumb-wrap {
      border-radius: 14px;
      overflow: hidden;
      background: #dfeaf5;
      min-height: 116px;
    }
    .feed-panel .mini-list {
      grid-auto-rows: var(--news-feed-card-height);
    }
    .feed-panel .mini-article {
      height: 100%;
      min-height: 0;
      grid-template-columns: 132px minmax(0, 1fr);
      overflow: hidden;
    }
    .feed-panel .mini-article.is-brief {
      grid-template-columns: 1fr;
      height: 100%;
      padding: .85rem;
    }
    .feed-panel .mini-article.is-brief.has-image {
      grid-template-columns: 132px minmax(0, 1fr);
    }
    .feed-panel .mini-article > div:last-child {
      min-width: 0;
      overflow: hidden;
    }
    .feed-panel .mini-thumb-wrap {
      min-height: 0;
      height: 100%;
    }
    .feed-panel .mini-article h4,
    .feed-panel .mini-article h5,
    .feed-panel .mini-article p {
      display: -webkit-box;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .feed-panel .mini-article h4,
    .feed-panel .mini-article h5 {
      -webkit-line-clamp: 2;
    }
    .feed-panel .mini-article p {
      -webkit-line-clamp: 2;
      line-height: 1.55;
    }
    .meta-line {
      display: flex;
      flex-wrap: wrap;
      gap: .7rem;
      margin-bottom: .55rem;
      color: #6a7d92;
      font-size: .8rem;
    }
    .meta-line .type {
      color: #0b80bb;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
    }
    .stretched-link-anchor {
      position: absolute;
      inset: 0;
      z-index: 2;
      border-radius: inherit;
    }
    .event-card {
      position: relative;
      display: grid;
      gap: 1rem;
      padding: 1rem;
      border-radius: 18px;
      background: #fff;
      border: 1px solid rgba(22, 50, 79, 0.08);
    }
    .event-card .mini-thumb-wrap {
      min-height: 140px;
    }
    .event-actions {
      display: flex;
      gap: .75rem;
      flex-wrap: wrap;
      position: relative;
      z-index: 3;
    }
    .event-actions .btn {
      padding: 11px 18px;
      font-size: 11px;
      border-radius: 999px;
    }
    .btn-map {
      background: #0b80bb;
      color: #fff;
    }
    .btn-map:hover {
      color: #fff;
      background: #08638f;
    }
    .empty-news-state {
      padding: 2rem;
      border-radius: 24px;
      background: #fff;
      border: 1px solid rgba(22, 50, 79, 0.08);
      text-align: center;
    }
    @media (max-width: 991.98px) {
      .news-shell {
        padding-top: 6rem;
      }
      .news-page-head,
      .news-board {
        grid-template-columns: 1fr;
      }
      .news-sidebar {
        position: static;
      }
      .headline-copy {
        max-width: 100%;
        padding: 1.5rem;
      }
    }
    @media (max-width: 575.98px) {
      .mini-article {
        grid-template-columns: 1fr;
      }
      .feed-panel {
        --news-feed-card-height: 190px;
      }
      .feed-panel .mini-article {
        grid-template-columns: 118px minmax(0, 1fr);
      }
      .feed-panel .mini-article.is-brief.has-image {
        grid-template-columns: 118px minmax(0, 1fr);
      }
      .headline-slide {
        height: 320px;
      }
      .headline-copy h2 {
        font-size: 1.55rem;
      }
    }
  </style>

</head><body class="news-events-page" data-spy="scroll" data-target="#mainNav">

@include('pinooycoop.partials.nav')

<section class="news-shell">
  <div class="container">
    @if (empty($items) || $items->isEmpty())
      <div class="empty-news-state">
        <div class="news-page-kicker">Newsroom</div>
        <h1 class="news-page-title">News & Events</h1>
        <p class="news-page-copy mx-auto">Latest published updates from MASS-SPECC.</p>
        <div class="mt-4">
          <h4 class="mb-3">No published news/events yet</h4>
          <p>Create a Page in Admin, choose a news/event display template, then check <strong>Publish</strong>.</p>
        </div>
      </div>
    @else
      @php
        $headlineItems = $items->where('template', 'headline')->values();
        $featureStories = $items->where('template', 'feature_story')->values();
        $events = $items->where('template', 'event')->values();

        // Get ALL standard news and short brief items for the feed section
        $feedItems = $items->whereIn('template', ['standard_news', 'news', 'short_brief'])->values();

        if ($headlineItems->isEmpty()) {
            $headlineItems = $featureStories->take(1)->values();
            $featureStories = $featureStories->slice($headlineItems->count())->values();
        }

        // If no specific feed items, use remaining items (excluding headlines and events)
        if ($feedItems->isEmpty()) {
            $feedItems = $items
                ->reject(function ($item) use ($headlineItems, $events) {
                    return $headlineItems->contains('id', $item->id) || $events->contains('id', $item->id);
                })
                ->values();
        }
      @endphp

      <div class="news-page-head">
        <div>
          <div class="news-page-kicker">Newsroom</div>
          <h1 class="news-page-title">News & Events</h1>
          <p class="news-page-copy">Latest published updates from MASS-SPECC</p>
        </div>
      </div>

      <div class="news-board">
        <div class="stack-grid">
          @if ($headlineItems->isNotEmpty())
            <section class="headline-panel">
              <div class="panel-heading-row">
                <div class="segment-label">Headlines</div>
                <a href="{{ route('pinooycoop.events.category', ['category' => 'headlines']) }}" class="panel-view-link">View all</a>
              </div>
              <div id="headlineCarousel" class="carousel slide headline-carousel" data-ride="carousel">
                <ol class="carousel-indicators">
                  @foreach ($headlineItems as $index => $item)
                    <li data-target="#headlineCarousel" data-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></li>
                  @endforeach
                </ol>
                <div class="carousel-inner">
                  @foreach ($headlineItems as $index => $item)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                      <article class="headline-slide">
                        @if ($item->image_data_uri)
                          <img src="{{ $item->image_data_uri }}" alt="{{ $item->title }}" class="headline-image">
                        @endif
                        <div class="headline-copy">
                          <div class="meta-line">
                            <span class="type">{{ $item->template_label }}</span>
                            <span><i class="far fa-calendar"></i> {{ optional($item->published_at)->format('M d, Y') ?? optional($item->updated_at)->format('M d, Y') }}</span>
                          </div>
                          <h2>{{ $item->title }}</h2>
                          <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->subcontext ?: $item->content ?? ''), 260) }}</p>
                          <a href="{{ route('cms.page', ['slug' => $item->slug]) }}" class="headline-link">Read more</a>
                        </div>
                      </article>
                    </div>
                  @endforeach
                </div>
              </div>
            </section>
          @endif

            @if ($featureStories->isNotEmpty())
              <section class="feature-panel">
                <div class="panel-heading-row">
                  <div class="segment-label">Featured News</div>
                  <a href="{{ route('pinooycoop.events.category', ['category' => 'featured-news']) }}" class="panel-view-link">View all</a>
                </div>
                <div class="mini-list">
                  @foreach ($featureStories as $item)
                    <article class="mini-article">
                      <a href="{{ route('cms.page', ['slug' => $item->slug]) }}" class="stretched-link-anchor" aria-label="{{ $item->title }}"></a>
                      <div class="mini-thumb-wrap">
                        @if ($item->image_data_uri)
                          <img src="{{ $item->image_data_uri }}" alt="{{ $item->title }}" class="mini-thumb">
                        @endif
                      </div>
                      <div>
                        <div class="meta-line">
                          <span class="type">{{ $item->template_label }}</span>
                          <span><i class="far fa-calendar"></i> {{ optional($item->published_at)->format('M d, Y') ?? optional($item->updated_at)->format('M d, Y') }}</span>
                        </div>
                        <h4>{{ $item->title }}</h4>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->subcontext ?: $item->content ?? ''), 135) }}</p>
                      </div>
                    </article>
                  @endforeach
                </div>
              </section>
            @endif
        </div>

        <aside class="news-sidebar">
          <section class="feed-panel">
            <div class="panel-heading-row">
              <div class="segment-label">Standard News & Short Brief</div>
              <a href="{{ route('pinooycoop.events.category', ['category' => 'standard-news-short-brief']) }}" class="panel-view-link">View all</a>
            </div>
            <div class="mini-list">
              @foreach ($feedItems as $item)
                <article class="mini-article {{ $item->template === 'short_brief' ? 'is-brief' : '' }} {{ $item->template === 'short_brief' && $item->image_data_uri ? 'has-image' : '' }}">
                  <a href="{{ route('cms.page', ['slug' => $item->slug]) }}" class="stretched-link-anchor" aria-label="{{ $item->title }}"></a>
                  @if ($item->template !== 'short_brief' || $item->image_data_uri)
                    <div class="mini-thumb-wrap">
                      @if ($item->image_data_uri)
                        <img src="{{ $item->image_data_uri }}" alt="{{ $item->title }}" class="mini-thumb">
                      @endif
                    </div>
                  @endif
                  <div>
                    <div class="meta-line">
                      <span class="type">{{ $item->template_label }}</span>
                      <span><i class="far fa-calendar"></i> {{ optional($item->published_at)->format('M d, Y') ?? optional($item->updated_at)->format('M d, Y') }}</span>
                    </div>
                    @if ($item->template === 'short_brief')
                      <h5>{{ $item->title }}</h5>
                      <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->subcontext ?: $item->content ?? ''), 90) }}</p>
                    @else
                      <h4>{{ $item->title }}</h4>
                      <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->subcontext ?: $item->content ?? ''), 110) }}</p>
                    @endif
                  </div>
                </article>
              @endforeach
            </div>
          </section>

            @if ($events->isNotEmpty())
              <section class="event-panel">
                <div class="segment-label">Events</div>
                <div class="mini-list">
                  @foreach ($events as $item)
                    <article class="event-card">
                      <a href="{{ route('cms.page', ['slug' => $item->slug]) }}" class="stretched-link-anchor" aria-label="{{ $item->title }}"></a>
                      <div class="mini-thumb-wrap">
                        @if ($item->image_data_uri)
                          <img src="{{ $item->image_data_uri }}" alt="{{ $item->title }}" class="mini-thumb">
                        @endif
                      </div>
                      <div>
                        <div class="meta-line">
                          <span class="type">{{ $item->template_label }}</span>
                          <span><i class="far fa-calendar"></i> {{ optional($item->published_at)->format('M d, Y') ?? optional($item->updated_at)->format('M d, Y') }}</span>
                        </div>
                        <h4>{{ $item->title }}</h4>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->subcontext ?: $item->content ?? ''), 120) }}</p>
                        <div class="event-actions">
                          <a href="{{ route('cms.page', ['slug' => $item->slug]) }}" class="btn btn-trans-black">Read more</a>
                          <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($item->title) }}" class="btn btn-map" target="_blank" rel="noopener noreferrer">Map</a>
                        </div>
                      </div>
                    </article>
                  @endforeach
                </div>
              </section>
            @endif
        </aside>
      </div>
    @endif
  </div>
</section>

@include('pinooycoop.partials.footer')
   

    <!-- 
    Essential Scripts
    =====================================-->

    
    <!-- Main jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap css -->
    <script src="plugins/bootstrap/bootstrap.min.js"></script>

    <!-- Google Map -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcABaamniA6OL5YvYSpB3pFMNrXwXnLwU"></script>
    <script src="plugins/google-map/map.js"></script>

    <!-- main script -->
    <script src="js/script.js"></script>

  </body>
  </html>
