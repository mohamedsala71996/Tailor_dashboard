@extends('layouts.admin')

@section('title', trans('admin.orders'))

@section('content')

    <div class="container mt-5">
        <!-- الطلبات المجموعة -->
        @foreach ($groupedOrders as $productId => $sizes)
            @php
                $product = App\Models\Product::where('id', $productId)->first();

                $last_batch = App\Models\Batch::where('excel_sheet_id', $excel_sheet->id)
                    ->where('product_id', $productId)
                    ->with('orders') // Load the related orders
                    ->latest()
                    ->first();

                $allCompleted = false; // Initialize to false

                if ($last_batch && $last_batch->orders) {
                    $allCompleted = $last_batch->orders->every(function ($order) {
                        return $order->remaining_quantity_admin == 0 && $order->remaining_quantity_admin !== null;
                    });
                }
            @endphp

            <!-- غلاف البطاقة المتجاوبة -->
            <div class="card mb-5" id="product_{{ $productId }}">
                {{-- <div class="card-header bg-primary text-white text-center">
                    {{ $product->name }}
                </div> --}}
                <div class="card-header bg-{{ $allCompleted ? 'success' : 'primary' }} text-white text-center">
                    {{ $product->name }}
                    @if ($allCompleted)
                        <span>
                            (مكتملة)
                            <i class="fas fa-check-circle" ></i> <!-- Font Awesome checkmark icon -->

                        </span>
                    @endif
                </div>


                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- محتوى الجدول -->
                        <div class="w-100">
                            <!-- الجدول الأول (يظهر دائمًا) -->
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
                                            @if (auth('user')->user()->has_permission('تعديل الكمية المطلوبة-الدفعات'))
                                                <form action="{{ route('dashboard.orders.update-requested-quantities') }}"
                                                    method="POST">
                                                    @csrf
                                            @endif
                                            @foreach ($sizes as $sizeId => $sizeData)
                                                <td>
                                                    <input type="hidden" name="product_id" value="{{ $productId }}">
                                                    <input type="hidden" name="size[]" value="{{ $sizeId }}">
                                                    <input type="text" name="quantity_requested[{{ $sizeId }}]"
                                                        class="form-control"
                                                        value="{{ old('quantity_requested.' . $sizeId, $sizeData['quantity_requested'] ?? 0) }}">
                                                </td>
                                            @endforeach
                                            @if (!$allCompleted)
                                            @if (auth('user')->user()->has_permission('تعديل الكمية المطلوبة-الدفعات'))
                                                <td>
                                                    <button type="submit" class="btn btn-success btn-sm">حفظ</button>
                                                </td>
                                            @endif
                                            @endif
                                            </form>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div>
                            <!-- عرض صورة المنتج -->
                            @if ($product->photo)
                                <img src="{{ asset('uploads/photos/' . $product->photo) }}" alt="{{ $product->name }}"
                                    class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <span>لا توجد صورة</span>
                            @endif
                        </div>
                    </div>

                    <!-- زر عرض المزيد مع تصميم جديد -->
                    <button class="btn btn-outline-primary mt-3 toggle-batches" data-toggle="collapse"
                        data-target="#collapseTable_{{ $productId }}" aria-expanded="false"
                        aria-controls="collapseTable_{{ $productId }}">
                        عرض المزيد <i class="fas fa-chevron-down"></i>
                    </button>

                    @php
                        $batches = App\Models\Batch::where('excel_sheet_id', $excel_sheet->id)->where('user_id', $excel_sheet->user_id)
                            ->where('product_id', $productId)
                            ->get();
                    @endphp

                    <!-- الجدول الثاني (مخفي افتراضيًا) -->
                    <div id="collapseTable_{{ $productId }}"
                        class="collapse mt-4 @if (session('show_more') == $productId) show @endif">
                        @foreach ($batches as $batch)
                            <div class="text-center mb-1">
                                <div class="card-header bg-info text-white">
                                    تاريخ الدفعة: ({{ \Carbon\Carbon::parse($batch['created_at'])->format('j/n/Y') }})
                                    رقم التتبع: ({{ $batch['track_number'] }})
                                </div>
                            </div>

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
                                        <!-- صف الخياط -->
                                        <tr>
                                            <form action="{{ route('dashboard.orders.order-quantities') }}" method="POST">
                                                @csrf
                                                <td class="align-middle">الخياط</td>
                                                @foreach ($sizes as $sizeId => $sizeData)
                                                    <td>
                                                        <input type="number"
                                                            name="quantity_delivered_tailor[{{ $batch->orders->where('size_id', $sizeId)->first()->id }}]"
                                                            class="form-control"
                                                            value="{{ old('quantity_delivered_tailor.' . $sizeData['master_order_id'], $batch->orders->where('size_id', $sizeId)->first()->quantity_delivered_tailor ?? '') }}">
                                                    </td>
                                                @endforeach
                                                @if (!$allCompleted)
                                                <td>
                                                    @if (auth('user')->user()->has_permission('تعديل الخياط-الدفعات'))
                                                        <button type="submit" class="btn btn-success btn-sm">حفظ</button>
                                                    @endif
                                                </td>
                                                @endif
                                            </form>
                                        </tr>

                                        <!-- صف المشرف -->
                                        <tr>
                                            <form action="{{ route('dashboard.orders.order-quantities') }}" method="POST">
                                                @csrf
                                                <td class="align-middle">المشرف</td>
                                                @foreach ($sizes as $sizeId => $sizeData)
                                                    <td>
                                                        <input type="number"
                                                            name="quantity_delivered_supervisor[{{ $batch->orders->where('size_id', $sizeId)->first()->id }}]"
                                                            class="form-control"
                                                            value="{{ old('quantity_delivered_supervisor.' . $sizeData['master_order_id'], $batch->orders->where('size_id', $sizeId)->first()->quantity_delivered_supervisor ?? '') }}">
                                                    </td>
                                                @endforeach
                                                @if (!$allCompleted)
                                                <td>
                                                    @if (auth('user')->user()->has_permission('تعديل المشرف-الدفعات'))
                                                        <button type="submit" class="btn btn-success btn-sm">حفظ</button>
                                                    @endif
                                                </td>
                                                @endif
                                            </form>
                                        </tr>

                                        <!-- صف الكمية المتبقية -->
                                        <tr>
                                            <form action="{{ route('dashboard.orders.order-quantities') }}" method="POST">
                                                @csrf
                                                <td class="align-middle">الكمية المتبقية</td>
                                                @foreach ($sizes as $sizeId => $sizeData)
                                                    <td>
                                                        <input type="number"
                                                            name="remaining_quantity_admin[{{ $batch->orders->where('size_id', $sizeId)->first()->id }}]"
                                                            class="form-control"
                                                            value="{{ old('remaining_quantity_admin.' . $sizeData['master_order_id'], $batch->orders->where('size_id', $sizeId)->first()->remaining_quantity_admin ?? '') }}">
                                                    </td>
                                                @endforeach
                                                @if (!$allCompleted)
                                                <td>
                                                    @if (auth('user')->user()->has_permission('الكمية المتبقية-الدفعات'))
                                                        <button type="submit" class="btn btn-success btn-sm">حفظ</button>
                                                    @endif
                                                </td>
                                                @endif
                                            </form>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach

                        <!-- زر لعرض الفورم الخاص بإنشاء جدول صادر (داخل الـ collapse) -->
                        @if (auth('user')->user()->has_permission('انشاء جدول صادر-الدفعات'))
                            <div class="text-center mt-4">
                                <form action="{{ route('dashboard.orders.create.batch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $productId }}">
                                    <input type="hidden" name="excel_sheet_id" value="{{ $excel_sheet->id }}">
                                    <input type="hidden" name="tailor_id" value="{{ $excel_sheet->user_id }}">
                                    @if (!$allCompleted)
                                    <button class="btn btn-outline-success" type="submit">
                                        إنشاء جدول صادر <i class="fas fa-plus"></i>
                                    </button>
                                    @endif
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // حفظ وضع السحب في الذاكرة المحلية
        document.addEventListener('DOMContentLoaded', function() {
            var storedPosition = localStorage.getItem('scrollPosition');
            if (storedPosition) {
                var targetElement = document.getElementById(storedPosition);
                if (targetElement) {
                    targetElement.scrollIntoView();
                }
                localStorage.removeItem('scrollPosition');
            }

            // حفظ وضع السحب بعد إرسال الفورم
            var forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    var parentDivId = form.closest('div.card').id;
                    localStorage.setItem('scrollPosition', parentDivId);
                });
            });
        });
    </script>
@endsection
