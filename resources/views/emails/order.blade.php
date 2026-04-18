<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    {{-- Đổi màu sắc tùy theo trạng thái --}}
    <h2 style="color: {{ $order->status == 'cancelled' ? '#dc3545' : '#0d6efd' }};">
        {{ $title }}
    </h2>

    <p>Chào <strong>{{ Auth::user()->fullname }}</strong>,</p>
    <p>{{ $content }} <strong>#{{ $order->order_id }}</strong> {{ $content_suffix }}</p>
    
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Sản phẩm</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">SL</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Giá</th>
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
                <td colspan="2" style="padding: 10px; border: 1px solid #ddd; text-align: right;"><strong>Tổng cộng:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right; color: {{ $order->status == 'cancelled' ? '#dc3545' : '#0d6efd' }};">
                    <strong>{{ number_format($order->total_amount) }}đ</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <p>Thời gian: <em>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</em></p>
    <p>Địa chỉ: <em>{{ $order->shipping_address }}</em></p>
    
    <div style="margin-top: 30px;">
        <a href="{{ url('/') }}" style="background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Quay lại cửa hàng</a>
    </div>

    <hr style="margin-top: 30px; border: 0; border-top: 1px solid #eee;">
    <p style="font-size: 12px; color: #888;">Đây là email tự động từ hệ thống Book Store, vui lòng không trả lời email này.</p>
</body>
</html>