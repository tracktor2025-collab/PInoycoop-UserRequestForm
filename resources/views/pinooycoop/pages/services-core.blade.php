<!DOCTYPE html>
<html lang="en">
<head>
  <base href="{{ url('/pinooycoop') }}/">

  <meta charset="utf-8">
  <title>Services Core - Pinoy Coop</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Pinoy Coop Core digital solutions">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">

  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/fontawesome/css/all.css">
  <link rel="stylesheet" href="plugins/icofont/icofont.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="images/favicon.png" type="image/x-icon">

  <style>
    .core-page {
      background: #f8f6e9;
      color: #1f3d7c;
      padding-top: 92px;
    }

    .core-hero {
      background: #1f3d7c;
      margin: 0 auto 70px;
      padding: 58px 24px 48px;
      text-align: center;
    }

    .core-hero h1 {
      color: #fff;
      font-size: 34px;
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    .core-divider {
      background: #fff;
      height: 6px;
      margin: 18px auto 42px;
      width: 68px;
    }

    .core-hero p {
      color: #f2f2f2;
      line-height: 1.75;
      margin: 0 auto;
      max-width: 660px;
    }

    .core-logo {
      align-items: center;
      display: flex;
      gap: 16px;
      justify-content: center;
      margin-bottom: 24px;
    }

    .core-logo img {
      max-height: 72px;
      width: auto;
    }

    .core-logo span {
      color: #a31f24;
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .core-overview {
      align-items: stretch;
      display: grid;
      grid-template-columns: minmax(260px, 38%) 1fr;
      margin-bottom: 16px;
    }

    .core-overview-image {
      background: #d9d9e3;
      min-height: 280px;
      overflow: hidden;
    }

    .core-overview-image img {
      height: 100%;
      min-height: 280px;
      object-fit: contain;
      width: 100%;
    }

    .core-overview-text {
      background: #1f3d7c;
      color: #fff;
      font-size: 15px;
      line-height: 1.75;
      padding: 28px 36px;
      text-align: justify;
    }

    .benefits-title {
      background: #1f3d7c;
      color: #fff;
      font-size: 32px;
      font-weight: 800;
      letter-spacing: 1px;
      margin: 0 0 16px;
      padding: 14px 20px;
      text-align: center;
      text-transform: uppercase;
    }

    .benefits-grid {
      display: grid;
      gap: 10px;
      grid-template-columns: repeat(3, 1fr);
      padding-bottom: 64px;
    }

    .benefit-card {
      background: #d9d9e3;
      min-height: 250px;
      padding: 28px 30px;
    }

    .benefit-card h3 {
      align-items: flex-start;
      color: #1f3d7c;
      display: flex;
      font-size: 21px;
      gap: 16px;
      line-height: 1.15;
      margin-bottom: 22px;
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

    .benefit-card ul {
      color: #1f3d7c;
      padding-left: 18px;
    }

    .benefit-card li {
      line-height: 1.65;
      list-style: disc;
      margin-bottom: 12px;
    }

    @media (max-width: 991px) {
      .core-hero {
        margin-bottom: 42px;
        padding: 44px 22px 38px;
      }

      .core-overview,
      .benefits-grid {
        grid-template-columns: 1fr;
      }

      .benefit-card {
        min-height: auto;
      }
    }

    @media (max-width: 575px) {
      .core-page {
        padding-top: 74px;
      }

      .core-hero h1 {
        font-size: 24px;
        line-height: 1.25;
      }

      .core-logo {
        flex-direction: column;
        gap: 8px;
        margin-bottom: 18px;
      }

      .core-logo img {
        max-height: 56px;
      }

      .core-logo span {
        font-size: 24px;
      }

      .core-overview-image {
        min-height: 210px;
      }

      .core-overview-image img {
        min-height: 210px;
      }

      .benefits-title {
        font-size: 25px;
        padding: 11px 16px;
      }

      .core-overview-text,
      .benefit-card {
        font-size: 14px;
        padding: 20px;
        text-align: left;
      }

      .benefit-card h3 {
        font-size: 18px;
      }
    }
  </style>
</head>
<body data-spy="scroll" data-target="#mainNav">

@include('pinooycoop.partials.nav')

<main class="core-page">
  <section class="core-hero">
    <div class="container">
      <h1>Pinoy Coop Digital Solutions</h1>
      <div class="core-divider"></div>
      <p>Recognizing the need and demand of co-operatives digital solutions to their day-to-day operations, the Information &amp; Communication Technology (ICT) Unit has to ensure that our member cooperatives will be able to adapt and keep pace with this technological advancement. Thus, the ICT will offer the following digital solutions:</p>
    </div>
  </section>

  <section class="container">
    <div class="core-logo">
      <img src="images/logo.png" alt="Pinoy Coop">
      <span>Core</span>
    </div>

    <div class="core-overview">
      <div class="core-overview-image">
        <img src="images/services/core-accounting-system.svg" alt="Core accounting and banking system">
      </div>
      <div class="core-overview-text">
        An automated accounting system that evolved gradually from DOS-based <strong>(PINOY COOP CORE 1.0)</strong> to WINDOWS-based <strong>(PINOY COOP CORE 2.0)</strong> and now to WEB-based <strong>(PINOY COOP CORE 3.0)</strong> version. It covers all common banking applications, modules, and supports end-to-end financial processes. It features value-added solutions such as the <strong>PINOY COOP BLAST</strong>, <strong>PINOY COOP REGISTER</strong> and <strong>PINOY COOP ELECT</strong>. PINOY COOP BLAST runs through Short Message Service (SMS) but independent to network that provides timely transactional alerts and new opportunities for engaging with the cooperative's members. PINOY COOP REGISTER enables members to register fast on cooperative's general forums and assemblies while PINOY COOP ELECT enables members to cast their votes electronically during elections in general assembly. Pinoy Coop Core is also <strong>CISA-compliant</strong>, which means that it has the capacity to submit basic credit data directly to the Credit Information Corporation (CIC).
      </div>
    </div>

    <h2 class="benefits-title">Benefits</h2>

    <div class="benefits-grid">
      <article class="benefit-card">
        <h3><span class="benefit-number">1</span><span>Increased<br>Productivity</span></h3>
        <ul>
          <li>Speeds up accounting processes that will give you more time on other important tasks.</li>
          <li>System handles number of periodic operations, like interest accrual, report printing, processing of standing orders in phases.</li>
          <li>Eliminates redundancies of functions of staff.</li>
          <li>Easier to look up for accounts across multiple locations at the same time.</li>
        </ul>
      </article>

      <article class="benefit-card">
        <h3><span class="benefit-number">2</span><span>Easy-to-use</span></h3>
        <ul>
          <li>Provides classic windows menu structure as well as a set of speed buttons for access to the most common system functions.</li>
          <li>Customizable within limits, through the so-called "Configurator" program.</li>
          <li>Makes it easier for you to standardize procedures.</li>
        </ul>
      </article>

      <article class="benefit-card">
        <h3><span class="benefit-number">3</span><span>Secured</span></h3>
        <ul>
          <li>Access to the system is controlled by teller passwords which the tellers are enforced to change periodically by setting an expiry date.</li>
          <li>Advanced monitoring and control of user access through privilege granting and terminal registration.</li>
        </ul>
      </article>

      <article class="benefit-card">
        <h3><span class="benefit-number">4</span><span>Cost and<br>Time Saving</span></h3>
        <ul>
          <li>Procedures are more efficient which leads to saving of costs and other expenses.</li>
        </ul>
      </article>

      <article class="benefit-card">
        <h3><span class="benefit-number">5</span><span>Reporting</span></h3>
        <ul>
          <li>Generated reports are precise decision-making tool.</li>
        </ul>
      </article>

      <article class="benefit-card" aria-hidden="true"></article>
    </div>
  </section>
</main>

@include('pinooycoop.partials.footer')

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>
