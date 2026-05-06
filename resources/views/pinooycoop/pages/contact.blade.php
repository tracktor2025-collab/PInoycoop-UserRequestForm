<!DOCTYPE html>
<html lang="en">
<head>
  <base href="{{ url('/pinooycoop') }}/">

  <meta charset="utf-8">
  <title>Contact Us - MASS-SPECC</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Contact MASS-SPECC Cooperative Development Center">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="author" content="MASS-SPECC">

  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/fontawesome/css/all.css">
  <link rel="stylesheet" href="plugins/icofont/icofont.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="images/favicon.png" type="image/x-icon">

  <style>
    .contact-page {
      background: #f6f8fb;
    }

    .contact-page .container {
      max-width: 1180px;
    }

    .contact-hero {
      background: linear-gradient(135deg, rgba(8, 35, 58, .76), rgba(17, 154, 219, .42)), url('{{ asset('pinooycoop/images/banner/GSP_0282-scaled-e1753425253116.jpg') }}') center center/cover;
      padding: 150px 0 80px;
      color: #ffffff;
    }

    .contact-hero h1 {
      color: #ffffff;
      font-size: 44px;
      line-height: 1.15;
      margin-bottom: 14px;
    }

    .contact-hero p {
      color: rgba(255, 255, 255, .86);
      font-size: 17px;
      max-width: 620px;
      margin: 0;
    }

    .contact-section {
      padding: 70px 0;
    }

    .contact-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(360px, .92fr);
      gap: 42px;
      align-items: start;
    }

    .contact-panel,
    .contact-map-panel {
      background: #ffffff;
      border: 1px solid #e8edf3;
      border-radius: 8px;
      box-shadow: 0 18px 40px rgba(15, 34, 58, .08);
    }

    .contact-panel {
      padding: 34px;
    }

    .contact-title {
      color: #20242a;
      font-size: 36px;
      line-height: 1.15;
      margin-bottom: 14px;
    }

    .contact-title::after {
      content: "";
      display: block;
      width: 105px;
      height: 3px;
      background: #1498df;
      margin-top: 24px;
    }

    .contact-copy {
      color: #66717d;
      margin: 0 0 28px;
      max-width: 620px;
    }

    .contact-form .form-control {
      border: 1px solid #d8dde4;
      border-radius: 2px;
      color: #222831;
      font-size: 16px;
      min-height: 54px;
      padding: 14px 16px;
      box-shadow: none;
    }

    .contact-form textarea.form-control {
      min-height: 150px;
      resize: vertical;
    }

    .contact-form .form-control:focus {
      border-color: #1498df;
    }

    .contact-submit {
      border: 0;
      border-radius: 30px;
      background: #19a5df;
      color: #ffffff;
      cursor: pointer;
      font-weight: 700;
      min-width: 210px;
      padding: 15px 28px;
      text-transform: uppercase;
      transition: background .2s ease, transform .2s ease;
    }

    .contact-submit:hover {
      background: #0d8fca;
      transform: translateY(-1px);
    }

    .contact-map-panel {
      overflow: hidden;
    }

    .contact-map {
      display: block;
      width: 100%;
      min-height: 420px;
      border: 0;
    }

    .contact-info {
      padding: 26px 28px 30px;
    }

    .contact-info h3 {
      color: #20242a;
      font-size: 22px;
      margin-bottom: 18px;
    }

    .contact-list li {
      display: flex;
      gap: 12px;
      color: #515b66;
      font-size: 15px;
      line-height: 1.5;
      margin-bottom: 14px;
    }

    .contact-list i {
      color: #1498df;
      font-size: 18px;
      line-height: 1.4;
      width: 20px;
      text-align: center;
    }

    .contact-list strong {
      display: block;
      color: #222831;
      margin-bottom: 2px;
    }

    .contact-link-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 22px;
    }

    .contact-link-row a {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid #dbe6ef;
      border-radius: 24px;
      color: #156f9f;
      font-size: 14px;
      font-weight: 600;
      padding: 10px 15px;
    }

    .contact-link-row a:hover {
      border-color: #1498df;
      color: #1498df;
    }

    @media (max-width: 991px) {
      .contact-hero {
        padding: 125px 0 64px;
      }

      .contact-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 575px) {
      .contact-hero h1 {
        font-size: 34px;
      }

      .contact-section {
        padding: 46px 0;
      }

      .contact-panel,
      .contact-info {
        padding: 24px;
      }

      .contact-title {
        font-size: 30px;
      }

      .contact-map {
        min-height: 320px;
      }
    }
  </style>
