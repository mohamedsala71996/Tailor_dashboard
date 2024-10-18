@extends('layouts.admin')

@section('title', "المتاجر")

@section('content')
<style>

.photo-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 120px; /* Fixed width */
    height: 120px; /* Fixed height */
}

.fixed-size {
    width: 100%; /* Ensure the image fits the container */
    height: 100%; /* Ensure the image fits the container */
    object-fit: cover; /* Maintain aspect ratio and crop if necessary */
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.fixed-size:hover {
    transform: scale(1.05); /* Slight hover effect */
}

</style>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">المتاجر</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item">{{ trans('admin.Home') }}</li> --}}
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Form for Creating and Updating Stores -->
            <div class="card-body">
                <form action="{{ isset($store) ? route('dashboard.stores.update', $store->id) : route('dashboard.stores.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($store))
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label for="name">الاسم</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', isset($store) ? $store->name : '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="desc">الوصف</label>
                        <input type="text" class="form-control @error('desc') is-invalid @enderror" id="desc" name="desc" value="{{ old('desc', isset($store) ? $store->desc : '') }}">
                        @error('desc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="photo">الصورة</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <!-- Display the existing photo when editing -->
                        @if(isset($store) && $store->photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $store->photo) }}" alt="Store Photo" style="max-width: 150px;">
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">{{ isset($store) ? 'تعديل المتجر' : 'انشاء المتجر' }}</button>
                </form>
            </div>

            <!-- Table for Displaying Stores -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">المتاجر</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>الاسم</th>
                                <th>الوصف</th>
                                <th>الصورة</th>
                                <th>الاجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stores as $store)
                                <tr>
                                    <td>{{ $store->id }}</td>
                                    <td>{{ $store->name }}</td>
                                    <td>{{ $store->desc }}</td>
                                    <td>
                                        @if($store->photo)
                                            <div class="photo-container">
                                                <img src="{{ asset('storage/' . $store->photo) }}" alt="Store Photo" class="img-thumbnail fixed-size">
                                            </div>
                                        @else
                                            <span>لا توجد صورة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.stores.edit', $store->id) }}" class="btn btn-warning btn-sm">تعديل</a>

                                        <!-- Button to trigger modal -->
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" data-id="{{ $store->id }}">
                                            حذف
                                        </button>
                                                    <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            هل أنت متأكد أنك تريد حذف هذا المتجر؟
                        </div>
                        <div class="modal-footer">
                            <form id="deleteForm" action="{{ route('dashboard.destroy.store',$store->id) }}" method="POST">
                                @csrf
                                {{-- @method('DELETE') --}}
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-danger">حذف</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد متاجر</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>



        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('scripts')
    <script>
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var storeId = button.data('id') // Extract info from data-* attributes
            // var actionUrl = "{{ url('dashboard/stores/delete/store') }}/" + storeId;
            var modal = $(this)
            modal.find('#deleteForm').attr('action', actionUrl)
        })
    </script>
@endsection
