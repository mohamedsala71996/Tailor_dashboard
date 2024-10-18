<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard.home') }}" class="nav-link">{{ trans('admin.Home') }}</a>
        </li>
    </ul>

    {{-- @if (auth('user')->user()->has_permission('تبديل-المتاجر')) <!-- Check if the user has permission --> --}}
    <!-- Center navbar links (for store switching) -->
    <ul class="navbar-nav mx-auto"> <!-- Added mx-auto to center this section -->
        {{-- @if(auth('user')->user()->role == 'admin') <!-- Only show if user is an admin --> --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-store"></i> تغيير المتجر
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                @php
                $stores = auth('user')->user()->role == 'admin' || auth('user')->user()->role == 'supervisor'
                    ? App\Models\Store::all()
                    : auth('user')->user()->stores;
                @endphp
                
                @if ( $stores)
                @foreach($stores as $store)
                <a href="{{ route('dashboard.stores.switch', $store->id) }}" class="dropdown-item">
                    {{ $store->name }}
                </a>
            @endforeach
                {{-- @endif --}}

            </div>
        </li>
        {{-- @endif --}}
    </ul>
 @endif

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <div style="position: relative; top: -10px;">
                    <img src="{{ auth('user')->user()->getImage() }}" style="width: 41px;" class="img-circle" alt="User Image">
                    {{ auth('user')->user()->name }}
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                {{-- <a href="{{ route('dashboard.profile.edit') }}" class="dropdown-item">
                    {{ trans('admin.Profile') }}
                </a> --}}
                <a href="{{ route('dashboard.logout') }}" class="dropdown-item">
                    {{ trans('admin.logout') }}
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
