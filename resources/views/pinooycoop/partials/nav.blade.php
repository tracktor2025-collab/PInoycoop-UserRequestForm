<nav class="navbar navbar-expand-lg fixed-top trans-navigation">
  <div class="container">
    <a class="navbar-brand" href="{{ route('landing') }}">
      <img src="{{ asset('pinooycoop/images/logo.png') }}" alt="" class="img-fluid b-logo">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon">
        <i class="fa fa-bars"></i>
      </span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="mainNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link smoth-scroll" href="{{ route('landing') }}" style="white-space: nowrap;">HOME</a>
        </li>
        <li class="nav-item">
          <a class="nav-link smoth-scroll" href="{{ route('pinooycoop.events') }}" style="white-space: nowrap;">EVENTS</a>
        </li>
        <li class="nav-item dropdown"style="text-border: 1px solid #111;">
          <a class="nav-link dropdown-toggle" href="{{ route('pinooycoop.service') }}" id="navbarWelcome" role="button" data-toggle="dropdown" style="white-space: nowrap;"
            aria-haspopup="true" aria-expanded="false" >
            PROGRAM & SERVICES <i class="fas fa-chevron-down"></i>
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarWelcome" >
            <li><a class="dropdown-item" href="{{ route('landing') }}">Home</a></li>
            <li><a class="dropdown-item" href="{{ route('pinooycoop.service') }}">Service</a></li>
            <li><a class="dropdown-item" href="{{ route('pinooycoop.events') }}">Events</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link smoth-scroll" href="{{ route('request.captcha') }}" style="white-space: nowrap;">FORM REQUEST</a>
        </li>
        <li class="nav-item">
          <a class="nav-link smoth-scroll" href="{{ route('pinooycoop.about') }}" style="white-space: nowrap;">ABOUT US</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