</head>
<body data-spy="scroll" data-target="#mainNav" class="contact-page">

@include('pinooycoop.partials.nav')

<main>
  <section class="contact-hero">
    <div class="container">
      <h1>Contact Us</h1>
      <p>Send a message, call our offices, or find MASS-SPECC Cooperative Development Center on the map.</p>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-grid">
        <div class="contact-panel">
          <h2 class="contact-title">Send Us a Message</h2>
          <p class="contact-copy">For inquiries, membership concerns, partnerships, and support requests, send us your details and our team will get back to you.</p>

          @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif

          <form class="contact-form" method="post" action="{{ route('pinooycoop.contact.store') }}">
            @csrf
            <div class="form-group">
              <input name="name" type="text" class="form-control" placeholder="Your Name" value="{{ old('name') }}" required>
              @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
              <input name="email" type="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" required>
              @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
              <textarea name="message" class="form-control" rows="7" placeholder="Message" required>{{ old('message') }}</textarea>
              @error('message') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="contact-submit">Send Message</button>
          </form>
        </div>

        <aside class="contact-map-panel">
          <iframe
            class="contact-map"
            title="MASS-SPECC Cooperative Development Center map"
            src="https://maps.google.com/maps?q=MASS-SPECC%20Cooperative%20Development%20Center%20Cagayan%20de%20Oro&t=&z=12&ie=UTF8&iwloc=&output=embed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>

          <div class="contact-info">
            <h3>MASS-SPECC Cooperative Development Center</h3>
            <ul class="contact-list">
              <li>
                <i class="fas fa-map-marker-alt"></i>
                <span><strong>Main Office</strong>Tiano-Yacapin Sts., Cagayan de Oro City, Philippines, 9000</span>
              </li>
              <li>
                <i class="fas fa-phone-alt"></i>
                <span><strong>Phone</strong>(088) 326-4617</span>
              </li>
              <li>
                <i class="fas fa-map-marker-alt"></i>
                <span><strong>Davao Office</strong>Anahaw Village, Anahaw Road, Ma-a, Davao City, Philippines, 8000</span>
              </li>
              <li>
                <i class="fas fa-phone-alt"></i>
                <span><strong>Davao Phone</strong>(084) 244-1096</span>
              </li>
              <li>
                <i class="fas fa-headset"></i>
                <span><strong>Helpdesk</strong>0967-448-4743</span>
              </li>
              <li>
                <i class="far fa-envelope"></i>
                <span><strong>Email</strong><a href="mailto:msu@mass-specc.com">msu@mass-specc.com</a></span>
              </li>
            </ul>

            <div class="contact-link-row">
              <a href="https://www.facebook.com/MASS.SPECC" target="_blank" rel="noopener"><i class="fab fa-facebook"></i> Facebook</a>
              <a href="https://www.instagram.com/mass.specc/" target="_blank" rel="noopener"><i class="fab fa-instagram"></i> Instagram</a>
              <a href="https://www.youtube.com/@MSP_Coop" target="_blank" rel="noopener"><i class="fab fa-youtube"></i> YouTube</a>
              <a href="https://www.google.com/maps/search/?api=1&query=MASS-SPECC%20Cooperative%20Development%20Center%20Cagayan%20de%20Oro" target="_blank" rel="noopener"><i class="fas fa-map-marked-alt"></i> Open Map</a>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>
</main>

@include('pinooycoop.partials.footer')

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>
