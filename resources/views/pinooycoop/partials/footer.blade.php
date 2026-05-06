<!--  FOOTER AREA START  -->
<style>
  #footer.mass-footer {
    background: #20251e;
    color: #ffffff;
    padding: 32px 0 0;
  }

  .mass-footer .container {
    max-width: 1080px;
  }

  .mass-footer a {
    color: inherit;
  }

  .mass-footer-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    margin-bottom: 42px;
  }

  .mass-footer-brand {
    display: block;
    max-width: 700px;
  }

  .mass-footer-brand img {
    display: block;
    width: 100%;
    height: auto;
  }

  .mass-footer-actions {
    display: flex;
    align-items: center;
    gap: 96px;
  }

  .mass-footer-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-width: 180px;
    min-height: 46px;
    padding: 12px 24px;
    border-radius: 24px;
    background: #2398dc;
    color: #ffffff;
    font-weight: 700;
    font-size: 13px;
    line-height: 1;
    text-transform: uppercase;
  }

  .mass-footer-btn:hover {
    color: #ffffff;
    background: #1688ca;
  }

  .mass-footer-main {
    display: grid;
    grid-template-columns: minmax(320px, 1fr) 220px;
    justify-content: space-between;
    gap: 56px;
    padding-bottom: 22px;
  }

  .mass-footer-title {
    color: #139cff;
    font-size: 15px;
    font-weight: 400;
    line-height: 1.25;
    margin: 0 0 8px;
    text-transform: uppercase;
  }

  .mass-footer-contact-list li,
  .mass-footer-social-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: #ffffff;
    font-size: 16px;
    line-height: 1.35;
    margin-bottom: 10px;
  }

  .mass-footer-contact-list i {
    color: #ffffff;
    font-size: 17px;
    width: 18px;
    line-height: 1.35;
    text-align: center;
  }

  .mass-footer-office {
    display: block;
    color: #139cff;
    margin-bottom: 2px;
  }

  .mass-footer-social-list a {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    color: #bfc1bd;
    font-size: 16px;
    line-height: 1.35;
  }

  .mass-footer-social-list i {
    color: #139cff;
    font-size: 21px;
    width: 26px;
    text-align: center;
  }

  .mass-footer-bottom {
    border-top: 3px solid #1498df;
    padding: 22px 15px 20px;
    text-align: center;
    color: #ffffff;
    font-size: 17px;
    line-height: 1.35;
  }

  @media (max-width: 991px) {
    .mass-footer-top {
      align-items: flex-start;
      flex-direction: column;
      margin-bottom: 40px;
    }

    .mass-footer-brand {
      max-width: 100%;
    }

    .mass-footer-actions {
      width: 100%;
      justify-content: space-between;
    }

    .mass-footer-main {
      grid-template-columns: 1fr;
      gap: 34px;
    }
  }

  @media (max-width: 575px) {
    .mass-footer-actions {
      align-items: stretch;
      flex-direction: column;
      gap: 14px;
    }

    .mass-footer-btn {
      width: 100%;
    }

    .mass-footer-contact-list li,
    .mass-footer-social-list a {
      font-size: 15px;
    }

    .mass-footer-bottom {
      font-size: 15px;
    }
  }
</style>

<section id="footer" class="mass-footer">
  <div class="container">
    <div class="mass-footer-top">
      <a class="mass-footer-brand" href="{{ route('landing') }}" aria-label="MASS-SPECC home">
        <img src="{{ asset('pinooycoop/images/Mass-specc-logo-with-60th-anniv.webp') }}" alt="MASS-SPECC COOP 60th Anniversary">
      </a>

      <div class="mass-footer-actions">
        <a class="mass-footer-btn" href="{{ route('pinooycoop.contact') }}">
          <i class="far fa-envelope"></i>
          <span>Contact Us</span>
        </a>
      </div>
    </div>

    <div class="mass-footer-main">
      <div class="mass-footer-contact">
        <h4 class="mass-footer-title">Main Office</h4>
        <ul class="mass-footer-contact-list">
          <li><i class="fas fa-map-marker-alt"></i><span>Tiano-Yacapin Sts., Cagayan de Oro City, Philippines, 9000</span></li>
          <li><i class="fas fa-phone-alt"></i><span>(088) 326-4617</span></li>
          <li><i class="fas fa-map-marker-alt"></i><span><span class="mass-footer-office">Davao Office</span>Anahaw Village, Anahaw Road, Ma-a, Davao City, Philippines, 8000</span></li>
          <li><i class="fas fa-phone-alt"></i><span>(084) 244-1096</span></li>
          <li><i class="fas fa-phone-alt"></i><span><span class="mass-footer-office">Helpdesk:</span>0967-448-4743</span></li>
          <li><i class="far fa-envelope"></i><a href="mailto:msu@mass-specc.com">msu@mass-specc.com</a></li>
        </ul>
      </div>

      <div class="mass-footer-social">
        <h4 class="mass-footer-title">Follow Us</h4>
        <ul class="mass-footer-social-list">
          <li><a href="https://www.facebook.com/MASS.SPECC" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook"></i><span>Facebook</span></a></li>
          <li><a href="https://www.instagram.com/mass.specc/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i><span>Instagram</span></a></li>
          <li><a href="https://www.youtube.com/@MSP_Coop" target="_blank" rel="noopener" aria-label="Youtube"><i class="fab fa-youtube"></i><span>Youtube</span></a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="mass-footer-bottom">
    Copyright &copy; {{ date('Y') }} MASS-SPECC Cooperative Development Center
  </div>
</section>
<!--  FOOTER AREA END  -->
