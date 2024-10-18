@extends('layouts.admin')

@section('title', trans('admin.products'))

@section('content')

<style>
    .photo-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100px; /* Fixed width */
    height: 100px; /* Fixed height */
}

.fixed-size {
    width: 100%; /* Fill container */
    height: 100%; /* Fill container */
    object-fit: cover; /* Maintain aspect ratio and crop if necessary */
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.fixed-size:hover {
    transform: scale(1.05); /* Slight zoom effect on hover */
}

</style>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">{{ trans('admin.product') }}</h1> --}}
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.products') }}</li> --}}
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="card collapsed-card">
                <div class="card-header">


                    <h3 class="card-title">{{ trans('admin.filter') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.products.index') }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="searchName">البحث باسم المنتج</label>
                                    <input type="text" name="name" id="searchName" class="form-control" value="{{ request('name') }}" placeholder="اسم المنتج">
                                    {{-- <button type="submit" class="btn btn-primary">بحث</button> --}}

                                </div>
                            </div>
                            <div class="col-lg-6">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-warning">
                {{-- <div class="card-header">
                    <h3 class="card-title">احصائيات</h3>
                </div> --}}
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>عدد المنتجات</label>
                                    <input id="countofproduct" type="text" class="form-control" value="{{ $products->count() }}" placeholder="" disabled>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Product Table -->
            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                    @if (auth('user')->user()->has_permission('اضافة-المنتجات'))
                <a href="{{ route('dashboard.products.create') }}" type="button" class="btn btn-info btn-sm">اضافة صور المنتجات</a>
                @endif
            </div>
<br>
                <table id="table" class="table table-bordered table-striped data-table responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            {{-- <th><input type="checkbox" id="selectAllProduct" /></th> --}}
                            <th>{{ trans('admin.Name') }}</th>
                            <th>صورة المنتج</th>
                            <th>{{ trans('admin.Created at') }}</th>
                            <th>{{ trans('admin.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                {{-- <td><input type="checkbox" class="selectProduct" value="{{ $product->id }}" /></td> --}}
                                <td>{{ $product->name }}</td>
                                <td>
                                    @if ($product->photo)
                                        <div class="photo-container">
                                            <img src="{{ asset('uploads/photos/' . $product->photo) }}" alt="{{ $product->name }}" class="fixed-size">
                                        </div>
                                    @else
                                        <span>{{ trans('admin.No Photo') }}</span>
                                    @endif
                                </td>
                                <td>{{ $product->created_at }}</td>
                                <td>
                                    @if (auth('user')->user()->has_permission('تعديل-المنتجات'))
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editProductModal{{ $product->id }}" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-photo="{{ $product->photo }}">
                                        {{ trans('admin.Edit') }}
                                    </button>
                                            <!-- Edit Product Modal -->
                                            <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1"
                                                aria-labelledby="editProductModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editProductModalLabel">
                                                                تعديل</h5>
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
                                                                    <label for="productPhoto">صورة المنتج</label>
                                                                    <input type="file" class="form-control"
                                                                        id="productPhoto{{ $product->id }}" name="photo">
                                                                    <img id="productPhotoPreview{{ $product->id }}" src="#"
                                                                        alt="Photo Preview" width="100" style="display:none;">
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">اغلاق</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">حفظ</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    @endif
                                    @if (auth('user')->user()->has_permission('حذف-المنتجات'))
                                    <form action="{{ route('dashboard.products.destroy', $product->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ trans('admin.Are you sure?') }}')">{{ trans('admin.Delete') }}</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection

@section('script')

    {{-- <script>
        var table = $("#table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('dashboard.products.index') }}",
                "data": function(d) {
                    d.size = $('#sizes').val();
                    d.name = $('#searchName').val();
                }
            },
            columns: [

                {
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'checkboxDelete',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'size',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    orderable: false,
                    searchable: false

                },

                {
                    data: 'actions',
                    orderable: false,
                    searchable: false

                },
            ]


        });

        $('#sizes').change(function() {


            $('#countofproduct').val(table.settings()[0].json.recordsFiltered);

            table.ajax.reload();



        });

        $('#searchName').change(function() {




            table.ajax.reload();

            console.log($('#searchName').val());



        });
        // $('#searchProduct').click(function() {


        //     $('#countofproduct').val(table.settings()[0].json.recordsFiltered);

        //     table.ajax.reload();

        //     console.log($('#nameProduct').val())



        // });


        table.on('draw.dt', function() {
            $('#countofproduct').val(table.settings()[0].json.recordsFiltered);
        });





    </script>

    <script>
        $(document).ready(function() {

            $('#selectAllProduct').click(function() {
                inputs = $(".checkboxDelete");
                inputs.prop('checked', $(this).prop('checked'));

            })

            $('#deletebyid').click(function(e) {

                e.preventDefault();

                inputs = $(".checkboxDelete:checked:enabled");

                var ids = [];

                inputs.each(function() {
                    ids.push($(this).val());
                });

                if (ids.length > 0) {
                    $.ajax({

                        url: "{{ route('dashboard.products.destroy.by.id') }}",
                        type: 'POST',
                        data: {
                            'ids': ids,
                            '_token': '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(data) {
                            //    $.each(ids, function (index,value) {

                            //         $('#row-' + value).remove();

                            //    })


                            location.reload();

                        },
                        error: function(request, error) {
                            console.log('Data: ' + request);
                        }
                    });
                }


            })

        });
    </script> --}}




<script>
    $('#searchName').change(function() {
    // Get the selected product name
    var searchName = $('#searchName').val();

    // Reload the table with the filtered products
    $.ajax({
        url: '{{ route("dashboard.products.index") }}',
        type: 'GET',
        data: {
            name: searchName,
        },
        success: function(data) {
            // Replace the table body with the filtered products
            $('tbody').html($(data).find('tbody').html());
            $('#countofproduct').val($(data).find('#countofproduct').val());
        }
    });
});
</script>

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
