<nav class="navbar navbar-expand-lg fixed-top trans-navigation">
  <div class="container">
    <a class="navbar-brand" href="{{ route('landing') }}">
      <img src="{{ asset('pinooycoop/images/logo.png') }}" alt="Pinoy Coop" class="img-fluid b-logo">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon">
        <i class="fa fa-bars"></i>
      </span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="mainNav">
      <ul class="navbar-nav">
        @if (! empty($primaryMenu) && $primaryMenu->activeItems->isNotEmpty())
          @foreach ($primaryMenu->activeItems as $item)
            @if ($item->activeChildren->isNotEmpty())
              <li class="nav-item dropdown" style="text-border: 1px solid #111;">
                <a class="nav-link dropdown-toggle" href="{{ $item->resolved_url }}" id="menuItem{{ $item->id }}" role="button" data-toggle="dropdown" style="white-space: nowrap;"
                  aria-haspopup="true" aria-expanded="false" target="{{ $item->target }}" @if($item->target === '_blank') rel="noopener" @endif>
                  {{ strtoupper($item->label) }} <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="menuItem{{ $item->id }}">
                  @foreach ($item->activeChildren as $child)
                    <li>
                      <a class="dropdown-item" href="{{ $child->resolved_url }}" target="{{ $child->target }}" @if($child->target === '_blank') rel="noopener" @endif>{{ $child->label === 'Services Secure & E-Store' ? 'Services Secure' : $child->label }}</a>
                    </li>
                    @if ($child->label === 'Services Secure & E-Store')
                      <li><a class="dropdown-item" href="{{ route('pinooycoop.e-store') }}">E-Store</a></li>
                    @endif
                  @endforeach
                </ul>
              </li>
            @else
              <li class="nav-item">
                <a class="nav-link smoth-scroll" href="{{ $item->resolved_url }}" target="{{ $item->target }}" @if($item->target === '_blank') rel="noopener" @endif style="white-space: nowrap;">{{ strtoupper($item->label) }}</a>
              </li>
            @endif
          @endforeach
        @else
          <li class="nav-item">
            <a class="nav-link smoth-scroll" href="{{ route('landing') }}" style="white-space: nowrap;">HOME</a>
          </li>
          <li class="nav-item">
            <a class="nav-link smoth-scroll" href="{{ route('pinooycoop.events') }}" style="white-space: nowrap;">EVENTS</a>
          </li>
          <li class="nav-item dropdown" style="text-border: 1px solid #111;">
            <a class="nav-link dropdown-toggle" href="{{ route('pinooycoop.service') }}" id="navbarWelcome" role="button" data-toggle="dropdown" style="white-space: nowrap;"
              aria-haspopup="true" aria-expanded="false">
              PROGRAM &amp; SERVICES <i class="fas fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarWelcome">
              <li><a class="dropdown-item" href="{{ route('pinooycoop.services-core') }}">Services Core</a></li>
              <li><a class="dropdown-item" href="{{ route('pinooycoop.services-secure-estore') }}">Services Secure</a></li>
              <li><a class="dropdown-item" href="{{ route('pinooycoop.e-store') }}">E-Store</a></li>
              <li><a class="dropdown-item" href="{{ route('pinooycoop.e-services') }}">E-Services</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link smoth-scroll" href="{{ route('request.captcha') }}" style="white-space: nowrap;">FORM REQUEST</a>
          </li>
          <li class="nav-item">
            <a class="nav-link smoth-scroll" href="{{ route('pinooycoop.about') }}" style="white-space: nowrap;">ABOUT US</a>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>
