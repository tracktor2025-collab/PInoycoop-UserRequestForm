<!DOCTYPE html>
<html lang="en">
<head>
  <base href="{{ url('/pinooycoop') }}/">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>{{ $category['label'] }} - News &amp; Events - MASS-SPECC</title>
  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/fontawesome/css/all.css">
  <link rel="stylesheet" href="plugins/icofont/icofont.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="images/favicon.png" type="image/x-icon">
  <style>
    body.news-category-page {
      background: #f5f8fc;
      color: #16324f;
    }
    .category-shell {
      padding: 7rem 0 4rem;
    }
    .category-head {
      display: flex;
      align-items: end;
      justify-content: space-between;
      gap: 1.5rem;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(22, 50, 79, .12);
    }
    .category-kicker,
    .category-card-type {
      color: #0b80bb;
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: .16em;
      text-transform: uppercase;
    }
    .category-back-link {
      display: inline-flex;
      margin-bottom: .85rem;
    }
    .category-title {
      font-size: clamp(2rem, 4vw, 3.25rem);
      line-height: 1.05;
      margin: .55rem 0 .35rem;
    }
    .category-copy {
      max-width: 650px;
      color: #59708b;
      margin-bottom: 0;
    }
    .category-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 1rem;
    }
    .category-filter-panel {
      margin-bottom: 1.3rem;
      border-radius: 18px;
      background: #fff;
      border: 1px solid rgba(22, 50, 79, .08);
      box-shadow: 0 16px 38px rgba(15, 23, 42, .05);
      overflow: hidden;
    }
    .category-filter-toggle {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      min-height: 58px;
      padding: 0 1rem;
      color: #16324f;
      cursor: pointer;
      font-size: .95rem;
      font-weight: 800;
      list-style: none;
    }
    .category-filter-toggle::-webkit-details-marker {
      display: none;
    }
    .category-filter-toggle:after {
      content: "\f078";
      color: #0b80bb;
      font-family: "Font Awesome 5 Free";
      font-size: .85rem;
      font-weight: 900;
      transition: transform .18s ease;
    }
    .category-filter-panel[open] .category-filter-toggle:after {
      transform: rotate(180deg);
    }
    .category-filter {
      display: grid;
      grid-template-columns: minmax(0, 620px) auto;
      align-items: start;
      justify-content: space-between;
      gap: 1rem;
      padding: 0 1rem 1rem;
    }
    .category-filter-field {
      position: relative;
      display: grid;
      gap: .3rem;
    }
    .category-filter label {
      display: grid;
      gap: .3rem;
      margin: 0;
      color: #59708b;
      font-size: .86rem;
      font-weight: 700;
    }
    .category-date-grid {
      display: grid;
      grid-template-columns: 1.25fr .7fr .9fr;
      gap: .65rem;
    }
    .category-filter select {
      height: 48px;
      padding: 0 .9rem;
      border: 1px solid #d7e3ee;
      border-radius: 14px;
      background: linear-gradient(180deg, #fbfdff, #f4f8fb);
      color: #16324f;
      font-size: .95rem;
      font-weight: 700;
      color-scheme: light;
      transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }
    .category-filter select:hover,
    .category-filter select:focus {
      border-color: rgba(0, 167, 225, .65);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(0, 167, 225, .12);
      outline: none;
    }
    .category-filter-actions {
      display: flex;
      flex-wrap: wrap;
      gap: .6rem;
      align-items: center;
      padding-top: 1.62rem;
    }
    .category-filter-actions .btn {
      min-height: 48px;
      padding: 0 2.6rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
    }
    .category-filter-hint {
      color: #7d8fa3;
      font-size: .78rem;
      font-weight: 500;
    }
    .category-card {
      display: flex;
      flex-direction: column;
      min-height: 420px;
      overflow: hidden;
      border-radius: 20px;
      background: #fff;
      border: 1px solid rgba(22, 50, 79, .08);
      box-shadow: 0 22px 54px rgba(15, 23, 42, .07);
      transition: transform .18s ease, box-shadow .18s ease;
    }
    .category-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 28px 68px rgba(15, 23, 42, .11);
    }
    .category-image {
      height: 190px;
      background: linear-gradient(135deg, #173f67 0%, #0b80bb 100%);
      overflow: hidden;
    }
    .category-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    .category-card-body {
      display: flex;
      flex: 1;
      flex-direction: column;
      padding: 1.15rem;
    }
    .category-meta {
      display: flex;
      flex-wrap: wrap;
      gap: .7rem;
      margin: .55rem 0 .7rem;
      color: #6a7d92;
      font-size: .82rem;
    }
    .category-card h3 {
      font-size: 1.18rem;
      line-height: 1.32;
      margin-bottom: .65rem;
    }
    .category-card p {
      color: #5f738b;
      font-size: .94rem;
      line-height: 1.7;
      margin-bottom: 1rem;
    }
    .category-card h3,
    .category-card p {
      display: -webkit-box;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .category-card h3 {
      -webkit-line-clamp: 2;
    }
    .category-card p {
      -webkit-line-clamp: 3;
    }
    .category-read {
      align-self: flex-start;
      margin-top: auto;
      color: #0b80bb;
      font-weight: 700;
    }
    .category-empty {
      padding: 2rem;
      border-radius: 20px;
      background: #fff;
      border: 1px solid rgba(22, 50, 79, .08);
      text-align: center;
    }
    .category-pager {
      margin-top: 2rem;
    }
    .category-pager nav {
      display: flex;
      justify-content: center;
    }
    @media (max-width: 991.98px) {
      .category-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }
    @media (max-width: 575.98px) {
      .category-shell {
        padding-top: 6rem;
      }
      .category-head {
        align-items: start;
        flex-direction: column;
      }
      .category-filter {
        align-items: stretch;
        grid-template-columns: 1fr;
      }
      .category-date-grid {
        grid-template-columns: 1fr;
      }
      .category-filter-actions {
        padding-top: 0;
      }
      .category-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="news-category-page" data-spy="scroll" data-target="#mainNav">
  @include('pinooycoop.partials.nav')

  <section class="category-shell">
    <div class="container">
      <div class="category-head">
        <div>
          <a href="{{ route('pinooycoop.events') }}" class="btn btn-trans-black category-back-link">Back to News & Events</a>
          <h1 class="category-title">{{ $category['label'] }}</h1>
          <p class="category-copy">{{ $category['description'] }}</p>
        </div>
      </div>

      <details class="category-filter-panel" @if ($hasDateFilter) open @endif>
        <summary class="category-filter-toggle">Filter by published date</summary>
        <form method="GET" action="{{ route('pinooycoop.events.category', ['category' => $categorySlug]) }}" class="category-filter" id="category-date-filter">
          <div class="category-filter-field">
            <div class="category-date-grid">
              <select name="month" id="category-filter-month" aria-label="Filter month">
                <option value="">Month</option>
                @foreach (range(1, 12) as $month)
                  <option value="{{ $month }}" {{ (int) $selectedMonth === $month ? 'selected' : '' }}>
                    {{ \Illuminate\Support\Carbon::create(null, $month, 1)->format('F') }}
                  </option>
                @endforeach
              </select>
              <select name="day" id="category-filter-day" aria-label="Filter day">
                <option value="">Day</option>
                @foreach (range(1, 31) as $day)
                  <option value="{{ $day }}" {{ (int) $selectedDay === $day ? 'selected' : '' }}>{{ $day }}</option>
                @endforeach
              </select>
              <select name="year" id="category-filter-year" aria-label="Filter year">
                <option value="">Year</option>
                @foreach (range((int) now()->format('Y'), 2020) as $year)
                  <option value="{{ $year }}" {{ (int) $selectedYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
              </select>
            </div>
            <span class="category-filter-hint">Choose month, day, year, or any combination.</span>
          </div>
          <div class="category-filter-actions">
            <button type="submit" class="btn btn-hero">Apply Filter</button>
            @if ($hasDateFilter)
              <a href="{{ route('pinooycoop.events.category', ['category' => $categorySlug]) }}" class="btn btn-trans-black">Clear</a>
            @endif
          </div>
        </form>
      </details>

      @if ($items->isEmpty())
        <div class="category-empty">
          <h4>No published items yet</h4>
          <p class="mb-0">
            @if ($hasDateFilter)
              No {{ strtolower($category['label']) }} matched the selected date filter.
            @else
              Published {{ strtolower($category['label']) }} will appear here.
            @endif
          </p>
        </div>
      @else
        <div class="category-grid">
          @foreach ($items as $item)
            <article class="category-card">
              <div class="category-image">
                @if ($item->image_data_uri)
                  <img src="{{ $item->image_data_uri }}" alt="{{ $item->title }}">
                @endif
              </div>
              <div class="category-card-body">
                <div class="category-card-type">{{ $item->template_label }}</div>
                <div class="category-meta">
                  <span><i class="far fa-calendar"></i> {{ optional($item->published_at)->format('M d, Y') ?? optional($item->updated_at)->format('M d, Y') }}</span>
                </div>
                <h3>{{ $item->title }}</h3>
                <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->subcontext ?: $item->content ?? ''), 155) }}</p>
                <a href="{{ route('cms.page', ['slug' => $item->slug]) }}" class="category-read">Read more</a>
              </div>
            </article>
          @endforeach
        </div>

        <div class="category-pager">
          {{ $items->links() }}
        </div>
      @endif
    </div>
  </section>

  @include('pinooycoop.partials.footer')

  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/bootstrap.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
