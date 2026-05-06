<!DOCTYPE html>
<html lang="en">
<head>
  <base href="{{ url('/pinooycoop') }}/">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>{{ $page->seo_title ?: $page->title }} - MASS-SPECC</title>
  @if ($page->seo_description)
    <meta name="description" content="{{ $page->seo_description }}">
  @endif
  @if ($page->seo_keywords)
    <meta name="keywords" content="{{ $page->seo_keywords }}">
  @endif
  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/fontawesome/css/all.css">
  <link rel="stylesheet" href="plugins/icofont/icofont.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .cms-article-banner {
      --cms-article-banner-height: 560px;
      height: var(--cms-article-banner-height);
      padding: 0 !important;
      background-color: #1f3d7c;
      overflow: hidden;
    }

    .cms-article-hero-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1.02fr);
      height: var(--cms-article-banner-height);
    }

    .cms-article-hero-image {
      position: relative;
      height: var(--cms-article-banner-height);
      background-color: #0f2947;
      background-image: linear-gradient(135deg, #102c54 0%, #0b80bb 100%);
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
    }

    .cms-article-hero-image:after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, rgba(10, 29, 54, .04) 0%, rgba(31, 61, 124, .16) 100%);
    }

    .cms-article-hero-copy {
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: var(--cms-article-banner-height);
      padding: 105px 7vw 58px 48px;
      background: #244967;
      color: #fff;
      overflow: hidden;
    }

    .cms-article-kicker {
      display: block;
      margin-bottom: 18px;
      color: #fff;
      font-family: "Poppins", sans-serif;
      font-size: 20px;
      font-weight: 700;
      letter-spacing: 1px;
      line-height: 1.2;
      text-transform: uppercase;
    }

    .cms-article-banner h1 {
      max-width: 880px;
      margin: 0;
      color: #57cfff;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(38px, 4.1vw, 64px);
      font-weight: 700;
      letter-spacing: 0;
      line-height: .99;
      text-transform: none;
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 3;
    }

    .cms-article-subtitle {
      max-width: 780px;
      margin-top: 22px;
      color: #fff;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(21px, 1.7vw, 30px);
      font-weight: 700;
      letter-spacing: 0;
      line-height: 1.23;
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
    }

    .cms-article-rule {
      width: 60px;
      height: 4px;
      margin: 24px 0 20px;
      background: #00a7e1;
    }

    .cms-article-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 14px 18px;
      margin-top: 0;
      color: #fff;
      font-size: 17px;
      line-height: 1.5;
    }

    .cms-article-meta span {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: inherit;
    }

    .cms-article-meta .cms-meta-break {
      flex-basis: 100%;
      height: 0;
    }

    .cms-article-shell {
      background: linear-gradient(180deg, #f4f8fb 0%, #fff 38%);
    }

    .cms-article-layout {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 360px;
      gap: 34px;
      align-items: start;
    }

    .cms-article-card {
      margin-top: 0;
      padding: 44px;
      background: #fff;
      border: 1px solid rgba(31, 61, 124, .08);
      box-shadow: 0 24px 70px rgba(31, 61, 124, .12);
      position: relative;
      z-index: 3;
    }

    .cms-recent-sidebar {
      position: sticky;
      top: 96px;
      padding: 28px 0 0 16px;
      border-left: 3px solid #00a7e1;
    }

    .cms-recent-title {
      color: #20242a;
      font-family: "Poppins", sans-serif;
      font-size: 32px;
      font-weight: 700;
      line-height: 1.15;
      margin-bottom: 34px;
    }

    .cms-recent-title:after {
      content: "";
      display: block;
      width: 150px;
      height: 3px;
      margin-top: 10px;
      background: #00a7e1;
    }

    .cms-recent-post {
      overflow: hidden;
      margin-bottom: 26px;
      background: #fff;
      border: 1px solid #e3eaf0;
      box-shadow: 0 14px 34px rgba(31, 61, 124, .08);
    }

    .cms-recent-image {
      display: block;
      aspect-ratio: 16 / 9;
      background: linear-gradient(135deg, #1f3d7c 0%, #00a7e1 100%);
      overflow: hidden;
    }

    .cms-recent-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .22s ease;
    }

    .cms-recent-post:hover .cms-recent-image img {
      transform: scale(1.04);
    }

    .cms-recent-body {
      padding: 22px 18px 24px;
    }

    .cms-recent-heading {
      margin: 0 0 12px;
      color: #0f2947;
      font-size: 19px;
      font-weight: 700;
      line-height: 1.25;
    }

    .cms-recent-heading a {
      color: inherit;
    }

    .cms-recent-heading a:hover {
      color: #00a7e1;
    }

    .cms-recent-excerpt {
      color: #4f5d61;
      font-size: 15px;
      line-height: 1.7;
      margin-bottom: 8px;
    }

    .cms-recent-readmore {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #00a7e1;
      font-size: 15px;
      font-weight: 600;
      margin-bottom: 14px;
    }

    .cms-recent-readmore:hover {
      color: #1f3d7c;
    }

    .cms-recent-date {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #7e8b93;
      font-size: 14px;
      line-height: 1.4;
    }

    .cms-article-card:before {
      content: "";
      display: block;
      width: 72px;
      height: 4px;
      margin-bottom: 28px;
      background: #00a7e1;
    }

    .cms-article-content {
      color: #4f5d61;
      font-size: 17px;
      line-height: 1.85;
    }

    .cms-article-content p {
      color: inherit;
      font-size: inherit;
      line-height: inherit;
      margin-bottom: 18px;
    }

    .cms-article-content h2 {
      color: #0f2947;
      font-size: 28px;
      line-height: 1.25;
      margin: 28px 0 14px;
      font-weight: 800;
    }

    .cms-article-content blockquote {
      margin: 24px 0;
      padding: 18px 20px;
      border-left: 4px solid #00a7e1;
      background: #f3f9fc;
      color: #244265;
      font-size: 18px;
      font-weight: 600;
      line-height: 1.65;
    }

    .cms-article-content .cms-builder-cta {
      display: inline-flex;
      align-items: center;
      margin: 12px 0 24px;
      padding: 12px 18px;
      border-radius: 8px;
      background: #00a7e1;
      color: #fff;
      font-weight: 800;
      line-height: 1.3;
    }

    .cms-article-content .cms-builder-divider {
      border: 0;
      border-top: 1px solid #dbe5ee;
      margin: 30px 0;
    }

    .cms-article-content .cms-builder-image {
      margin: 28px 0 30px;
      overflow: hidden;
      border-radius: 10px;
      border: 1px solid #e3eaf0;
      background: #f4f8fb;
      box-shadow: 0 16px 42px rgba(31, 61, 124, .10);
    }

    .cms-article-content .cms-builder-image img {
      display: block;
      width: 100%;
      max-height: 560px;
      object-fit: cover;
    }

    .cms-article-content .cms-builder-image figcaption {
      padding: 10px 14px;
      color: #607993;
      font-size: 14px;
      line-height: 1.5;
      background: #fff;
    }

    .cms-article-content .cms-builder-gallery {
      margin: 28px 0 30px;
    }

    .cms-article-content .cms-builder-gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 14px;
    }

    .cms-article-content .cms-builder-gallery-grid img {
      display: block;
      width: 100%;
      aspect-ratio: 4 / 3;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid #e3eaf0;
      background: #f4f8fb;
      box-shadow: 0 12px 30px rgba(31, 61, 124, .10);
    }

    .cms-article-content .cms-builder-gallery figcaption {
      padding: 10px 2px 0;
      color: #607993;
      font-size: 14px;
      line-height: 1.5;
    }

    .cms-article-sub-image {
      margin: 28px 0 30px;
      overflow: hidden;
      border-radius: 10px;
      border: 1px solid #e3eaf0;
      background: #f4f8fb;
      box-shadow: 0 16px 42px rgba(31, 61, 124, .10);
    }

    .cms-article-sub-image img {
      display: block;
      width: 100%;
      max-height: 520px;
      object-fit: cover;
    }

    .cms-article-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 32px;
      padding-top: 28px;
      border-top: 1px solid #e8eef3;
    }

    .cms-article-actions .btn-main {
      background: #00a7e1;
      color: #fff;
    }

    .cms-article-actions .btn-main:hover {
      background: #1f3d7c;
      color: #fff;
    }

    .cms-article-actions .btn-outline-main {
      border-color: rgba(31, 61, 124, .2);
      color: #1f3d7c;
      background: #fff;
    }

    .cms-article-actions .btn-outline-main:hover {
      border-color: #00a7e1;
      color: #00a7e1;
    }

    @media (max-width: 767.98px) {
      .cms-article-hero-grid {
        grid-template-columns: 1fr;
        grid-template-rows: 40% 60%;
        height: var(--cms-article-banner-height);
      }

      .cms-article-hero-image {
        height: 100%;
      }

      .cms-article-hero-copy {
        height: 100%;
        padding: 26px 24px 30px;
      }

      .cms-article-kicker {
        font-size: 13px;
        margin-bottom: 12px;
      }

      .cms-article-banner h1 {
        font-size: 30px;
        line-height: 1.04;
      }

      .cms-article-subtitle {
        margin-top: 14px;
        font-size: 19px;
        line-height: 1.2;
      }

      .cms-article-meta {
        gap: 8px 10px;
        font-size: 13px;
      }

      .cms-article-rule {
        margin: 16px 0 12px;
      }

      .cms-article-card {
        padding: 30px 22px;
      }

      .cms-article-layout {
        grid-template-columns: 1fr;
      }

      .cms-recent-sidebar {
        position: static;
        padding-left: 14px;
      }
    }

    @media (max-width: 991.98px) {
      .cms-article-layout {
        grid-template-columns: 1fr;
      }

      .cms-recent-sidebar {
        position: static;
      }
    }
  </style>
