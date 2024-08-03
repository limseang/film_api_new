<div class="page-header-content d-lg-flex border-top py-2">
    <div class="d-flex">
        <ol class="breadcrumb">
            @foreach ($bc as $b)
               @if ($b['page'] === 'fas fa-home') 
                    <?php echo '<li class="breadcrumb-item">'.'<i class="ph-house"></i>'.'</li>'; ?>
                @elseif ($b['link'] === '#') 
                    <?php echo '<li class="breadcrumb-item">' . $b['page'] . '</li>'; ?>
                @else
                    <?php echo '<li class="breadcrumb-item "><a class="text-success" href="' . $b['link'] . '">' . $b['page'] . '</a></li>' ?>  
                @endif
            @endforeach 
         </ol>
    </div>
    <div class="collapse d-lg-block ms-lg-auto" id="breadcrumb_elements">
        <div class="d-lg-flex mb-2 mb-lg-0">
            @yield('breadcrumb-topbar')
        </div>
    </div>
</div>