<?php

namespace Sudo\Order\Http\Controllers\App;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Cart;

use \Sudo\Order\Models\Order;
use \Sudo\Order\Models\OrderDetail;
use \Sudo\Order\Models\OrderHistory;
use \Sudo\Order\Models\Customer;

class OrderController extends Controller
{
    public function __construct() {
        // Sử dụng middleware để check phân quyền và set ngôn ngữ
        $this->middleware(function ($request, $next) {
            // Đặt lại ngôn ngữ nếu trên url có request setLanguage
            setLanguage($request->setLanguage);

            return $next($request);
        });
    }

    public function store(Request $requests) {
        // Nếu không có hàng trong giỏ thì trả về trang chủ tránh trường hợp tạo đơn hàng rác khi bấm F5
        if (Cart::count() == 0) {
            return redirect('/');
        }
        // Xử lý validate
        validateForm($requests, 'customer_name', 'Tên người đặt không được để trống.');
        validateForm($requests, 'customer_phone', 'Điện thoại người đặt không được để trống.');
        validateForm($requests, 'customer_email', 'Email không được để trống.');
        validateForm($requests, 'payment_method', 'Hình thức thanh toán không được để trống.');
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Khách hàng
        $customers = [
            'name'      => $customer_name,
            'phone'     => $customer_phone,
            'email'     => $customer_email,
            'address'   => $customer_address,
        ];
        $customer_id = Customer::add($customers);
        // Đơn hàng
        $orders = [
            'customer_id'       => $customer_id,
            'payment_method'    => $payment_method,
            'note'              => $note,
            'total_price'       => Cart::priceTotal(0, '', ''),
        ];
        $order_id = Order::add($orders);
        OrderHistory::add($order_id, 'customer_create');
        // Chi tiết đơn hàng
        foreach (Cart::content() as $value) {
            $order_detail = [
                'order_id'      => $order_id,
                'product_id'    => $value->id ?? 0,
                'price'         => $value->price ?? 0,
                'quantity'      => $value->qty ?? 0,
            ];
            OrderDetail::add($order_detail);
        }
        // Xoá giỏ hàng
        Cart::destroy();
        return view('Order::shopping_carts.success', compact('order_id'));
    }

}
