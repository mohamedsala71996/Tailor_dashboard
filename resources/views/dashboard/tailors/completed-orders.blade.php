@extends('layouts.admin')

@section('title', trans('admin.completed_orders'))

@section('content')

<div class="container mt-5">
    <h2 class="mb-4">الطلبيات المكتملة</h2>

    <!-- Check if there are any completed master orders -->
    @if($completedMasters->isEmpty())
        <div class="alert alert-info">
            لا توجد طلبيات مكتملة في الوقت الحالي.
        </div>
    @else
        <!-- Loop through each completed master order -->
        @foreach($completedMasters as $master)
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <div>
                    <strong>دفعة رقم: </strong>
                    <span>{{ $master->created_at->format('j/n/Y') }}</span>
                </div>
                <div>
                    <span class="badge badge-light">عدد الطلبات: {{ $master->orders->count() }}</span>
                </div>
            </div>

            <div class="card-body">
                <!-- Loop through the orders in each master order -->
                @foreach($master->orders as $order)
                @php
                $product = $order->product;
                $size = $order->size;
                @endphp
                <div class="mb-3">
                    <h5>{{ $product->name }} (المقاس: {{ $size->name }})</h5>

                    <!-- Check if the product has a photo and display it -->
                    @if ($product->photo)
                        <img src="{{ asset('uploads/photos/' . $product->photo) }}" alt="{{ $product->name }}" width="100" class="mb-2" />
                    @else
                        <span>- لا توجد صورة للمنتج -</span>
                    @endif

                    <p>الكمية المطلوبة: {{ $order->quantity_requested }}</p>
                    <p>الكمية التي وضعها الخياط: {{ $order->quantity_delivered_tailor }}</p>
                    <p>الكمية التي وضعها المراقب: {{ $order->quantity_delivered_supervisor }}</p>
                    <p>الكمية المتبقية: {{ $order->remaining_quantity_admin }}</p>
                </div>
                <hr>
                @endforeach
            </div>
        </div>
        @endforeach
    @endif
</div>

@endsection
