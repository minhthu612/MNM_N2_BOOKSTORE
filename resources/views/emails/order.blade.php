<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject ?? $title }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px;">
    {{-- Tiêu đề đổi màu: Đỏ cho hủy, Xanh cho đặt --}}
    <h2 style="color: {{ $order->status == 'cancelled' ? '#dc3545' : '#0d6efd' }}; margin-bottom: 20px;">
        {{ $title }}
    </h2>

    <p>Chào <strong>{{ Auth::user()->fullname }}</strong>,</p>
    <p>{{ $content }} <strong>#{{ $order->order_id }}</strong> {{ $content_suffix }}</p>
    
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead style="background: #f8f9fa;">
            <tr>
                {{-- Đổi tên cột tùy theo trạng thái --}}
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">
                    {{ $order->status == 'cancelled' ? 'Sản phẩm đã hủy' : 'Sản phẩm' }}
                </th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: center; width: 50px;">SL</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: right; width: 100px;">Giá</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $detail)
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $detail->book->title }}</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $detail->quantity }}</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{ number_format($detail->price) }}đ</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="padding: 10px; border: 1px solid #ddd; text-align: right;">
                    <strong>{{ $order->status == 'cancelled' ? 'Số tiền hoàn trả/hủy:' : 'Tổng cộng:' }}</strong>
                </td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right; color: {{ $order->status == 'cancelled' ? '#dc3545' : '#0d6efd' }};">
                    <strong>{{ number_format($order->total_amount) }}đ</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- HIỂN THỊ THÔNG TIN KHÁC NHAU --}}
    @if($order->status == 'cancelled')
        <p style="margin: 5px 0;">Thời gian đặt hàng: <em>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</em></p>
        <p style="margin: 15px 0;">Nếu bạn không thực hiện thao tác này hoặc có thắc mắc, vui lòng liên hệ hotline của chúng tôi ngay lập tức.</p>
    @else
        <p style="margin: 5px 0;">Địa chỉ giao hàng: <em>{{ $order->shipping_address }}</em></p>
        <p style="margin: 15px 0;">Chúng tôi sẽ sớm liên hệ để giao hàng cho bạn.</p>
    @endif
    
    <div style="margin-top: 30px;">
        <a href="{{ url('/') }}" style="background-color: #0d6efd; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            Quay lại cửa hàng
        </a>
    </div>

    <hr style="margin-top: 30px; border: 0; border-top: 1px solid #eee;">
    <p style="font-size: 12px; color: #888;">Đây là email tự động, vui lòng không trả lời email này.</p>
</body>
</html>