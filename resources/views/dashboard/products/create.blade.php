@extends('layouts.admin')

@section('title', trans('admin.products'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.products') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> / <a
                                href="{{ route('dashboard.products.index') }}">{{ trans('admin.products') }}</a> /
                            {{ trans('admin.Create') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        {{-- <div class="card-header">
                            <h3 class="card-title">{{ trans('admin.Create') }}</h3>
                        </div> --}}
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('dashboard.products.store') }}" enctype="multipart/form-data">
                            @csrf
                            @include('dashboard.products.form')
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.Add') }}</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div><!-- /.container-fluid -->

            <!-- Product Table -->
            {{-- <div class="card-body">
                <table id="table" class="table table-bordered table-striped data-table responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><input type="checkbox" id="selectAllProduct" /></th>
                            <th>{{ trans('admin.Name') }}</th>
                            <th>{{ trans('admin.Photo') }}</th>
                            <th>{{ trans('admin.Created at') }}</th>
                            <th>{{ trans('admin.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><input type="checkbox" class="selectProduct" value="{{ $product->id }}" /></td>
                                <td>{{ $product->name }}</td>
                                <td>
                                    @if ($product->photo)
                                        <img src="{{ asset('uploads/photos/' . $product->photo) }}"
                                            alt="{{ $product->name }}" width="100" />
                                    @else
                                        <span>{{ trans('admin.No Photo') }}</span>
                                    @endif
                                </td>
                                <td>{{ $product->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#editProductModal{{ $product->id }}" data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}" data-photo="{{ $product->photo }}">
                                        {{ trans('admin.Edit') }}
                                    </button>

                                    <!-- Edit Product Modal -->
                                    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1"
                                        aria-labelledby="editProductModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editProductModalLabel">
                                                        {{ trans('admin.Edit Product') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Edit Form -->
                                                    <form id="editProductForm{{ $product->id }}" method="POST"
                                                        action="{{ route('dashboard.products.update', $product->id) }}"
                                                        enctype="multipart/form-data">
                                                        @csrf

                                                        <div class="form-group">
                                                            <label for="productPhoto">{{ trans('admin.Photo') }}</label>
                                                            <input type="file" class="form-control"
                                                                id="productPhoto{{ $product->id }}" name="photo">
                                                            <img id="productPhotoPreview{{ $product->id }}" src="#"
                                                                alt="Photo Preview" width="100" style="display:none;">
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">{{ trans('admin.Close') }}</button>
                                                            <button type="submit"
                                                                class="btn btn-primary">{{ trans('admin.Save changes') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Form -->
                                    <form action="{{ route('dashboard.products.destroy', $product->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('{{ trans('admin.Are you sure?') }}')">{{ trans('admin.Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> --}}
        </div>
    </section>



@endsection

@section('scripts')
    <script>
        $('#editProductModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var productId = button.data('id'); // Extract info from data-* attributes
            var productName = button.data('name');
            var productPhoto = button.data('photo');

            var modal = $(this);
            // Dynamically update the form action with the correct product ID
            modal.find('#editProductForm' + productId).attr('action',
                '{{ route('dashboard.products.update', ':id') }}'.replace(':id', productId));
            modal.find('#productName').val(productName); // Set product name

            // If product has a photo, show it
            if (productPhoto) {
                modal.find('#productPhotoPreview' + productId).show().attr('src',
                    '{{ asset('uploads/photos') }}/' + productPhoto);
            } else {
                modal.find('#productPhotoPreview' + productId).hide();
            }
        });

        // Preview the photo when selecting a new one
        $('#productPhoto').on('change', function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#productPhotoPreview').show().attr('src', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    </script>
@endsection
