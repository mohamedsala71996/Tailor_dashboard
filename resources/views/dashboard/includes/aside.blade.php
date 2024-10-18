@php
    use App\Models\User;
@endphp
<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image">
            @if (isset(auth('user')->user()->store->photo))
            <img src="{{ asset('storage/' . auth('user')->user()->store->photo) }}"
                 class="img-circle elevation-2 shadow"
                 alt="Store Image"
                 style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #fff;">
                 @endif
        </div>
        <div class="info ml-0"> <!-- تقليل الـ margin-left -->
        @if (isset(auth('user')->user()->store->name))
            <a href="#" class="d-block font-weight-bold"
               style="color: #ffffff; font-size: 16px;">
               <i class="fas fa-store mr-1"></i> <!-- أيقونة المتجر -->
               {{ auth('user')->user()->store->name }}
            </a>
        @endif
    </div>

    </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{route('dashboard.home')}}" class="nav-link {{request()->is('*/dashboard')? 'active':''}}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                الرئيسية
              </p>
            </a>
          </li>
   {{-- Stores Management --}}
   @if (auth('user')->user()->has_permission('قراءة-المتاجر')) <!-- Check if the user has permission -->
   <li class="nav-item">
    <a href="{{ route('dashboard.stores.index') }}" class="nav-link {{ request()->routeIs('dashboard.stores.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-store"></i>
        <p>
           المتاجر
        </p>
    </a>
</li>
@endif
          {{-- menu-open --}}
          @if (auth('user')->user()->has_permission('قراءة-المستخدمين'))
            <li class="nav-item {{(request()->routeIs('dashboard.users.*') || request()->routeIs('dashboard.roles.*'))? 'menu-open':''}}">
              <a href="#" class="nav-link">
                <i class="fas fa-user"></i>
                <p>
                  {{ trans('admin.Users Mangement') }}
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('dashboard.users.index')}}" class="nav-link {{(request()->routeIs('dashboard.users.*'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Users') }}</p>
                  </a>
                </li>
                @endif

                @if (auth('user')->user()->has_permission('قراءة-الادوار'))

                <li class="nav-item">
                  <a href="{{route('dashboard.roles.index')}}" class="nav-link {{( request()->routeIs('dashboard.roles.*'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Roles') }} </p>
                  </a>
                </li>
              </ul>
            </li>
          @endif

          {{-- @if (auth('user')->user()->has_permission('read-categories'))
            <li class="nav-item">
              <a href="{{route('dashboard.categories.index')}}" class="nav-link {{request()->routeIs('dashboard.categories.*')? 'active':''}}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  {{ trans('admin.Categories') }}
                </p>
              </a>
            </li>
          @endif --}}

          @if (auth('user')->user()->has_permission('قراءة-المنتجات') && isset(auth('user')->user()->store))
            <li class="nav-item">
              <a href="{{route('dashboard.products.index')}}" class="nav-link {{request()->routeIs('dashboard.products.*')? 'active':''}}">
                <i class="nav-icon fas fa-box"></i> <!-- Updated to fa-box -->
                <p>
                  {{ trans('admin.products') }}
                </p>
              </a>
            </li>
          @endif

          @php
          $tailors = User::where('store_id', auth()->user()->store_id)->where('role', 'tailor')->get();
          $currentTailorId = request()->route('tailor_id'); // Assuming your route uses 'tailor_id' as a parameter
      @endphp

        @foreach ($tailors as $tailor)
        @if (auth()->user()->id==$tailor->id || auth()->user()->role=='admin'|| auth()->user()->role=='supervisor' )
        {{-- @if ((auth()->user()->id == $tailor->id || auth()->user()->role == 'admin' || auth()->user()->role == 'supervisor')) --}}

        <li class="nav-item">
            <a href="{{ route('dashboard.orders.index', $tailor->id) }}" class="nav-link {{ $currentTailorId == $tailor->id ? 'active' : '' }}">
                <i class="nav-icon fas fa-cut"></i>
                <p>
                    {{ $tailor->name }}
                </p>
            </a>
        </li>
        @endif

        @endforeach

          {{-- @if (auth('user')->user()->has_permission('read-sizes')) --}}
            {{-- <li class="nav-item">
              <a href="{{route('dashboard.sizes.index')}}" class="nav-link {{request()->routeIs('dashboard.sizes.*')? 'active':''}}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  {{ trans('admin.sizes') }}
                </p>
              </a>
            </li> --}}
          {{-- @endif --}}

          {{-- @if (auth('user')->user()->has_permission('read-settings'))
            <li class="nav-item">
              <a href="{{route('dashboard.settings.edit')}}" class="nav-link {{request()->routeIs('dashboard.settings.*')? 'active':''}}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  {{ trans('admin.Settings') }}
                </p>
              </a>
            </li>
          @endif
        </ul> --}}
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
