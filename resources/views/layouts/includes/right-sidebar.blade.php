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
                    <a href="{{route('dashboard')}}" class="nav-link  {{request()->is('dashboard') ? ' active ' : '' }}">
                        <i class="ph-house"></i>
                        <span>
                            {{__('global.dashboard')}}
                        </span>
                    </a>
                </li>
                {{-- People --}}
                <?php $arrRoutePeople = [
                route('director.index'),
                route('director.create'), 
                request()->is('admin/director/edit/*'), 
                ]; ?>
                <li class="nav-item nav-item-submenu @if(in_array($currentURL, $arrRoutePeople)) nav-item-open @else '' @endif">
                    <a href="" class="nav-link @if(in_array($currentURL, $arrRoutePeople)) active @else '' @endif">
                        <i class="ph-user"></i>
                        <span>{{__('global.people')}}</span>
                    </a>
                    <ul class="nav-group-sub collapse  @if(in_array($currentURL, $arrRoutePeople)) show @else '' @endif">
                        <?php $arrRouteRole = [route('role.index'), route('role.create'), request()->is('admin/role/edit/*'), request()->is('admin/role/permission/*')]; ?>
                        <li class="nav-item">
                            <a href="{{route('role.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteRole)) active @else '' @endif">
                                {{__('global.user')}}
                            </a>
                        </li>
                        <?php $arrRouteDirector = [route('director.index'), route('director.create'), request()->is('admin/type/director/*')]; ?>
                        <li class="nav-item">
                            <a href="{{route('director.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteDirector)) active @else '' @endif">
                                {{__('sma.director')}}
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- end People --}}
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
                ]; ?>
                <li class="nav-item nav-item-submenu @if(in_array($currentURL, $arrRouteSetting)) nav-item-open @else '' @endif">
                    <a href="" class="nav-link @if(in_array($currentURL, $arrRouteSetting)) active @else '' @endif">
                        <i class="fa fa-cog"></i>
                        <span>{{__('global.setting')}}</span>
                    </a>
                    <ul class="nav-group-sub collapse  @if(in_array($currentURL, $arrRouteSetting)) show @else '' @endif">
                        <?php $arrRouteRole = [route('role.index'), route('role.create'), request()->is('admin/role/edit/*'), request()->is('admin/role/permission/*')]; ?>
                        <li class="nav-item">
                            <a href="{{route('role.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteRole)) active @else '' @endif">
                                {{__('global.role')}}
                            </a>
                        </li>
                        <?php $arrRouteType = [route('type.index'), route('type.create'), request()->is('admin/type/edit/*')]; ?>
                        <li class="nav-item">
                            <a href="{{route('type.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteType)) active @else '' @endif">
                                {{__('sma.type')}}
                            </a>
                        </li>
                        <?php $arrRouteTag = [route('tag.index'), route('tag.create'), request()->is('admin/tag/edit/*')]; ?>
                        <li class="nav-item">
                            <a href="{{route('tag.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteTag)) active @else '' @endif">
                                {{__('sma.tag')}}
                            </a>
                        </li>
                        <?php $arrRouteCategory = [route('category.index'), route('category.create'), request()->is('admin/category/edit/*')]; ?>
                        <li class="nav-item">
                            <a href="{{route('category.index')}}" class="nav-link  @if(in_array($currentURL, $arrRouteCategory)) active @else '' @endif">
                                {{__('sma.category_film')}}
                            </a>
                        </li>
                        <?php $arrRouteSystemLog = [route('system_log.index')]; ?>
                        <li class="nav-item">
                            <a href="{{route('system_log.index')}}" class="nav-link @if(in_array($currentURL, $arrRouteSystemLog)) active @else '' @endif"">
                                {{__('global.user_system_log')}}
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- /layout -->


            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->
    
</div>