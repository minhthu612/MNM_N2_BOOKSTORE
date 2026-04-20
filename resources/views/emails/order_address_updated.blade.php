<h2>Chào {{ $order->fullname }},</h2>
<p>Bạn vừa cập nhật thông tin nhận hàng cho đơn hàng <strong>#{{ $order->order_id }}</strong>.</p>

<p><strong>Thông tin mới:</strong></p>
<ul>
    <li>Người nhận: {{ $order->fullname }}</li>
    <li>Số điện thoại: {{ $order->phone }}</li>
    <li>Địa chỉ giao: {{ $order->shipping_address }}</li>
</ul>

<p>Cảm ơn bạn đã mua sắm tại Book Store!</p>