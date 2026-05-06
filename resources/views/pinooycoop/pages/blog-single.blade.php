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
  <title>{{ $article['title'] ?? 'Article' }} - MASS-SPECC</title>

  <!-- ** Mobile Specific Metas ** -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="MASS-SPECC Article">
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
    /* Modern Single Article Page Styles */
    body {
      padding-top: 0;
    }

    .article-hero {
      position: relative;
      min-height: 600px;
      margin-top: 70px;
      overflow: hidden;
    }

    .article-hero-image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      filter: brightness(0.4);
    }

    .article-hero-content {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 600px;
      padding: 80px 20px;
      text-align: center;
    }

    .article-hero-badge {
      display: inline-block;
      background: #00a7e1;
      color: #fff;
      padding: 8px 24px;
      border-radius: 30px;
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 20px;
    }

    .article-hero h1 {
      font-size: 3rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 20px;
      max-width: 900px;
      line-height: 1.3;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .article-hero-meta {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 25px;
      color: rgba(255,255,255,0.8);
      font-size: 0.95rem;
    }

    .article-hero-meta span {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .article-hero-meta i {
      color: #00a7e1;
    }

    .article-content-wrapper {
      padding: 80px 0;
    }

    .article-main {
      background: #fff;
      border-radius: 20px;
      padding: 50px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }

    .article-featured-image {
      width: 100%;
      height: 400px;
      border-radius: 15px;
      overflow: hidden;
      margin-bottom: 40px;
    }

    .article-featured-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .article-body h2,
    .article-body h3,
    .article-body h4 {
      color: #232323;
      margin-top: 30px;
      margin-bottom: 15px;
    }

    .article-body h2 {
      font-size: 1.8rem;
    }

    .article-body h3 {
      font-size: 1.5rem;
    }

    .article-body h4 {
      font-size: 1.3rem;
    }

    .article-body p {
      font-size: 1.05rem;
      line-height: 1.8;
      color: #555;
      margin-bottom: 20px;
    }

    .article-body blockquote {
      background: linear-gradient(135deg, #1f3d7c 0%, #00a7e1 100%);
      color: #fff;
      padding: 40px;
      border-radius: 15px;
      margin: 30px 0;
      position: relative;
      font-style: italic;
      font-size: 1.1rem;
      line-height: 1.8;
    }

    .article-body blockquote i {
      font-size: 2rem;
      opacity: 0.5;
      position: absolute;
      top: 20px;
      left: 20px;
    }

    .article-body blockquote p {
      color: #fff;
      margin-bottom: 0;
      padding-left: 30px;
    }

    .article-tags {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 15px;
      padding: 30px 0;
      border-top: 1px solid #f0f0f0;
      border-bottom: 1px solid #f0f0f0;
      margin: 30px 0;
    }

    .article-tags h5 {
      margin: 0;
      color: #232323;
    }

    .article-tags a {
      display: inline-block;
      padding: 6px 16px;
      background: #f8f9fa;
      color: #788487;
      border-radius: 20px;
      font-size: 0.85rem;
      transition: all 0.3s ease;
    }

    .article-tags a:hover {
      background: #00a7e1;
      color: #fff;
    }

    .article-share {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .article-share h5 {
      margin: 0;
      color: #232323;
    }

    .article-share a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #f8f9fa;
      color: #788487;
      transition: all 0.3s ease;
    }

    .article-share a:hover {
      background: #00a7e1;
      color: #fff;
      transform: translateY(-3px);
    }

    .author-box {
      display: flex;
      gap: 30px;
      padding: 40px;
      background: #f8f9fa;
      border-radius: 15px;
      margin: 40px 0;
    }

    .author-box img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #00a7e1;
    }

    .author-box-content h4 {
      margin-bottom: 10px;
    }

    .author-box-content p {
      color: #788487;
      line-height: 1.7;
    }

    .author-box-content .author-social {
      margin-top: 15px;
    }

    .author-box-content .author-social a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: #00a7e1;
      color: #fff;
      margin-right: 8px;
      transition: all 0.3s ease;
    }

    .author-box-content .author-social a:hover {
      background: #1f3d7c;
      transform: translateY(-3px);
    }

    /* Comments Section */
    .comments-section {
      margin-top: 50px;
    }

    .comments-section h3 {
      font-size: 1.5rem;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 2px solid #00a7e1;
      display: inline-block;
    }

    .comment-item {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
      padding-bottom: 30px;
      border-bottom: 1px solid #f0f0f0;
    }

    .comment-item:last-child {
      border-bottom: none;
    }

    .comment-item img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
    }

    .comment-content h5 {
      margin-bottom: 5px;
    }

    .comment-content .comment-date {
      font-size: 0.85rem;
      color: #788487;
      margin-bottom: 10px;
      display: block;
    }

    .comment-content p {
      color: #555;
      line-height: 1.6;
      margin-bottom: 10px;
    }

    .comment-reply-btn {
      color: #00a7e1;
      font-weight: 600;
      font-size: 0.9rem;
      transition: color 0.3s ease;
    }

    .comment-reply-btn:hover {
      color: #1f3d7c;
    }

    .comment-item.reply {
      margin-left: 100px;
    }

    /* Comment Form */
    .comment-form {
      margin-top: 50px;
      background: #f8f9fa;
      padding: 40px;
      border-radius: 15px;
    }

    .comment-form h3 {
      font-size: 1.5rem;
      margin-bottom: 10px;
    }

    .comment-form p {
      color: #788487;
      margin-bottom: 30px;
    }

    .comment-form .form-control {
      border-radius: 10px;
      padding: 15px 20px;
      border: 2px solid #e9ecef;
      transition: border-color 0.3s ease;
    }

    .comment-form .form-control:focus {
      border-color: #00a7e1;
      outline: none;
      box-shadow: none;
    }

    .comment-form .btn {
      border-radius: 30px;
      padding: 15px 40px;
      font-weight: 600;
    }

    /* Modern Sidebar */
    .modern-sidebar {
      padding-top: 80px;
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

    .recent-post {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid #f0f0f0;
    }

    .recent-post:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .recent-post img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
    }

    .recent-post h6 {
      font-size: 0.95rem;
      line-height: 1.4;
      margin-bottom: 5px;
    }

    .recent-post h6 a {
      color: #232323;
      transition: color 0.3s ease;
    }

    .recent-post h6 a:hover {
      color: #00a7e1;
    }

    .recent-post span {
      font-size: 0.8rem;
      color: #788487;
    }

    @media (max-width: 768px) {
      .article-hero h1 {
        font-size: 2rem;
      }

      .article-hero-meta {
        flex-direction: column;
        gap: 10px;
      }

      .article-main {
        padding: 30px 20px;
      }

      .article-featured-image {
        height: 250px;
      }

      .author-box {
        flex-direction: column;
        text-align: center;
      }

      .comment-item.reply {
        margin-left: 30px;
      }
    }

    /* Modern Contact CTA Section */
    .modern-contact-cta {
      position: relative;
      min-height: 400px;
      display: flex;
      align-items: center;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      overflow: hidden;
      margin: 60px 0;
    }

    .modern-contact-cta::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(31, 61, 124, 0.85) 0%, rgba(0, 167, 225, 0.85) 100%);
      z-index: 1;
    }

    .cta-content {
      position: relative;
      z-index: 2;
      color: #fff;
      padding: 40px 20px;
    }

    .cta-content h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .cta-content p {
      font-size: 1.1rem;
      margin-bottom: 30px;
      opacity: 0.95;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .cta-buttons {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .cta-buttons .btn {
      padding: 15px 30px;
      border-radius: 30px;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .cta-buttons .btn-primary {
      background: #fff;
      color: #1f3d7c;
      border: 2px solid #fff;
    }

    .cta-buttons .btn-primary:hover {
      background: transparent;
      color: #fff;
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    .cta-buttons .btn-outline {
      background: transparent;
      color: #fff;
      border: 2px solid #fff;
    }

    .cta-buttons .btn-outline:hover {
      background: #fff;
      color: #1f3d7c;
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    @media (max-width: 768px) {
      .modern-contact-cta {
        min-height: 300px;
        background-attachment: scroll;
      }

      .cta-content h2 {
        font-size: 2rem;
      }

      .cta-buttons {
        flex-direction: column;
        align-items: center;
      }

      .cta-buttons .btn {
        width: 100%;
        max-width: 250px;
      }
    }
  </style>

</head><body data-spy="scroll" data-target="#mainNav">

@include('pinooycoop.partials.nav')

<!-- Article Hero Section -->
<div class="article-hero">
  <div class="article-hero-image" style="background-image: url('{{ $article['image'] ?? 'images/blog/blog-lg.jpg' }}');"></div>
  <div class="article-hero-content">
    <div>
      <span class="article-hero-badge">Featured Article</span>
      <h1>{{ $article['title'] ?? 'Empowering Cooperatives: MASS-SPECC\'s Strategic Initiatives for 2024' }}</h1>
      <div class="article-hero-meta">
        <span><i class="fa fa-user"></i> {{ $article['author'] ?? 'John Mackel' }}</span>
        <span><i class="fa fa-calendar-check"></i> {{ $article['date'] ?? '19 June 2024' }}</span>
        <span><i class="fa fa-comments"></i> {{ $article['comments_count'] ?? 12 }} Comments</span>
        <span><i class="fa fa-clock"></i> {{ $article['read_time'] ?? '8 min read' }}</span>
      </div>
    </div>
  </div>
</div>

<!-- Article Content Section -->
<section class="article-content-wrapper">
  <div class="container">
    <div class="row">
      <!-- Main Article Content -->
      <div class="col-lg-8">
        <div class="article-main">
          <!-- Featured Image -->
          <div class="article-featured-image">
            <img src="{{ $article['image'] ?? 'images/blog/blog-lg.jpg' }}" alt="Article Featured Image">
          </div>

          <!-- Article Body -->
          <div class="article-body">
            <p>{{ $article['content'] ?? 'MASS-SPECC continues to lead the way in empowering cooperatives across the Philippines through innovative programs, strategic partnerships, and community-driven initiatives that create lasting positive impact in communities.' }}</p>

            <h3>Strategic Vision for 2024</h3>
            <p>Our strategic vision for 2024 focuses on three key pillars: digital transformation, sustainable development, and capacity building. These pillars form the foundation of our comprehensive approach to cooperative development.</p>

            <p>Through our digital transformation initiatives, we are helping cooperatives leverage technology to improve their operations, reach more members, and provide better services. This includes implementing modern management systems, digital payment platforms, and online training programs.</p>

            <blockquote>
              <i class="fa fa-quote-left"></i>
              <p>"At MASS-SPECC, we believe that empowering cooperatives is key to building stronger, more resilient communities. Our commitment to sustainable development drives everything we do."</p>
            </blockquote>

            <h3>Key Initiatives and Programs</h3>
            <p>Our comprehensive program portfolio includes leadership training, financial management workshops, marketing strategy sessions, and technology adoption support. Each program is designed to address specific challenges faced by cooperatives in today's rapidly evolving landscape.</p>

            <p>We have also established partnerships with leading financial institutions, technology providers, and academic institutions to ensure our cooperatives have access to the resources and expertise they need to thrive.</p>

            <h4>Impact and Results</h4>
            <p>Since the launch of our 2024 initiatives, we have seen remarkable results:</p>
            <p>Over 500 cooperatives have participated in our training programs, with 95% reporting improved operational efficiency. Digital adoption has increased by 60% among member cooperatives, leading to better service delivery and member satisfaction.</p>

            <p>Our sustainable development programs have helped cooperatives reduce their environmental impact while improving profitability. We continue to measure and track our impact to ensure we are meeting our goals and making a real difference.</p>

            <h3>Looking Ahead</h3>
            <p>As we move forward, MASS-SPECC remains committed to its mission of empowering cooperatives and building stronger communities. We will continue to innovate, adapt, and expand our programs to meet the evolving needs of the cooperative sector.</p>

            <p>Our vision for the future includes expanding our reach to more remote communities, developing advanced digital tools, and creating new partnership opportunities that will benefit cooperatives across the country.</p>
          </div>

          <!-- Tags and Share -->
          <div class="article-tags">
            <h5>Tags:</h5>
            @if(isset($article['tags']) && is_array($article['tags']))
              @foreach($article['tags'] as $tag)
                <a href="#!">{{ $tag }}</a>
              @endforeach
            @else
              <a href="#!">Cooperatives</a>
              <a href="#!">Development</a>
              <a href="#!">Strategy</a>
              <a href="#!">Innovation</a>
              <a href="#!">Community</a>
            @endif
          </div>

          <div class="article-share">
            <h5>Share:</h5>
            <a href="#!"><i class="fab fa-facebook-f"></i></a>
            <a href="#!"><i class="fab fa-twitter"></i></a>
            <a href="#!"><i class="fab fa-linkedin-in"></i></a>
            <a href="#!"><i class="fab fa-pinterest-p"></i></a>
          </div>

          <!-- Author Box -->
          <div class="author-box">
            <img src="images/blog/2.jpg" alt="Author">
            <div class="author-box-content">
              <h4>John Mackel</h4>
              <p class="text-muted">Senior Communications Officer</p>
              <p>John has been with MASS-SPECC for over 10 years, leading communications and outreach initiatives. He is passionate about cooperative development and community empowerment.</p>
              <div class="author-social">
                <a href="#!"><i class="fab fa-facebook-f"></i></a>
                <a href="#!"><i class="fab fa-twitter"></i></a>
                <a href="#!"><i class="fab fa-linkedin-in"></i></a>
              </div>
            </div>
          </div>

          <!-- Comments Section -->
          <div class="comments-section">
            <h3>Comments (3)</h3>

            <div class="comment-item">
              <img src="images/blog/2.jpg" alt="Commenter">
              <div class="comment-content">
                <h5>Maria Santos</h5>
                <span class="comment-date">20 June 2024</span>
                <p>Excellent article! The initiatives described here are exactly what our cooperative needed. We've already seen positive changes since implementing some of these strategies.</p>
                <a href="#!" class="comment-reply-btn">Reply <i class="fa fa-reply"></i></a>
              </div>
            </div>

            <div class="comment-item reply">
              <img src="images/blog/2.jpg" alt="Commenter">
              <div class="comment-content">
                <h5>John Mackel</h5>
                <span class="comment-date">21 June 2024</span>
                <p>Thank you for your feedback, Maria! We're thrilled to hear about your positive experience. Please don't hesitate to reach out if you need any additional support.</p>
                <a href="#!" class="comment-reply-btn">Reply <i class="fa fa-reply"></i></a>
              </div>
            </div>

            <div class="comment-item">
              <img src="images/blog/2.jpg" alt="Commenter">
              <div class="comment-content">
                <h5>Carlos Reyes</h5>
                <span class="comment-date">19 June 2024</span>
                <p>Very informative read. I appreciate the detailed breakdown of the strategic initiatives. Looking forward to seeing how these programs develop over the coming months.</p>
                <a href="#!" class="comment-reply-btn">Reply <i class="fa fa-reply"></i></a>
              </div>
            </div>
          </div>

          <!-- Comment Form -->
          <div class="comment-form">
            <h3>Leave a Comment</h3>
            <p>Your email address will not be published. Required fields are marked *</p>
            <form>
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group mb-3">
                    <textarea cols="30" rows="5" class="form-control" placeholder="Your comment *" required></textarea>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <input type="text" class="form-control" placeholder="Your name *" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <input type="email" class="form-control" placeholder="Your email *" required>
                  </div>
                </div>
                <div class="col-lg-12">
                  <button type="submit" class="btn btn-hero">Post Comment</button>
                </div>
              </div>
            </form>
          </div>
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

          <!-- Recent Posts -->
          <div class="sidebar-widget">
            <h5>Recent Articles</h5>
            <div class="recent-post">
              <img src="images/blog/blog-1.jpg" alt="Post">
              <div>
                <h6><a href="#!">Digital Transformation in Cooperative Marketing</a></h6>
                <span><i class="fa fa-calendar"></i> 15 Jun 2024</span>
              </div>
            </div>
            <div class="recent-post">
              <img src="images/blog/blog-2.jpg" alt="Post">
              <div>
                <h6><a href="#!">Building Sustainable Cooperative Enterprises</a></h6>
                <span><i class="fa fa-calendar"></i> 12 Jun 2024</span>
              </div>
            </div>
            <div class="recent-post">
              <img src="images/blog/blog-3.jpg" alt="Post">
              <div>
                <h6><a href="#!">Capacity Building Programs for Leaders</a></h6>
                <span><i class="fa fa-calendar"></i> 10 Jun 2024</span>
              </div>
            </div>
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

<!-- Modern Contact CTA Section with Article Image -->
<section class="modern-contact-cta" style="background-image: url('{{ $article['image'] ?? 'images/blog/blog-lg.jpg' }}');">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <div class="cta-content">
          <h2>Ready to Transform Your Cooperative?</h2>
          <p>Join thousands of cooperatives that have benefited from MASS-SPECC's innovative programs and strategic initiatives.</p>
          <div class="cta-buttons">
            <a href="{{ route('pinooycoop.contact') }}" class="btn btn-primary">Get Started Today</a>
            <a href="{{ route('pinooycoop.about') }}" class="btn btn-outline">Learn More About Us</a>
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
