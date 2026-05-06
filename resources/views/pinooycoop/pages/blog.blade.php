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
  <title>News &amp; Articles - MASS-SPECC</title>

  <!-- ** Mobile Specific Metas ** -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="MASS-SPECC News and Articles">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="author" content="MASS-SPECC">
  <meta name="generator" content="MASS-SPECC v1.0">
  
  <!-- theme meta -->
  <meta name="theme-name" content="mass-specc" />
  
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
    /* Modern Blog Page Styles */
    .modern-blog-header {
      position: relative;
      min-height: 500px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #1f3d7c 0%, #00a7e1 100%);
      overflow: hidden;
      margin-top: 70px;
    }

    .modern-blog-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('../images/blog/blog-lg.jpg') center/cover;
      opacity: 0.3;
    }

    .modern-blog-header .header-content {
      position: relative;
      z-index: 2;
      text-align: center;
      padding: 60px 20px;
    }

    .modern-blog-header h1 {
      font-size: 3.5rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .modern-blog-header p {
      font-size: 1.2rem;
      color: rgba(255,255,255,0.9);
      max-width: 600px;
      margin: 0 auto;
    }

    .modern-blog-grid {
      padding: 80px 0;
    }

    .blog-card {
      background: #fff;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      margin-bottom: 30px;
      height: 100%;
    }

    .blog-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .blog-card-image {
      position: relative;
      height: 250px;
      overflow: hidden;
    }

    .blog-card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .blog-card:hover .blog-card-image img {
      transform: scale(1.1);
    }

    .blog-card-category {
      position: absolute;
      top: 20px;
      left: 20px;
      background: #00a7e1;
      color: #fff;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .blog-card-content {
      padding: 30px;
    }

    .blog-card-meta {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 15px;
      font-size: 0.9rem;
      color: #788487;
    }

    .blog-card-meta i {
      color: #00a7e1;
      margin-right: 5px;
    }

    .blog-card-title {
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 15px;
      line-height: 1.4;
    }

    .blog-card-title a {
      color: #232323;
      transition: color 0.3s ease;
    }

    .blog-card-title a:hover {
      color: #00a7e1;
    }

    .blog-card-excerpt {
      color: #788487;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .blog-card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 20px;
      border-top: 1px solid #f0f0f0;
    }

    .read-more-btn {
      color: #00a7e1;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: gap 0.3s ease;
    }

    .read-more-btn:hover {
      gap: 15px;
      color: #1f3d7c;
    }

    .author-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }

    .modern-sidebar {
      padding: 80px 0;
    }

    .sidebar-widget {
      background: #fff;
      border-radius: 15px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .sidebar-widget h5 {
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid #00a7e1;
      display: inline-block;
    }

    .search-widget .form-group {
      position: relative;
    }

    .search-widget input {
      border-radius: 25px;
      padding: 12px 45px 12px 20px;
      border: 2px solid #f0f0f0;
      transition: border-color 0.3s ease;
    }

    .search-widget input:focus {
      border-color: #00a7e1;
      outline: none;
    }

    .search-widget i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #00a7e1;
    }

    .category-list li {
      padding: 10px 0;
      border-bottom: 1px solid #f0f0f0;
      transition: padding-left 0.3s ease;
    }

    .category-list li:hover {
      padding-left: 10px;
      color: #00a7e1;
    }

    .category-list li:last-child {
      border-bottom: none;
    }

    .tag-cloud a {
      display: inline-block;
      padding: 8px 16px;
      margin: 4px;
      background: #f8f9fa;
      color: #788487;
      border-radius: 20px;
      font-size: 0.85rem;
      transition: all 0.3s ease;
    }

    .tag-cloud a:hover {
      background: #00a7e1;
      color: #fff;
    }

    .featured-post {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid #f0f0f0;
    }

    .featured-post:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .featured-post img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
    }

    .featured-post h6 {
      font-size: 0.95rem;
      line-height: 1.4;
      margin-bottom: 5px;
    }

    .featured-post h6 a {
      color: #232323;
      transition: color 0.3s ease;
    }

    .featured-post h6 a:hover {
      color: #00a7e1;
    }

    .featured-post span {
      font-size: 0.8rem;
      color: #788487;
    }

    .newsletter-widget {
      background: linear-gradient(135deg, #1f3d7c 0%, #00a7e1 100%);
      color: #fff;
    }

    .newsletter-widget h5 {
      color: #fff;
      border-bottom-color: rgba(255,255,255,0.3);
    }

    .newsletter-widget input {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: #fff;
      margin-bottom: 10px;
    }

    .newsletter-widget input::placeholder {
      color: rgba(255,255,255,0.7);
    }

    .newsletter-widget .btn {
      background: #fff;
      color: #1f3d7c;
      width: 100%;
      border-radius: 25px;
      font-weight: 600;
    }

    @media (max-width: 768px) {
      .modern-blog-header h1 {
        font-size: 2.5rem;
      }
      
      .modern-blog-header p {
        font-size: 1rem;
      }
    }
  </style>

</head><body data-spy="scroll" data-target="#mainNav">

@include('pinooycoop.partials.nav')

<!-- Modern Blog Header -->
<div class="modern-blog-header">
  <div class="header-content">
    <h1>Latest News & Articles</h1>
    <p>Stay updated with the latest developments, insights, and stories from MASS-SPECC</p>
  </div>
</div>

<!-- Blog Content Section -->
<section class="modern-blog-grid">
  <div class="container">
    <div class="row">
      <!-- Main Blog Grid -->
      <div class="col-lg-8">
        <div class="row">
          <!-- Blog Post 1 - Full Width Featured -->
          <div class="col-lg-12">
            <div class="blog-card">
              <div class="blog-card-image">
                <img src="{{ $articles[0]['image'] ?? 'images/blog/blog-lg.jpg' }}" alt="Featured Article">
                <span class="blog-card-category">{{ $articles[0]['category'] ?? 'Featured' }}</span>
              </div>
              <div class="blog-card-content">
                <div class="blog-card-meta">
                  <span><i class="fa fa-user"></i> {{ $articles[0]['author'] ?? 'John Mackel' }}</span>
                  <span><i class="fa fa-calendar-check"></i> {{ $articles[0]['date'] ?? '19 Jun 2024' }}</span>
                  <span><i class="fa fa-comments"></i> {{ $articles[0]['comments_count'] ?? 12 }} Comments</span>
                </div>
                <h3 class="blog-card-title">
                  <a href="{{ route('pinooycoop.blog.single') }}">{{ $articles[0]['title'] ?? 'Empowering Cooperatives: MASS-SPECC\'s Strategic Initiatives for 2024' }}</a>
                </h3>
                <p class="blog-card-excerpt">{{ $articles[0]['excerpt'] ?? 'Discover how MASS-SPECC is revolutionizing the cooperative sector through innovative programs, strategic partnerships, and community-driven initiatives that create lasting impact.' }}</p>
                <div class="blog-card-footer">
                  <a href="{{ route('pinooycoop.blog.single') }}" class="read-more-btn">
                    Read More <i class="fa fa-angle-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Blog Posts Grid -->
          @for($i = 1; $i < count($articles ?? []); $i++)
          <div class="col-lg-6 col-md-6">
            <div class="blog-card">
              <div class="blog-card-image">
                <img src="{{ $articles[$i]['image'] ?? 'images/blog/blog-' . ($i) . '.jpg' }}" alt="Article">
                <span class="blog-card-category">{{ $articles[$i]['category'] ?? 'Category' }}</span>
              </div>
              <div class="blog-card-content">
                <div class="blog-card-meta">
                  <span><i class="fa fa-user"></i> {{ $articles[$i]['author'] ?? 'Author' }}</span>
                  <span><i class="fa fa-calendar-check"></i> {{ $articles[$i]['date'] ?? 'Date' }}</span>
                </div>
                <h4 class="blog-card-title">
                  <a href="{{ route('pinooycoop.blog.single') }}">{{ $articles[$i]['title'] ?? 'Article Title' }}</a>
                </h4>
                <p class="blog-card-excerpt">{{ $articles[$i]['excerpt'] ?? 'Article excerpt...' }}</p>
                <div class="blog-card-footer">
                  <a href="{{ route('pinooycoop.blog.single') }}" class="read-more-btn">
                    Read More <i class="fa fa-angle-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endfor
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="modern-sidebar">
          <!-- Search Widget -->
          <div class="sidebar-widget search-widget">
            <div class="form-group">
              <input type="text" placeholder="Search articles..." class="form-control">
              <i class="fa fa-search"></i>
            </div>
          </div>

          <!-- Newsletter Widget -->
          <div class="sidebar-widget newsletter-widget">
            <h5>Newsletter</h5>
            <p style="margin-bottom: 15px; opacity: 0.9;">Subscribe to get the latest news and updates</p>
            <form>
              <input type="email" placeholder="Your email address" class="form-control">
              <button type="submit" class="btn">Subscribe</button>
            </form>
          </div>

          <!-- Featured Posts -->
          <div class="sidebar-widget">
            <h5>Popular Articles</h5>
            @for($i = 1; $i < min(4, count($articles ?? [])); $i++)
            <div class="featured-post">
              <img src="{{ $articles[$i]['image'] ?? 'images/blog/blog-' . $i . '.jpg' }}" alt="Post">
              <div>
                <h6><a href="{{ route('pinooycoop.blog.single') }}">{{ $articles[$i]['title'] ?? 'Article Title' }}</a></h6>
                <span><i class="fa fa-calendar"></i> {{ $articles[$i]['date'] ?? 'Date' }}</span>
              </div>
            </div>
            @endfor
          </div>

          <!-- Categories -->
          <div class="sidebar-widget">
            <h5>Categories</h5>
            <ul class="category-list">
              <li><a href="#!">Marketing <span class="float-right">(12)</span></a></li>
              <li><a href="#!">Development <span class="float-right">(8)</span></a></li>
              <li><a href="#!">Training <span class="float-right">(15)</span></a></li>
              <li><a href="#!">Innovation <span class="float-right">(6)</span></a></li>
              <li><a href="#!">Technology <span class="float-right">(10)</span></a></li>
              <li><a href="#!">Community <span class="float-right">(9)</span></a></li>
            </ul>
          </div>

          <!-- Tags -->
          <div class="sidebar-widget">
            <h5>Popular Tags</h5>
            <div class="tag-cloud">
              <a href="#!">cooperatives</a>
              <a href="#!">development</a>
              <a href="#!">training</a>
              <a href="#!">marketing</a>
              <a href="#!">technology</a>
              <a href="#!">innovation</a>
              <a href="#!">community</a>
              <a href="#!">growth</a>
              <a href="#!">leadership</a>
            </div>
          </div>
        </div>
      </div>
    </div>
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
