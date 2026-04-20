@extends('layouts.app')
@section('title', 'Xem đơn hàng #' . $order->order_id)
@section('content')

<style>
    /* GIAO DIỆN TRÊN WEB */
    .khung-don-hang { background: #fff; padding: 25px; border-radius: 15px; border: 1px solid #eee; }
    .o-thong-tin { background: #f8f9fa; border-radius: 10px; padding: 15px; height: 100%; }
    .anh-sach { width: 50px; height: 70px; object-fit: cover; border-radius: 5px; }
    .nut-tron { border-radius: 20px !important; padding: 8px 20px !important; font-weight: bold; }

    /* GIAO DIỆN IN - ĐẶC TRỊ LỖI BỊ BÓP NHỎ */
    @media print {
        /* 1. Ép khổ giấy A4 và xóa lề thừa trình duyệt */
        @page { 
            size: A4; 
            margin: 10mm 15mm; 
        }

        /* 2. Ẩn toàn bộ thành phần thừa để không bị chiếm không gian */
        .d-print-none, .sidebar, .navbar, .btn, footer, header { 
            display: none !important; 
        }

        /* 3. Ép nội dung chính bung 100% chiều ngang */
        body, html {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background: #fff !important;
        }

        .container-fluid, .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            display: block !important;
        }

        .khung-don-hang {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            width: 100% !important;
            position: absolute; /* Ép nó về sát lề trái */
            left: 0;
            top: 0;
        }

        /* 4. Fix row và col để không bị nhảy hàng hay bóp nhỏ */
        .row {
            display: flex !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .col-md-6 {
            width: 50% !important;
            flex: 0 0 50% !important;
            max-width: 50% !important;
            padding: 5px !important;
        }

        .o-thong-tin {
            border: 1px solid #eee !important;
            background: #fff !important;
            border-radius: 5px !important;
        }

        /* 5. Làm bảng to rõ ràng */
        .table {
            width: 100% !important;
            margin-top: 20px !important;
            border: 1px solid #000 !important;
        }

        .table th {
            background-color: #f2f2f2 !important;
            color: #000 !important;
            border: 1px solid #000 !important;
            -webkit-print-color-adjust: exact;
        }

        .table td {
            border: 1px solid #000 !important;
            padding: 10px !important;
        }

        .text-primary, .text-danger {
            color: #000 !important;
            font-weight: bold !important;
        }

        /* Header hóa đơn to rõ hơn */
        h4 { font-size: 24pt !important; margin-bottom: 10px !important; }
    }
</style>

<div class="container-fluid">
    <div class="khung-don-hang shadow-sm">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-bold text-primary mb-0">
                    CHI TIẾT ĐƠN HÀNG #{{ $order->order_id }}
                </h4>
                <small class="text-muted">
                    Ngày đặt: {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                </small>
            </div>

            <div class="d-print-none">
                @if($order->status == 'pending')
                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2">Đang chờ xử lý</span>
                @elseif($order->status == 'processing')
                    <span class="badge rounded-pill bg-info px-3 py-2">Đang đóng gói</span>
                @elseif($order->status == 'shipped')
                    <span class="badge rounded-pill bg-primary px-3 py-2">Đang giao hàng</span>
                @elseif($order->status == 'delivered')
                    <span class="badge rounded-pill bg-success px-3 py-2">Đã giao thành công</span>
                @else
                    <span class="badge rounded-pill bg-danger px-3 py-2">Đã hủy đơn</span>
                @endif
            </div>
            
            {{-- Hiển thị trạng thái bằng chữ thường khi in để trang trọng --}}
            <div class="d-none d-print-block">
                <strong>Trạng thái:</strong> 
                {{ $order->status == 'pending' ? 'Đang chờ xử lý' : ($order->status == 'delivered' ? 'Đã giao thành công' : 'Đang xử lý') }}
            </div>
        </div>

        {{-- INFO --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-6">
                <div class="o-thong-tin">
                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-user me-2"></i>THÔNG TIN NGƯỜI NHẬN
                    </h6>
                    <p class="mb-1">Họ tên: <strong>{{ $order->fullname }}</strong></p>
                    <p class="mb-1">SĐT: <strong>{{ $order->phone }}</strong></p>
                    <p class="mb-1">Email: {{ $order->email }}</p>
                </div>
            </div>

            <div class="col-md-6 col-6">
                <div class="o-thong-tin">
                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>ĐỊA CHỈ GIAO HÀNG
                    </h6>
                    <p class="mb-0">{!! nl2br($order->shipping_address) !!}</p>
                    <div class="mt-2 small text-muted">
                        Phương thức: <b>{{ $order->payment_method }}</b>
                    </div>
                </div>
            </div>
        </div>

        {{-- ITEMS --}}
        <h6 class="fw-bold mb-3">
            <i class="fas fa-list me-2"></i>DANH SÁCH SÁCH ĐÃ ĐẶT
        </h6>

        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="80">Ảnh</th>
                        <th class="text-start">Tên sách</th>
                        <th width="80">SL</th>
                        <th width="120">Đơn giá</th>
                        <th width="120">Thành tiền</th>
                    </tr>
                </thead>

                <tbody>
                    @php $tong_sl = 0; @endphp
                    @foreach($items as $item)
                        @php
                            $thanh_tien = $item->quantity * $item->price;
                            $tong_sl += $item->quantity;

                            // LOGIC TÌM ẢNH THÔNG MINH TRONG STORAGE
                            $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                            $anh_item = 'https://via.placeholder.com/50x70?text=No+Img';
                            
                            // Xác định ID và quy tắc đặt tên (Nếu có set_id thì dùng ID_ID, ngược lại dùng ID)
                            $isSet = !empty($item->set_id);
                            $id = $isSet ? $item->set_id : $item->book_id;
                            $fileName = $isSet ? ($id . '_' . $id) : $id;

                            foreach ($extensions as $ext) {
                                if (file_exists(storage_path("app/public/image/{$fileName}.{$ext}"))) {
                                    $anh_item = asset("storage/image/{$fileName}.{$ext}");
                                    break;
                                }
                            }

                            // Nếu không có trong storage mới dùng link_images
                            if ($anh_item == 'https://via.placeholder.com/50x70?text=No+Img' && !empty($item->link_images)) {
                                $anh_item = $item->link_images;
                            }
                        @endphp
                        <tr>
                            <td class="text-center">
                                <img src="{{ $anh_item }}" class="anh-sach shadow-sm" onerror="this.src='https://via.placeholder.com/50x70?text=No+Img'">
                            </td>
                            <td>
                                <div class="fw-bold">{{ $item->title }}</div>
                                <small class="text-muted">Mã: #{{ $id }} {{ $isSet ? '(Bộ sách)' : '' }}</small>
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->price) }}đ</td>
                            <td class="text-end fw-bold text-primary">
                                {{ number_format($thanh_tien) }}đ
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">TỔNG CỘNG:</td>
                        <td class="text-end fw-bold text-danger fs-5">
                            {{ number_format($order->total_amount) }}đ
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Chữ ký khi in --}}
        <div class="d-none d-print-block mt-5">
            <div class="row text-center">
                <div class="col-6">
                    <p class="mb-5"><strong>Người mua hàng</strong></p>
                    <p class="mt-5 text-muted">(Ký và ghi rõ họ tên)</p>
                </div>
                <div class="col-6">
                    <p class="mb-5"><strong>Người lập hóa đơn</strong></p>
                    <p class="mt-5 text-muted">(Ký và ghi rõ họ tên)</p>
                </div>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="pt-3 border-top d-flex justify-content-between d-print-none">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.orders.index') }}?{{ http_build_query(request()->all()) }}" class="btn btn-outline-secondary nut-tron">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
                <button onclick="window.print()" class="btn btn-dark nut-tron">
                    <i class="fas fa-print me-2"></i>In hóa đơn
                </button>
            </div>

            <div class="d-flex gap-2">
                @if($order->status == 'pending')
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action=ship" class="btn btn-info text-white nut-tron">Xác nhận đơn</a>
                @endif

                @if($order->status == 'processing')
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action=ship" class="btn btn-primary nut-tron">Giao hàng</a>
                @endif

                @if($order->status == 'shipped')
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action=deliver" class="btn btn-success nut-tron">Hoàn tất</a>
                @endif

                @if(!in_array($order->status, ['delivered','cancelled']))
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action=cancel" class="btn btn-danger nut-tron" onclick="return confirm('Bạn có chắc muốn hủy đơn này?')">Hủy đơn</a>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection