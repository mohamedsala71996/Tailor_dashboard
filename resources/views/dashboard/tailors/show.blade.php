@extends('layouts.admin')

@section('title', trans('admin.tailor_batch_details'))

@section('content')

<div class="container mt-5">

    <!-- Batch Details -->
    {{-- <div class="text-center mb-5">
        <h3>دفعة بتاريخ: {{ $batch->created_at->format('j/n/Y') }}</h3>
        <p>رقم التتبع: {{ $batch->track_number }}</p>
    </div> --}}
    <div class="text-center mb-5">
        {{-- <form action="{{ route('dashboard.orders.end-batch', $last_batch->id) }}" method="POST"> --}}
            {{-- @csrf --}}
            <!-- Button to end the current batch -->
            <button class="btn btn-info btn-lg" >
                 الدفعة بتاريخ: ( {{ $batch->created_at->format('j/n/Y') }} )
                 
                رقم التتبع: ( {{ $batch->track_number }} )
            </button>
        {{-- </form> --}}
    </div>


    <!-- Grouped Orders -->
    @foreach($groupedOrders as $productId => $sizes)
    @php
    $product = App\Models\Product::find($productId);
    @endphp

    <div class="card mb-5">
        <div class="card-header bg-primary text-white text-center">
            {{ $product->name }}
        </div>

        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="w-100">
                    <!-- Table to show quantities requested per size -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-dark">
                                <tr class="text-center">
                                    <th>48</th>
                                    <th>50</th>
                                    <th>52</th>
                                    <th>54</th>
                                    <th>56</th>
                                    <th>58</th>
                                    <th>60</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>{{ $sizes[1]['quantity_requested'] ?? 0 }}</td>
                                    <td>{{ $sizes[2]['quantity_requested'] ?? 0 }}</td>
                                    <td>{{ $sizes[3]['quantity_requested'] ?? 0 }}</td>
                                    <td>{{ $sizes[4]['quantity_requested'] ?? 0 }}</td>
                                    <td>{{ $sizes[5]['quantity_requested'] ?? 0 }}</td>
                                    <td>{{ $sizes[6]['quantity_requested'] ?? 0 }}</td>
                                    <td>{{ $sizes[7]['quantity_requested'] ?? 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Product Image -->
                <div>
                    @if ($product->photo)
                        <img src="{{ asset('uploads/photos/' . $product->photo) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <span>No Image</span>
                    @endif
                </div>
            </div>

            <!-- Forms for different stakeholders -->
            <div id="collapseTable_{{ $productId }}" class="collapse mt-4" style="display: inline">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th></th>
                                <th>48</th>
                                <th>50</th>
                                <th>52</th>
                                <th>54</th>
                                <th>56</th>
                                <th>58</th>
                                <th>60</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Tailor -->
                            <tr>
                                <td class="align-middle">الخياط</td>
                                @foreach([1, 2, 3, 4, 5, 6, 7] as $size)
                                    <td>
                                        <input type="text" name="quantity_delivered_tailor[{{$sizes[$size]['order_id'] ?? ''}}]" class="form-control" value="{{ old('quantity_delivered_tailor.' . ($sizes[$size]['order_id'] ?? ''), $sizes[$size]['quantity_delivered_tailor'] ?? '') }}" disabled>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Supervisor -->
                            <tr>
                                <td class="align-middle">المطابقة</td>
                                @foreach([1, 2, 3, 4, 5, 6, 7] as $size)
                                    <td>
                                        <input type="text" name="quantity_delivered_supervisor[{{$sizes[$size]['order_id'] ?? ''}}]" class="form-control" value="{{ old('quantity_delivered_supervisor.' . ($sizes[$size]['order_id'] ?? ''), $sizes[$size]['quantity_delivered_supervisor'] ?? '') }}" disabled>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Remaining quantity -->
                            <tr>
                                <td class="align-middle">المتبقي</td>
                                @foreach([1, 2, 3, 4, 5, 6, 7] as $size)
                                    <td>
                                        <input type="text" name="remaining_quantity_admin[{{$sizes[$size]['order_id'] ?? ''}}]" class="form-control" value="{{ old('remaining_quantity_admin.' . ($sizes[$size]['order_id'] ?? ''), $sizes[$size]['remaining_quantity_admin'] ?? '') }}" disabled>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>

@endsection