</head>
<body data-spy="scroll" data-target="#mainNav">
  @include('pinooycoop.partials.nav')
    @php
      $isEvent = $page->template === 'event';
      $builderSettings = $page->builder_settings ?? [];
      $layoutWidth = $builderSettings['layout_width'] ?? 'default';
      $showRecentPosts = $builderSettings['show_recent_posts'] ?? true;
      $enableArticleActions = $builderSettings['enable_article_actions'] ?? true;
      $bannerImage = $page->image_data_uri;
    $publishedDate = optional($page->published_at)->format('M d, Y') ?? optional($page->updated_at)->format('M d, Y');
    $subcontext = \Illuminate\Support\Str::of($page->subcontext ?? '')->squish();
    $contentParagraphs = preg_split('/\R{2,}/', trim((string) ($page->content ?? ''))) ?: [];
    $contentParagraphs = array_values(array_filter($contentParagraphs, fn ($paragraph) => trim($paragraph) !== ''));
    $articleSubImages = $page->subImages->values();
    $subImagesBySlot = [];

    if ($articleSubImages->isNotEmpty()) {
      $slotCount = max(1, count($contentParagraphs));
      $slots = range(0, $slotCount - 1);
      mt_srand((int) $page->id);
      shuffle($slots);
      mt_srand();

      foreach ($articleSubImages as $imageIndex => $subImage) {
        $slot = $slots[$imageIndex % count($slots)];
        $subImagesBySlot[$slot][] = $subImage;
      }
    }
  @endphp

  <div class="page-banner-area page-contact cms-article-banner" id="page-banner">
    <div class="cms-article-hero-grid">
      <div class="cms-article-hero-image" @if($bannerImage) style="background-image:url('{{ $bannerImage }}');" @endif></div>
      <div class="cms-article-hero-copy">
        <span class="cms-article-kicker">{{ $page->template_label }}</span>
        <h1>{{ $page->title }}</h1>
        @if($subcontext->isNotEmpty())
          <div class="cms-article-subtitle">{{ $subcontext }}</div>
        @endif
        <div class="cms-article-rule"></div>
        <div class="cms-article-meta">
          <span>MASS-SPECC</span>
          <span class="cms-meta-break"></span>
          @if($publishedDate)
            <span>{{ $publishedDate }}</span>
          @endif
          <span>|</span>
          <span>{{ max(1, ceil(str_word_count(strip_tags($page->content ?? '')) / 200)) }} min read</span>
        </div>
      </div>
    </div>
  </div>

  <section class="section-padding cms-article-shell">
      <div class="{{ $layoutWidth === 'wide' ? 'container-fluid' : 'container' }}">
        <div class="cms-article-layout">
        <article class="cms-article-card">
          <div class="cms-article-content">
            @forelse ($contentParagraphs as $paragraphIndex => $paragraph)
              @php
                $trimmedParagraph = trim($paragraph);
              @endphp

              @if ($trimmedParagraph === '---')
                <hr class="cms-builder-divider">
              @elseif (\Illuminate\Support\Str::startsWith($trimmedParagraph, '# '))
                <h2>{{ \Illuminate\Support\Str::after($trimmedParagraph, '# ') }}</h2>
              @elseif (\Illuminate\Support\Str::startsWith($trimmedParagraph, '> '))
                <blockquote>{!! nl2br(e(\Illuminate\Support\Str::after($trimmedParagraph, '> '))) !!}</blockquote>
              @elseif (\Illuminate\Support\Str::startsWith($trimmedParagraph, '[CTA] '))
                <div class="cms-builder-cta">{{ \Illuminate\Support\Str::after($trimmedParagraph, '[CTA] ') }}</div>
              @elseif (\Illuminate\Support\Str::startsWith($trimmedParagraph, '[IMAGE] '))
                @php
                  $imageLines = preg_split('/\R/', $trimmedParagraph) ?: [];
                  $builderImagePath = trim((string) \Illuminate\Support\Str::after(array_shift($imageLines), '[IMAGE] '));
                  $builderImageCaption = trim(implode("\n", $imageLines));
                @endphp
                @if ($builderImagePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($builderImagePath))
                  <figure class="cms-builder-image">
                    <img src="{{ route('pinoycoop.media.show', ['path' => $builderImagePath]) }}" alt="{{ $builderImageCaption ?: $page->title }}">
                    @if ($builderImageCaption)
                      <figcaption>{{ $builderImageCaption }}</figcaption>
                    @endif
                  </figure>
                @endif
              @elseif (\Illuminate\Support\Str::startsWith($trimmedParagraph, '[GALLERY] '))
                @php
                  $galleryLines = preg_split('/\R/', $trimmedParagraph) ?: [];
                  $builderGalleryPaths = collect(explode('|', (string) \Illuminate\Support\Str::after(array_shift($galleryLines), '[GALLERY] ')))
                    ->map(fn ($path) => trim((string) $path))
                    ->filter(fn ($path) => $path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path))
                    ->values();
                  $builderGalleryCaption = trim(implode("\n", $galleryLines));
                @endphp
                @if ($builderGalleryPaths->isNotEmpty())
                  <figure class="cms-builder-gallery">
                    <div class="cms-builder-gallery-grid">
                      @foreach ($builderGalleryPaths as $galleryPath)
                        <img src="{{ route('pinoycoop.media.show', ['path' => $galleryPath]) }}" alt="{{ $builderGalleryCaption ?: $page->title }}">
                      @endforeach
                    </div>
                    @if ($builderGalleryCaption)
                      <figcaption>{{ $builderGalleryCaption }}</figcaption>
                    @endif
                  </figure>
                @endif
              @else
                <p>{!! nl2br(e($paragraph)) !!}</p>
              @endif

              @foreach (($subImagesBySlot[$paragraphIndex] ?? []) as $subImage)
                @if ($subImage->image_data_uri)
                  <figure class="cms-article-sub-image">
                    <img src="{{ $subImage->image_data_uri }}" alt="{{ $page->title }} sub image">
                  </figure>
                @endif
              @endforeach
            @empty
              <p>No content available.</p>
            @endforelse
          </div>

          @if ($enableArticleActions)
            <div class="cms-article-actions">
              <a href="{{ route('pinooycoop.events') }}" class="btn btn-outline-main">Back to Articles</a>
              @if ($isEvent)
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($page->title) }}" class="btn btn-main" target="_blank" rel="noopener noreferrer">Open Map</a>
              @endif
            </div>
          @endif
        </article>

        @if($showRecentPosts && $recentPosts->isNotEmpty())
          <aside class="cms-recent-sidebar" aria-label="Recent posts">
            <h2 class="cms-recent-title">Recent Post</h2>
            @foreach($recentPosts as $recentPost)
              @php
                $recentDate = optional($recentPost->published_at)->format('F d, Y') ?? optional($recentPost->updated_at)->format('F d, Y');
                $recentExcerpt = \Illuminate\Support\Str::limit(\Illuminate\Support\Str::of(strip_tags($recentPost->subcontext ?: $recentPost->content ?: ''))->squish(), 120);
              @endphp
              <article class="cms-recent-post">
                <a class="cms-recent-image" href="{{ route('cms.page', $recentPost->slug) }}" aria-label="{{ $recentPost->title }}">
                  @if($recentPost->image_data_uri)
                    <img src="{{ $recentPost->image_data_uri }}" alt="{{ $recentPost->title }}">
                  @endif
                </a>
                <div class="cms-recent-body">
                  <h3 class="cms-recent-heading">
                    <a href="{{ route('cms.page', $recentPost->slug) }}">{{ $recentPost->title }}</a>
                  </h3>
                  @if($recentExcerpt)
                    <p class="cms-recent-excerpt">{{ $recentExcerpt }}</p>
                  @endif
                  <a class="cms-recent-readmore" href="{{ route('cms.page', $recentPost->slug) }}">Read More</a>
                  @if($recentDate)
                    <div class="cms-recent-date"><i class="far fa-clock"></i> {{ $recentDate }}</div>
                  @endif
                </div>
              </article>
            @endforeach
          </aside>
        @endif
      </div>
    </div>
  </section>

  @include('pinooycoop.partials.footer')

  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/bootstrap.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
