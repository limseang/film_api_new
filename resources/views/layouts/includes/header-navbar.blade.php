<div class="navbar navbar-dark navbar-expand-lg navbar-static border-bottom border-bottom-white border-opacity-10">
    <div class="container-fluid">
        <div class="d-flex d-lg-none me-2">
            <button type="button" class="navbar-toggler sidebar-mobile-main-toggle rounded-pill">
                <i class="ph-list"></i>
            </button>
        </div>

        <div class="navbar-brand flex-1 flex-lg-0">
            <a href="index.html" class="d-inline-flex align-items-center">
                <img src="../../../assets/images/logo_icon.svg" alt="">
                <img src="../../../assets/images/logo_text_light.svg" class="d-none d-sm-inline-block h-16px ms-3" alt="">
            </a>
        </div>
        <ul class="nav flex-row justify-content-end order-1 order-lg-2">
            <li class="nav-item nav-item-dropdown-lg dropdown language-switch">
                @php
                    $language_key = ['en' => __('sma.english'), 'km' => __('sma.khmer')];
                @endphp
                <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="rounded" src="{{ asset('img/'.App::getLocale().'.png') }}" height="22" width="36" alt="">
                    <span class="ms-2 d-none d-lg-inline-block me-1">{{ (App::getLocale() == 'en') ? $language_key['en'] : $language_key[App::getLocale()]  }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('lang', 'km' )}}" class="dropdown-item kh {{ (App::getLocale()  == 'km') ? 'active' : ''}} ">
                        <img class="rounded"  src="{{ asset('img/km.png') }}" height="22" width="36"  alt="">
                        <span class="ms-2">{{ $language_key['km'] }}</span>
                    </a>
                    <a href="{{ route('lang', 'en' )}}" class="dropdown-item en {{ (App::getLocale()  == 'en') ? 'active' : ''}} ">
                        <img class="rounded"  src="{{ asset('img/en.png') }}" height="22" width="36" alt="">
                        <span class="ms-2">{{ $language_key['en'] }}</span>
                    </a>
                </div>
            </li>

            <li class="nav-item nav-item-dropdown-lg dropdown ms-lg-2">
                <a href="#" class="navbar-nav-link align-items-center rounded-pill p-1" data-bs-toggle="dropdown">
                    <div class="status-indicator-container">
                        <img src="{{asset('img/default_profile.jpg')}}" class="w-32px h-32px rounded-pill" alt="">
                        <span class="status-indicator bg-success"></span>
                    </div>
                    <span class="d-none d-lg-inline-block mx-lg-2">{{Auth::user()->name}}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-end">
                    <a href="#" class="dropdown-item">
                        <i class="ph-user-circle me-2"></i>
                        My profile
                    </a>
                    <a href="{{route('logout')}}" class="dropdown-item">
                        <i class="ph-sign-out me-2"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>