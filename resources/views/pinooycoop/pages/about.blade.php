<!DOCTYPE html>
<html lang="en">
<head>
  <base href="{{ url('/pinooycoop') }}/">

  <meta charset="utf-8">
  <title>About Pinoycoop - MASS-SPECC</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="About Pinoycoop, MASS-SPECC's cooperative technology platform and digital services ecosystem.">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="author" content="MASS-SPECC">
  <meta name="theme-name" content="promodise" />

  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/fontawesome/css/all.css">
  <link rel="stylesheet" href="plugins/icofont/icofont.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="images/favicon.png" type="image/x-icon">
  <style>
    .about-pinoycoop-card {
      height: 100%;
      background: #fff;
      border: 1px solid rgba(31, 61, 124, 0.1);
      border-radius: 8px;
      padding: 28px 24px;
      box-shadow: 0 10px 28px rgba(31, 61, 124, 0.06);
    }

    .about-pinoycoop-card h4 {
      color: #1f3d7c;
    }

    .about-pinoycoop-number {
      font-size: 42px;
      line-height: 1;
      font-weight: 700;
      color: #00a7e1;
      margin-bottom: 14px;
    }

    .about-logo-panel {
      background: #f7fbff;
      border: 1px solid rgba(31, 61, 124, 0.12);
      border-radius: 10px;
      padding: 30px;
    }

    .about-logo-panel img {
      max-height: 110px;
      object-fit: contain;
    }

    .about-hero-banner {
      width: 100%;
      min-height: 589px;
      background: url('{{ asset("MASS-SPECC Logo/Pinoycoop Banner.png") }}') center center / cover no-repeat;
    }

    @media (max-width: 575.98px) {
      .about-hero-banner {
        min-height: 340px;
        background-position: center top;
      }
    }
  </style>
</head>
<body data-spy="scroll" data-target="#mainNav">

@include('pinooycoop.partials.nav')

<div class="page-banner-area page-contact about-hero-banner" id="page-banner">
</div>

<section class="section-padding">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 col-sm-12 col-md-8 mb-4">
        <h3 class="mb-3">Technology made for cooperative operations</h3>
        <p class="mb-4">Pinoycoop supports MASS-SPECC member cooperatives with practical digital tools for daily operations, member service, access management, reporting, and secure system use.</p>

        <span class="h5 mb-4 d-inline-block">What Pinoycoop helps cooperatives do:</span>
        <ul class="about-list2 my-4">
          <li class="mb-2"><i class="far fa-check-circle"></i> Modernize cooperative banking and back-office workflows</li>
          <li class="mb-2"><i class="far fa-check-circle"></i> Manage digital access requests for users and internal systems</li>
          <li class="mb-2"><i class="far fa-check-circle"></i> Support member engagement through connected Pinoycoop services</li>
          <li class="mb-2"><i class="far fa-check-circle"></i> Strengthen data readiness, security, and service continuity</li>
        </ul>

        <a href="{{ route('pinooycoop.services-core') }}" class="mt-3 d-inline-block">Explore Pinoycoop services <i class="fa fa-angle-right"></i></a>
      </div>

      <div class="col-lg-6 col-md-4">
        <div class="about-logo-panel text-center">
          <img src="{{ asset('MASS-SPECC Logo/Pinoy_Coop_Logo_21.png') }}" alt="Pinoycoop logo" class="img-fluid mb-4">
          <p class="mb-0">A cooperative-first technology ecosystem for operations, access, member participation, and digital service delivery.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-padding pt-0">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <div class="media img-block mb-3 mb-lg-0">
          <img src="images/about/h-1.jpg" alt="Cooperative technology support" class="img-fluid rounded mr-3">
          <div class="media-body">
            <h4 class="mb-3">Built from cooperative needs</h4>
            <p>Pinoycoop grew from the operational requirements of cooperatives that need reliable, practical, and locally relevant digital systems.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="media img-block">
          <img src="images/about/h-2.jpg" alt="MASS-SPECC cooperative support" class="img-fluid rounded mr-3">
          <div class="media-body">
            <h4 class="mb-3">Guided by MASS-SPECC support</h4>
            <p>The platform is aligned with MASS-SPECC's work of helping cooperatives improve service delivery, governance, and technology adoption.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-padding pt-0">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 col-md-6">
        <div class="text-center border p-4 rounded mb-4">
          <span class="counter text-dark font-weight-normal" data-count="1">0</span>
          <h5 class="text-uppercase mt-2">Cooperative focus</h5>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="text-center border p-4 rounded mb-4">
          <span class="counter text-dark font-weight-normal" data-count="3">0</span>
          <h5 class="text-uppercase mt-2">Core evolution</h5>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="text-center border p-4 rounded mb-4">
          <span class="counter text-dark font-weight-normal" data-count="6">0</span>
          <h5 class="text-uppercase mt-2">Access modules</h5>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="text-center border p-4 rounded">
          <span class="counter text-dark font-weight-normal" data-count="24">0</span>
          <h5 class="text-uppercase mt-2">Support mindset</h5>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-padding" id="section-strategy">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="row">
          <div class="col-lg-6">
            <div class="mb-5">
              <span class="icon-3x text-default"><i class="icofont-layers"></i></span>
              <h4 class="my-3">Core system support</h4>
              <p>Pinoycoop Core helps cooperatives handle common financial and operational processes through a system designed for their environment.</p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="mb-5">
              <span class="icon-3x text-default"><i class="icofont-users-alt-4"></i></span>
              <h4 class="my-3">Member engagement</h4>
              <p>Services such as BLAST, REGISTER, and ELECT help cooperatives reach members, organize participation, and support assembly activities.</p>
            </div>
          </div>
          <div class="col-lg-6">
            <div>
              <span class="icon-3x text-default"><i class="icofont-ui-browser"></i></span>
              <h4 class="my-3">Secure access workflows</h4>
              <p>Access request and approval flows help administrators document user access, system assignments, and authorization records.</p>
            </div>
          </div>
          <div class="col-lg-6">
            <div>
              <span class="icon-3x text-default"><i class="icofont-shield"></i></span>
              <h4 class="my-3">Cooperative data readiness</h4>
              <p>Pinoycoop services support better reporting discipline and data preparation for connected compliance and operational needs.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-padding bg-gray">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div>
          <h3 class="mb-2">A cooperative-first digital ecosystem</h3>
          <p>Pinoycoop is not a generic software offering. It is shaped around cooperative realities: member records, access approvals, transaction alerts, reporting, assemblies, and the day-to-day need for dependable systems.</p>
        </div>
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="about-pinoycoop-card">
          <div class="about-pinoycoop-number">01</div>
          <h4 class="mt-3">For cooperative operations</h4>
          <p>Tools that help offices process work faster, reduce manual tracking, and keep access records organized.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="about-pinoycoop-card">
          <div class="about-pinoycoop-number">02</div>
          <h4 class="mt-3">For members</h4>
          <p>Services that support communication, registration, electronic participation, and better cooperative engagement.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="about-pinoycoop-card">
          <div class="about-pinoycoop-number">03</div>
          <h4 class="mt-3">For governance</h4>
          <p>Structured requests, authorization trails, and system access documentation help cooperatives manage accountability.</p>
        </div>
      </div>
    </div>
  </div>
</section>

@include('pinooycoop.partials.footer')

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>
