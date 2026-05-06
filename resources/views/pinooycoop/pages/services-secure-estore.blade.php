<!DOCTYPE html>
<html lang="en">
<head>
  <base href="{{ url('/pinooycoop') }}/">

  <meta charset="utf-8">
  <title>Services Secure &amp; E-Store - Pinoy Coop</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Pinoy Coop Secure and E-Store digital solutions">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">

  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/fontawesome/css/all.css">
  <link rel="stylesheet" href="plugins/icofont/icofont.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="images/favicon.png" type="image/x-icon">

  <style>
    .solutions-page {
      background: #f8f6e9;
      color: #1f3d7c;
      padding-top: 94px;
    }

    .solution-logo {
      align-items: center;
      display: flex;
      gap: 16px;
      justify-content: center;
      margin: 0 0 24px;
      text-transform: uppercase;
    }

    .solution-logo img {
      max-height: 72px;
      width: auto;
    }

    .solution-logo span {
      color: #2c789a;
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .solution-logo .estore {
      color: #2d7b32;
    }

    .solution-feature {
      align-items: stretch;
      display: grid;
      grid-template-columns: minmax(280px, 50%) 1fr;
      margin-bottom: 22px;
    }

    .feature-image {
      background: #d9d9e3;
      min-height: 250px;
      overflow: hidden;
    }

    .feature-image img {
      height: 100%;
      min-height: 250px;
      object-fit: cover;
      width: 100%;
    }

    .feature-text {
      background: #1f3d7c;
      color: #fff;
      display: flex;
      flex-direction: column;
      font-size: 17px;
      justify-content: center;
      line-height: 1.65;
      padding: 34px 42px;
    }

    .feature-text ul {
      margin: 8px 0 0 22px;
    }

    .feature-text li {
      list-style: none;
      margin-bottom: 2px;
    }

    .benefits-title {
      background: #1f3d7c;
      color: #fff;
      font-size: 32px;
      font-weight: 800;
      letter-spacing: 1px;
      margin: 0 0 14px;
      padding: 13px 20px;
      text-align: center;
      text-transform: uppercase;
    }

    .benefits-list,
    .benefits-grid {
      margin-bottom: 34px;
    }

    .wide-benefit,
    .benefit-card {
      background: #d9d9e3;
      padding: 24px 32px;
    }

    .wide-benefit {
      margin-bottom: 10px;
    }

    .wide-benefit h3,
    .benefit-card h3 {
      align-items: center;
      color: #1f3d7c;
      display: flex;
      font-size: 21px;
      gap: 14px;
      margin-bottom: 15px;
      text-transform: uppercase;
    }

    .benefit-number {
      align-items: center;
      background: #1f3d7c;
      border-radius: 50%;
      color: #fff;
      display: inline-flex;
      flex: 0 0 34px;
      font-size: 20px;
      height: 34px;
      justify-content: center;
      width: 34px;
    }

    .wide-benefit ul,
    .benefit-card ul {
      color: #1f3d7c;
      padding-left: 18px;
    }

    .wide-benefit li,
    .benefit-card li {
      line-height: 1.65;
      list-style: disc;
      margin-bottom: 7px;
    }

    .benefits-grid {
      display: grid;
      gap: 10px;
      grid-template-columns: repeat(3, 1fr);
      padding-bottom: 58px;
    }

    .benefit-card {
      min-height: 210px;
    }

    @media (max-width: 991px) {
      .solution-feature,
      .benefits-grid {
        grid-template-columns: 1fr;
      }

      .benefit-card {
        min-height: auto;
      }
    }

    @media (max-width: 575px) {
      .solutions-page {
        padding-top: 74px;
      }

      .solution-logo {
        flex-direction: column;
        gap: 8px;
        margin-bottom: 18px;
      }

      .solution-logo img {
        max-height: 56px;
      }

      .solution-logo span {
        font-size: 24px;
      }

      .feature-image,
      .feature-image img {
        min-height: 210px;
      }

      .feature-text {
        font-size: 14px;
        line-height: 1.65;
      }

      .benefits-title {
        font-size: 25px;
        padding: 11px 16px;
      }

      .feature-text,
      .wide-benefit,
      .benefit-card {
        padding: 20px;
      }

      .wide-benefit h3,
      .benefit-card h3 {
        align-items: flex-start;
        font-size: 18px;
      }
    }
  </style>
</head>
<body data-spy="scroll" data-target="#mainNav">

@include('pinooycoop.partials.nav')

<main class="solutions-page">
  <section class="container">
    <div class="solution-logo">
      <img src="images/logo.png" alt="Pinoy Coop">
      <span>Secure</span>
    </div>

    <div class="solution-feature">
      <div class="feature-image secure">
        <img src="images/services/cybersecurity-secure.svg" alt="Cybersecurity network protection">
      </div>
      <div class="feature-text">
        <p>Developing the information security program of the organization through information security solutions such as the following:</p>
        <ul>
          <li>- Avira Endpoint Security</li>
          <li>- Firewall</li>
          <li>- Data Backup and Recovery</li>
        </ul>
      </div>
    </div>

    <h2 class="benefits-title">Benefits</h2>

    <div class="benefits-list">
      <article class="wide-benefit">
        <h3><span class="benefit-number">1</span><span>Security</span></h3>
        <ul>
          <li>Identifies, authenticates and authorizes individuals and groups of peoples to have access to applications, systems, and networks by associating user rights and restrictions.</li>
          <li>Protecting your network when accessed via remote devices such as laptops or other wireless and mobile devices.</li>
          <li>Allowing you to control the access of end users to your data and applications.</li>
          <li>Protects your organization from web-based attacks and provides content filtering.</li>
          <li>Allowing data backup and recovering data if lost anytime.</li>
        </ul>
      </article>

      <article class="wide-benefit">
        <h3><span class="benefit-number">2</span><span>Cost Effective</span></h3>
        <ul>
          <li>Reasonable and shared cost within the members of the network.</li>
        </ul>
      </article>
    </div>

    <div class="solution-logo">
      <img src="images/logo.png" alt="Pinoy Coop">
      <span class="estore">E-Store</span>
    </div>

    <div class="solution-feature">
      <div class="feature-image estore">
        <img src="images/services/hardware-estore.svg" alt="Hardware products for sale">
      </div>
      <div class="feature-text">
        <p>A provider of hardware items such as but not limited to personal computers, servers, switches and routers, external storage, UPS, structured cabling and other hardware peripherals that responds to the requirements of technology for best organization performance.</p>
      </div>
    </div>

    <h2 class="benefits-title">Benefits</h2>

    <div class="benefits-grid">
      <article class="benefit-card">
        <h3><span class="benefit-number">1</span><span>Quality</span></h3>
        <ul>
          <li>Provides products that meet specific needs and requirements of technology.</li>
        </ul>
      </article>

      <article class="benefit-card">
        <h3><span class="benefit-number">2</span><span>Cost-Effective</span></h3>
        <ul>
          <li>Price reasonable for product's useful life.</li>
        </ul>
      </article>

      <article class="benefit-card">
        <h3><span class="benefit-number">3</span><span>After Sales<br>Service</span></h3>
        <ul>
          <li>Provides good after sale service to satisfy and retain patronage of member owners.</li>
        </ul>
      </article>
    </div>
  </section>
</main>

@include('pinooycoop.partials.footer')

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>
