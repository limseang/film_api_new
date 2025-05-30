<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

    <?php $currentURL = url()->current(); ?>
    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Sidebar header -->
        <div class="sidebar-section">
            <div class="sidebar-section-body d-flex justify-content-center">
                <h5 class="sidebar-resize-hide flex-grow-1 my-auto">CinemagicKH</h5>

                <div>
                    <button type="button" class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                        <i class="ph-arrows-left-right"></i>
                    </button>

                    <button type="button" class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-lg-none">
                        <i class="ph-x"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- /sidebar header -->


        <!-- Main navigation -->
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Mains</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{route('dashboard')}}" class="nav-link  {{request()->is('dashboard') ? ' active-side ' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <span>
                            {{__('global.dashboard')}}
                        </span>
                    </a>
                </li>
                {{-- People --}}

                <?php $arrRoutePeople = [
                route('artist.index'),
                route('artist.create'),
                request()->is('admin/artist/edit/*'),
                route('user.index'),
                route('user.create'),
                request()->is('admin/user/edit/*')
                ]; ?>
                @if(authorize('can view artist') || authorize('can view user'))
                <li class="nav-item nav-item-submenu @if(in_array($currentURL, $arrRoutePeople)) nav-item-open @else '' @endif">
                    <a href="" class="nav-link @if(in_array($currentURL, $arrRoutePeople)) active @else '' @endif">
                        <i class="ph-user"></i>
                        <span>{{__('global.people')}}</span>
                    </a>
                    <ul class="nav-group-sub collapse  @if(in_array($currentURL, $arrRoutePeople)) show @else '' @endif">
                        <?php $arrRouteRole = [route('user.index'), route('user.create'), request()->is('admin/user/edit/*')]; ?>
                        @if(authorize('can view user'))
                        <li class="nav-item">
                            <a href="{{route('user.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteRole)) active-side @else '' @endif">
                                {{__('global.user')}}
                            </a>
                        </li>
                        @endif
                        @if(authorize('can view artist'))
                        <?php $arrRouteArtist = [route('artist.index'), route('artist.create'), request()->is('admin/artist/edit/*')]; ?>
                        <li class="nav-item">
                            <a href="{{route('artist.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteArtist)) active-side @else '' @endif">
                                {{__('sma.artist')}}
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(authorize('can view director'))
                <?php $arrRouteDirector = [route('director.index'), route('director.create'), request()->is('admin/director/edit/*')]; ?>
                <li class="nav-item">
                    <a href="{{route('director.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteDirector)) active-side @else '' @endif">
                        <i class="fa fa-user-tie"></i>
                        <span>
                            {{__('sma.director')}}
                        </span>
                    </a>
                </li>
                @endif
                @if(authorize('can view available in'))
                <?php $arrRouteAvailableIn = [route('available_in.index'), route('available_in.create'), request()->is('admin/available_in/edit/*'), request()->is('admin/available_in/assign-film/*')]; ?>
                <li class="nav-item">
                    <a href="{{route('available_in.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteAvailableIn)) active-side @else '' @endif">
                        <i class="ph-folder-notch-open"></i>
                        <span>
                            {{__('sma.cinema')}}
                        </span>
                    </a>
                </li>
                @endif
                @if(authorize('can view cinema branch'))
                <?php $arrRouteCinemaBranch = [route('cinema_branch.index'), route('cinema_branch.create'), request()->is('admin/cinema_branch/edit/*')]; ?>
                <li class="nav-item">
                    <a href="{{route('cinema_branch.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteCinemaBranch)) active-side @else '' @endif">
                        <i class="ph-buildings"></i>
                        <span>
                            {{__('sma.cinema_branch')}}
                        </span>
                    </a>
                </li>
                @endif
                @if(authorize('can view gift'))
                <?php $arrRouteGift = [route('gift.index'), route('gift.create'), request()->is('admin/gift/edit/*')]; ?>
                <li class="nav-item">
                    <a href="{{route('gift.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteGift)) active-side @else '' @endif">
                        <i class="ph-gift"></i>
                        <span>
                            {{__('sma.gift')}}
                        </span>
                    </a>
                </li>
                @endif
                @if(authorize('can view random gift'))
                <?php $arrRouteRandomGift = [route('random_gift.index')]; ?>
                <li class="nav-item">
                    <a href="{{route('random_gift.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteRandomGift)) active-side @else '' @endif">
                        <i class="ph-confetti"></i>
                        <span>
                            {{__('sma.random_gift')}}
                        </span>
                    </a>
                </li>
                @endif
                {{-- Artical --}}
                  <?php $arrRouteArtical = [
                    route('artical.index'),
                    route('artical.create'),
                    request()->is('/edit/*'),
                    route('origin.index'),
                    route('origin.create'),
                    request()->is('admin/origin/edit/*')
                    ]; ?>
                    @if(authorize('can view artical') || authorize('can view origin'))
                    <li class="nav-item nav-item-submenu @if(in_array($currentURL, $arrRouteArtical)) nav-item-open @else '' @endif">
                        <a href="" class="nav-link @if(in_array($currentURL, $arrRouteArtical)) active @else '' @endif">
                            <i class="ph-article"></i>
                            <span>{{__('sma.artical')}}</span>
                        </a>
                        <ul class="nav-group-sub collapse  @if(in_array($currentURL, $arrRouteArtical)) show @else '' @endif">
                            <?php $arrRouteArticalList = [route('artical.index'),route('artical.create'),request()->is('/edit/*')]; ?>
                            @if(authorize('can view artical'))
                                <li class="nav-item">
                                    <a href="{{route('artical.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteArticalList)) active-side @else '' @endif">
                                        {{__('sma.list_artical')}}
                                    </a>
                                </li>
                            @endif
                            <?php $arrRouteOrigin = [route('origin.index'), route('origin.create'), request()->is('admin/origin/edit/*')]; ?>
                            @if(authorize('can view origin'))
                            <li class="nav-item">
                                <a href="{{route('origin.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteOrigin)) active-side @else '' @endif">
                                    {{__('sma.origin')}}
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                {{-- End Aritcal --}}
                {{-- Film --}}
                <?php $arrRouteFilm = [
                route('film.index'),
                route('film.create'),
                request()->is('admin/film/edit/*'),
                request()->is('admin/film/show-episode/*'),
                route('cast.index'),
                route('cast.create'),
                request()->is('admin/cast/edit/*'),
                request()->is('admin/episode/create/*'),
                request()->is('admin/episode/edit/*'),
                request()->is('admin/episode/add-subtitle/*'),
                request()->is('admin/episode/edit-subtitle/*'),
                request()->is('admin/film/assign-available/*')
                ]; ?>
                @if(authorize('can view film') || authorize('can view cast'))
                <li class="nav-item nav-item-submenu @if(in_array($currentURL, $arrRouteFilm)) nav-item-open @else '' @endif">
                    <a href="" class="nav-link @if(in_array($currentURL, $arrRouteFilm)) active @else '' @endif">
                        <i class="fa fa-video"></i>
                        <span>{{__('sma.film')}}</span>
                    </a>
                    <ul class="nav-group-sub collapse  @if(in_array($currentURL, $arrRouteFilm)) show @else '' @endif">
                        <?php $arrRouteFilmList = [route('film.index'), route('film.create'), request()->is('admin/film/edit/*'),  request()->is('admin/film/show-episode/*'),  request()->is('admin/episode/create/*'), request()->is('admin/episode/edit/*'), request()->is('admin/episode/add-subtitle/*'),
                    request()->is('admin/episode/edit-subtitle/*'), request()->is('admin/film/assign-available/*')]; ?>
                        @if(authorize('can view film'))
                        <li class="nav-item">
                            <a href="{{route('film.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteFilmList)) active-side @else '' @endif">
                                {{__('sma.list_film')}}
                            </a>
                        </li>
                        @endif
                        @if(authorize('can view cast'))
                        <?php $arrRouteCast = [route('cast.index'), route('cast.create'), request()->is('admin/cast/edit/*')]; ?>
                        <li class="nav-item">
                            <a href="{{route('cast.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteCast)) active-side @else '' @endif">
                                {{__('sma.cast')}}
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(authorize('can view report income and expense'))
                <?php $arrRouteExpense = [route('report_income_expense.index'), route('report_income_expense.create'), request()->is('admin/report_income_expense/edit/*')]; ?>
                <li class="nav-item">
                    <a href="{{route('report_income_expense.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteExpense)) active-side @else '' @endif">
                        <i class="ph-currency-circle-dollar"></i>
                        <span>
                            {{__('sma.report_income_and_expense')}}
                        </span>
                    </a>
                </li>
                @endif
                {{-- End Film --}}
                <?php $arrRouteSetting = [route('role.index'),
                route('role.create'),
                request()->is('admin/role/edit/*'),
                request()->is('admin/role/permission/*'),
                route('system_log.index'),
                route('type.index'),
                route('type.create'),
                request()->is('admin/type/edit/*'),
                route('tag.index'),
                route('tag.create'),
                request()->is('admin/tag/edit/*'),
                route('category.index'),
                route('category.create'),
                request()->is('admin/category/edit/*'),
                route('distributor.index'),
                route('distributor.create'),
                request()->is('admin/distributor/edit/*'),
                route('genre.index'),
                route('genre.create'),
                request()->is('admin/genre/edit/*'),
                route('version.index'),
                route('version.create'),
                request()->is('admin/version/edit/*')
                ]; ?>
                @if(authorize('can view role') || authorize('can view type') || authorize('can view tag') || authorize('can view distributor') || authorize('can view category') || authorize('can view genre') || authorize('can view version') || authorize('can view system user log'))
                <li class="nav-item nav-item-submenu @if(in_array($currentURL, $arrRouteSetting)) nav-item-open @else '' @endif">
                    <a href="" class="nav-link @if(in_array($currentURL, $arrRouteSetting)) active @else '' @endif">
                        <i class="fa fa-cog"></i>
                        <span>{{__('global.setting')}}</span>
                    </a>
                    <ul class="nav-group-sub collapse  @if(in_array($currentURL, $arrRouteSetting)) show @else '' @endif">
                        <?php $arrRouteRole = [route('role.index'), route('role.create'), request()->is('admin/role/edit/*'), request()->is('admin/role/permission/*')]; ?>
                        @if(authorize('can view role'))
                        <li class="nav-item">
                            <a href="{{route('role.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteRole)) active-side @else '' @endif">
                                {{__('global.role')}}
                            </a>
                        </li>
                        @endif
                        <?php $arrRouteType = [route('type.index'), route('type.create'), request()->is('admin/type/edit/*')]; ?>
                        @if(authorize('can view type'))
                            <li class="nav-item">
                                <a href="{{route('type.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteType)) active-side @else '' @endif">
                                    {{__('sma.type')}}
                                </a>
                            </li>
                        @endif
                        <?php $arrRouteTag = [route('tag.index'), route('tag.create'), request()->is('admin/tag/edit/*')]; ?>
                        @if(authorize('can view tag'))
                            <li class="nav-item">
                                <a href="{{route('tag.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteTag)) active-side @else '' @endif">
                                    {{__('sma.tag')}}
                                </a>
                            </li>
                        @endif
                        <?php $arrRouteDistributor = [route('distributor.index'), route('distributor.create'), request()->is('admin/distributor/edit/*')]; ?>
                        @if(authorize('can view distributor'))
                        <li class="nav-item">
                            <a href="{{route('distributor.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteDistributor)) active-side @else '' @endif">
                                {{__('sma.distributor')}}
                            </a>
                        </li>
                        @endif
                        <?php $arrRouteCategory = [route('category.index'), route('category.create'), request()->is('admin/category/edit/*')]; ?>
                        @if(authorize('can view category'))
                            <li class="nav-item">
                                <a href="{{route('category.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteCategory)) active-side @else '' @endif">
                                    {{__('sma.category_film')}}
                                </a>
                            </li>
                        @endif
                        <?php $arrRouteGenre = [route('genre.index'), route('genre.create'), request()->is('admin/genre/edit/*')]; ?>
                        @if(authorize('can view genre'))
                            <li class="nav-item">
                                <a href="{{route('genre.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteGenre)) active-side @else '' @endif">
                                    {{__('sma.genre')}}
                                </a>
                            </li>
                        @endif
                        <?php $arrRouteVersion = [route('version.index'), route('version.create'), request()->is('admin/version/edit/*')]; ?>
                        @if(authorize('can view version'))
                            <li class="nav-item">
                                <a href="{{route('version.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteVersion)) active-side @else '' @endif">
                                    {{__('sma.version')}}
                                </a>
                            </li>
                        @endif
                        <?php $arrRouteSystemLog = [route('system_log.index')]; ?>
                        @if(authorize('can view system user log'))
                            <li class="nav-item">
                                <a href="{{route('system_log.index')}}" class="nav-link @if(in_array($currentURL, $arrRouteSystemLog)) active-side @else '' @endif"">
                                    {{__('global.user_system_log')}}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                @endif
                <!-- /layout -->


            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>
