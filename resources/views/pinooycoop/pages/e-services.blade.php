<!DOCTYPE html>
<html lang="en">
<head>
  <base href="{{ url('/pinooycoop') }}/">

  <meta charset="utf-8">
  <title>E-Services - Pinoy Coop</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Pinoy Coop E-Services banking solutions">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">

  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/fontawesome/css/all.css">
  <link rel="stylesheet" href="plugins/icofont/icofont.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="images/favicon.png" type="image/x-icon">

  <style>
    .eservices-page {
      background: #f8f6e9;
      color: #1f3d7c;
      padding-top: 94px;
    }

    .eservices-logo {
      align-items: center;
      display: flex;
      gap: 16px;
      justify-content: center;
      margin: 0 0 30px;
      text-transform: uppercase;
    }

    .eservices-logo img {
      max-height: 72px;
      width: auto;
    }

    .eservices-logo span {
      color: #171735;
      font-size: 30px;
      font-weight: 800;
      letter-spacing: 1px;
    }

    .service-feature {
      align-items: stretch;
      display: grid;
      grid-template-columns: minmax(300px, 50%) 1fr;
      margin-bottom: 12px;
    }

    .feature-image {
      background: url('images/blog/blog-1.jpg') center/cover no-repeat;
      min-height: 280px;
    }

    .feature-text {
      background: #1f3d7c;
      color: #fff;
      display: flex;
      flex-direction: column;
      font-size: 17px;
      justify-content: center;
      line-height: 1.65;
      padding: 34px 44px;
      text-align: justify;
    }

    .section-band {
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

    .benefits-grid {
      display: grid;
      gap: 10px;
      grid-template-columns: repeat(3, 1fr);
      margin-bottom: 18px;
    }

    .benefit-card,
    .services-column {
      background: #d9d9e3;
      padding: 26px 30px;
    }

    .benefit-card {
      min-height: 310px;
    }

    .benefit-card h3,
    .service-item h3 {
      align-items: flex-start;
      color: #12153f;
      display: flex;
      font-size: 19px;
      gap: 14px;
      line-height: 1.12;
      margin-bottom: 18px;
      text-transform: uppercase;
    }

    .number-badge {
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

    .number-badge.red {
      background: #a81d1f;
    }

    .red-text {
      color: #a81d1f;
    }

    .benefit-card ul,
    .service-item ul {
      color: #11143d;
      padding-left: 18px;
    }

    .benefit-card li,
    .service-item li {
      line-height: 1.55;
      list-style: disc;
      margin-bottom: 7px;
    }

    .services-grid {
      display: grid;
      gap: 10px;
      grid-template-columns: repeat(2, 1fr);
      padding-bottom: 64px;
    }

    .service-item {
      display: grid;
      grid-template-columns: 46px 1fr;
      margin-bottom: 34px;
    }

    .service-item h3 {
      display: block;
      margin-bottom: 10px;
    }

    .service-item:last-child {
      margin-bottom: 0;
    }

    .pipeline-note {
      color: #a81d1f;
      font-size: 14px;
      font-style: italic;
      margin: 28px 0 0;
      text-align: right;
    }

    @media (max-width: 991px) {
      .service-feature,
      .benefits-grid,
      .services-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 575px) {
      .eservices-page {
        padding-top: 82px;
      }

      .eservices-logo {
        flex-direction: column;
      }

      .feature-text,
      .benefit-card,
      .services-column {
        padding: 24px;
      }
    }
  </style>
</head>
<body data-spy="scroll" data-target="#mainNav">

@include('pinooycoop.partials.nav')

<main class="eservices-page">
  <section class="container">
    <div class="eservices-logo">
      <img src="images/logo.png" alt="Pinoy Coop">
      <span>E-Services</span>
    </div>

    <div class="service-feature">
      <div class="feature-image" aria-label="Online banking with card payment"></div>
      <div class="feature-text">
        <p>Pinoy Coop E-Services is a service that enables all members to do personal banking such as, but not limited to balance inquiry, withdrawal, bills payments, fund transfer, and more, by using their Pinoy Coop Kard anywhere on any channel (Pinoy Coop ATMs, other Bancnet affiliated ATMs, POS, Bancnet Online) and anytime most convenient to them thus enjoying better quality of life.</p>
      </div>
    </div>

    <h2 class="section-band">Benefits</h2>

    <div class="benefits-grid">
      <article class="benefit-card">
        <h3><span class="number-badge">1</span><span>Convenience</span></h3>
        <ul>
          <li>Account balance inquiry.</li>
          <li>Cash withdrawals of up to 50,000 a day.</li>
          <li>Pay your bills in accredited billers.</li>
          <li>Transfer funds from your account to another account in the same bank or another bank's account.</li>
          <li>Pay for purchases and services through partner merchants or website of e-payment partners.</li>
          <li>Government payments such as SSS contribution through internet banking.</li>
          <li>Purchase load for prepaid products.</li>
        </ul>
      </article>

      <article class="benefit-card">
        <h3><span class="number-badge">2</span><span>Wide Coverage and<br>Acceptance</span></h3>
        <ul>
          <li>You can use your card or withdraw cash from any Pinoy Coop ATM and all other Bancnet affiliated ATMs.</li>
        </ul>
      </article>

      <article class="benefit-card">
        <h3><span class="number-badge">3</span><span>Security</span></h3>
        <ul>
          <li>Using the card is safer than carrying cash in doing financial transactions.</li>
          <li>Uses EMV chip card technology to ensure security of every transaction.</li>
        </ul>
      </article>
    </div>

    <h2 class="section-band">Services</h2>

    <div class="services-grid">
      <div class="services-column">
        <article class="service-item">
          <span class="number-badge">1</span>
          <div>
            <h3>Balance Inquiry</h3>
            <ul><li>Inquire your Pinoy Coop Kard's available balance.</li></ul>
          </div>
        </article>

        <article class="service-item">
          <span class="number-badge">2</span>
          <div>
            <h3>Withdrawal</h3>
            <ul><li>Withdraw amount from Pinoy Coop Kard's available balance.</li></ul>
          </div>
        </article>

        <article class="service-item">
          <span class="number-badge">3</span>
          <div>
            <h3>Bills Payment</h3>
            <ul><li>Pay for bills using funds from Pinoy Coop Kard's savings account.</li></ul>
          </div>
        </article>

        <article class="service-item">
          <span class="number-badge">4</span>
          <div>
            <h3>Fund Transfer</h3>
            <ul><li>Transfer funds from different accounts in different banks.</li></ul>
          </div>
        </article>

        <article class="service-item">
          <span class="number-badge">5</span>
          <div>
            <h3>Cashless Shopping</h3>
            <ul><li>Paying for purchases and services using funds from your ATM account.</li></ul>
          </div>
        </article>
      </div>

      <div class="services-column">
        <article class="service-item">
          <span class="number-badge">6</span>
          <div>
            <h3>Internet Payments</h3>
            <ul><li>Pay for purchases at the website of e-payment partners.</li></ul>
          </div>
        </article>

        <article class="service-item">
          <span class="number-badge">7</span>
          <div>
            <h3>Prepaid Load</h3>
            <ul><li>Purchase airtime credits for prepaid products.</li></ul>
          </div>
        </article>

        <article class="service-item">
          <span class="number-badge">8</span>
          <div>
            <h3>Online Banking</h3>
            <ul>
              <li>Can transact balance inquiry, bills payment, funds transfer.</li>
              <li>Visit www.Bancnetonline.com.</li>
            </ul>
          </div>
        </article>

        <article class="service-item">
          <span class="number-badge red">9</span>
          <div>
            <h3 class="red-text">Cashout</h3>
            <ul><li>Withdraw cash from accredited POS Cashout partner institutions under pipeline.</li></ul>
          </div>
        </article>

        <article class="service-item">
          <span class="number-badge red">10</span>
          <div>
            <h3 class="red-text">Government Payments</h3>
            <ul><li>Pay for SSS, GSIS and PAG-IBIG contributions through internet banking.</li></ul>
          </div>
        </article>

        <p class="pipeline-note">*Under pipeline- services that are not yet live</p>
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
