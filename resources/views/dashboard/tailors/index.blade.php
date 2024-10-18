@extends('layouts.admin')

@section('title', 'الطلبيات')

@section('content')
<style>
    /* Styles for buttons */
    .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #218838;
    }

    /* Styles for the form and input fields */
    .form-control {
        border-radius: 50px;
        padding: 10px 20px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    /* Styles for file upload */
    .custom-file-input {
        cursor: pointer;
    }

    .input-group-text {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 0 50px 50px 0;
    }

    .input-group-text:hover {
        background-color: #0056b3;
    }

    /* Card styling */
    .card {
        border-radius: 15px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-body {
        padding: 20px;
    }

    /* Table styling */
    .table th, .table td {
        vertical-align: middle;
    }

    /* Center the form elements */
    .form-group {
        margin-bottom: 15px;
    }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <form method="GET" action="{{ route('dashboard.orders.index', $tailor->id) }}" class="mb-3 w-100">
                <div class="row justify-content-center">
                             <!-- Batch Name Filter -->
                             <div class="col-md-3 mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-tags"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="name" class="form-control" placeholder="اسم الدفعة" value="{{ request('name') }}">
                                </div>
                            </div>
                    <!-- Track Number Filter -->
                    <div class="col-md-3 mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode"></i>
                                </span>
                            </div>
                            <input type="text" name="track_number" class="form-control" placeholder="رقم التتبع" value="{{ request('track_number') }}">
                        </div>
                    </div>
            
                    <!-- Created At Filter (Date) -->
                    <div class="col-md-3 mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                            <input type="date" name="created_at" class="form-control" placeholder="أنشأت في" value="{{ request('created_at') }}">
                        </div>
                    </div>
            
           
            
                    <!-- Submit Button -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
            </form>
            

            <div class="col-md-12">
                @if (auth('user')->user()->has_permission('اضافة-شيت الاكسل'))
                    <div class="card card-primary">
                        <form method="post" action="{{ route('dashboard.orders.import.orders', $tailor->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                {{-- <h5 class="card-title">استيراد الطلبات</h5> --}}
                                <div class="form-group">
                                    <label for="exampleInputFile">رفع الملف</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="excel" class="custom-file-input" id="exampleInputFile" required>
                                            <label class="custom-file-label" for="exampleInputFile">اختر ملف</label>
                                        </div>
                                    </div>
                                    @error('excel')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="batchName">اسم الدفعة</label>
                                    <input type="text" name="name" class="form-control" id="batchName" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> إنشاء دفعة
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-bordered table-striped data-table text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الدفعة</th>
                        <th>رقم التتبع</th>
                        <th>أنشأت في</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($excel_sheets as $excel_sheet)
                        <tr>
                            <td>{{ $excel_sheet->id }}</td>
                            <td>{{ $excel_sheet->name }}</td>
                            <td>{{ $excel_sheet->track_number }}</td>
                            <td>{{ $excel_sheet->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('dashboard.orders.master-order', $excel_sheet->id) }}" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> عرض تفاصيل الدفعة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">لا توجد دفعات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection

@section('script')
@endsection
