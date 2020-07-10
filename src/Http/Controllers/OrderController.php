<?php

namespace Sudo\Order\Http\Controllers;
use Sudo\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use ListData;
use Form;
use ListCategory;

use \Sudo\Order\Models\Order;
use \Sudo\Order\Models\OrderDetail;
use \Sudo\Order\Models\OrderHistory;
use \Sudo\Order\Models\Customer;

class OrderController extends AdminController
{
    function __construct() {
        $this->models = new Order;
        $this->table_name = $this->models->getTable();
        $this->module_name = 'Đơn hàng';
        $this->has_seo = false;
        $this->has_locale = false;
        parent::__construct();

        $this->order_status = [
            1 => 'Đơn hàng mới',
            2 => 'Đã tiếp nhận',
            3 => 'Huỷ',
            4 => 'Thành công',
        ];

        $this->payment_method = [
            1 => 'Ship COD',
            2 => 'Tiền mặt',
            3 => 'Chuyển khoản ATM',
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests) {
        $listdata = new ListData($requests, $this->models, 'Order::admin.orders.table.index', $this->has_locale);
        $order_status = $this->order_status;
        $payment_method = $this->payment_method;
        // Build Form tìm kiếm
        $listdata->search('order_id', 'Mã đơn hàng', 'string');
        $listdata->search('customer_name', 'Tên người đặt', 'string');
        $listdata->search('customer_phone', 'Điện thoại người đặt', 'string');
        $listdata->search('created_at', 'Ngày tạo', 'range');
        $listdata->search('status', 'Trạng thái', 'array', $order_status);
        // Build bảng
        $listdata->add('id', 'Mã đơn hàng', 1);
        $listdata->add('', 'Tên người đặt', 0);
        $listdata->add('', 'Trạng thái', 0);
        $listdata->add('total_price', 'Giá trị đơn', 0);
        $listdata->add('note', 'Ghi chú đơn', 0);
        $listdata->add('', 'Thời gian', 0, 'time');
        $listdata->add('', 'Xem', 0, 'show');
        // Trả về views
        $data = $listdata->data();
        $show_data = $data['show_data'] ?? [];
        $customer_array_id = $show_data->pluck('customer_id')->toArray();
        $customers = Customer::whereIn('id', $customer_array_id)->get();

        return $listdata->render(compact('data', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $form = new Form;
        $form->title('Thông tin khách hàng');
        $form->text('name', '', 1, 'Tên người đặt');
        $form->text('phone', '', 1, 'Điện thoại');
        $form->text('email', '', 0, 'Email');
        $form->text('address', '', 0, 'Địa chỉ');
        $form->select('payment_method', '', 1, 'Hình thức thanh toán', $this->payment_method, 0);
        $form->textarea('note', '', 0, 'Ghi chú');
        $form->title('Thông tin Sản phẩm');
        $form->custom('Order::admin.orders.form.product_in_orders', [
            'name' => 'products',
            'value' => [],
        ]);
        $form->action('add');
        return $form->render('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $requests) {
        // Xử lý validate
        validateForm($requests, 'name', 'Tên người đặt không được để trống.');
        validateForm($requests, 'phone', 'Điện thoại người đặt không được để trống.');
        validateForm($requests, 'payment_method', 'Hình thức thanh toán không được để trống.');
        validateForm($requests, 'products', 'Sản phẩm không được để trống.');
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Khách hàng
        $customers = [
            'name'      => $name,
            'phone'     => $phone,
            'email'     => $email,
            'address'   => $address,
        ];
        $customer_id = Customer::add($customers);
        $orders = [
            'customer_id'       => $customer_id,
            'payment_method'    => $payment_method,
            'note'              => $note,
            'total_price'       => $total_price,
        ];
        $order_id = Order::add($orders);
        if (isset($products) && !empty($products)) {
            foreach ($products as $value) {
                $order_detail = [
                    'order_id'      => $order_id,
                    'product_id'    => $value['id'] ?? 0,
                    'price'         => $value['price'] ?? 0,
                    'quantity'      => $value['quantity'] ?? 0,
                ];
                OrderDetail::add($order_detail);
            }
        }
        // Điều hướng
        if ($redirect == 'edit') {
            $redirect = 'show';
        }
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $order_id))->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment_method = $this->payment_method;
        // Toàn bộ admin_user để hiển thị cho lịch xử
        $admin_user_query = \DB::table('admin_users')->get();
        $admin_users = [];
        foreach ($admin_user_query as $value) {
            $admin_users[$value->id] = $value->display_name ?? $value->name;
        }
        // Lấy bản ghi
        $order = $this->models->where('id', $id)->first();
        // Khách hàng
        $customers = Customer::where('id', $order->id)->first();
        // Thông tin sản phẩm
        $order_details = OrderDetail::where('order_id', $order->id)->get();
        // Lịch sử hành động của đơn hàng
        $order_histories = OrderHistory::getOrderHistory($order->id);

        return view('Order::admin.orders.show', compact(
            'payment_method',
            'admin_users',
            'order',
            'customers',
            'order_details',
            'order_histories'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // Lấy bản ghi
        $data_edit = $this->models->where('id', $id)->first();
        // Chỉ được sửa khi ở trạng thái đã tiếp nhận
        if ($data_edit->status != 2) {
            return redirect(route('admin.orders.show', $id))->with([
                'type' => 'success',
                'message' => __('Để sửa đơn hàng phải ở trạng thái "Đã tiếp nhận"')
            ]);
        }
        // Khách hàng
        $customers = Customer::where('id', $data_edit->id)->first();
        // Thông tin sản phẩm
        $order_details = OrderDetail::where('order_id', $data_edit->id)->get();
        $product_details = [];
        foreach ($order_details as $value) {
            $product_details[] = [
                'product_id' => $value->product_id,
                'quantity' => $value->quantity,
                'price' => $value->price,
            ];
        }
        // Khởi tạo form
        $form = new Form;
        $form->title('Thông tin khách hàng');
        $form->text('name', $customers->name, 1, 'Tên người đặt');
        $form->text('phone', $customers->phone, 1, 'Điện thoại');
        $form->text('email', $customers->email, 0, 'Email');
        $form->text('address', $customers->address, 0, 'Địa chỉ');
        $form->select('payment_method', $data_edit->payment_method, 1, 'Hình thức thanh toán', $this->payment_method, 0);
        $form->textarea('note', $data_edit->note, 0, 'Ghi chú');
        $form->title('Thông tin Sản phẩm');
        $form->custom('Order::admin.orders.form.product_in_orders', [
            'name' => 'products',
            'value' => $product_details,
        ]);
        $form->action('edit');
        return $form->render('edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $requests
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $requests, $id) {
        // Xử lý validate
        validateForm($requests, 'name', 'Tên người đặt không được để trống.');
        validateForm($requests, 'phone', 'Điện thoại người đặt không được để trống.');
        validateForm($requests, 'payment_method', 'Hình thức thanh toán không được để trống.');
        validateForm($requests, 'products', 'Sản phẩm không được để trống.');
        // Lấy bản ghi
        $data_edit = $this->models->where('id', $id)->first();
        // Chỉ được sửa khi ở trạng thái đã tiếp nhận
        if ($data_edit->status != 2) {
            return redirect(route('admin.orders.show', $id))->with([
                'type' => 'success',
                'message' => __('Để sửa đơn hàng phải ở trạng thái "Đã tiếp nhận"')
            ]);
        }
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Khách hàng
        $customers = [
            'name'      => $name,
            'phone'     => $phone,
            'email'     => $email,
            'address'   => $address,
        ];
        $customer_id = Customer::add($customers);
        // Đơn hàng
        $orders = [
            'customer_id'       => $customer_id,
            'payment_method'    => $payment_method,
            'note'              => $note,
            'total_price'       => $total_price,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        \DB::table('orders')->where('id', $id)->update($orders);
        // Chi tiết đơn hàng
        if (isset($products) && !empty($products)) {
            OrderDetail::where('order_id', $id)->delete();
            foreach ($products as $value) {
                $order_detail = [
                    'order_id'      => $id,
                    'product_id'    => $value['id'] ?? 0,
                    'price'         => $value['price'] ?? 0,
                    'quantity'      => $value['quantity'] ?? 0,
                ];
                OrderDetail::add($order_detail);
            }
        }
        // Điều hướng
        if ($redirect == 'edit') {
            $redirect = 'show';
        }
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Ghi chú dành cho Admin
     */
    public function adminNote(Request $requests) {
        // Không có quyền sửa thì trả về trang chủ
        if (!checkRole($this->table_name.'_edit')) {
            return redirect(route('admin.home'))->with([
                'type' => 'danger',
                'message' => 'Core::admin.role.no_permission',
            ]);
        } else {
            $note = $requests->admin_note;
            $order_id = $requests->order_id;
            // Không có note sẽ không ghi
            if (!empty($note)) {
                OrderHistory::add($order_id, 'admin_note', $note);
            }
            return redirect(route('admin.orders.show', $order_id));
        }
    }

    /**
     * Tiếp nhận đơn hàng
     */
    public function accepts(Request $requests) {
        // Không có quyền sửa thì trả về trang chủ
        if (!checkRole($this->table_name.'_edit')) {
            return redirect(route('admin.home'))->with([
                'type' => 'danger',
                'message' => 'Core::admin.role.no_permission',
            ]);
        }
        // ID đơn hàng
        $order_id = $requests->order_id;
        // Lấy bản ghi
        $order = $this->models->where('id', $order_id)->first();
        // Nếu trạng thái đơn không phải đơn hàng mới thì sẽ không cho tiếp nhận
        if ($order->status == 1) {
            // Đổi trạng thái tiếp nhận
            $this->models->where('id', $order_id)->update(['status' => 2]);
            // Ghi lịch sử
            OrderHistory::add($order_id, 'received');
            return redirect(route('admin.orders.show', $order_id))->with([
                'type' => 'success',
                'message' => 'Cập nhật trạng thái thành công.',
            ]);
        } else {
            return redirect(route('admin.orders.show', $order_id))->with([
                'type' => 'danger',
                'message' => 'Chỉ chuyển được khi đơn là đơn hàng mới.',
            ]);
        }
    }

    /**
     * Thành công
     */
    public function success(Request $requests) {
        // Không có quyền sửa thì trả về trang chủ
        if (!checkRole($this->table_name.'_edit')) {
            return redirect(route('admin.home'))->with([
                'type' => 'danger',
                'message' => 'Core::admin.role.no_permission',
            ]);
        }
        // ID đơn hàng
        $order_id = $requests->order_id;
        // Lấy bản ghi
        $order = $this->models->where('id', $order_id)->first();
        // Nếu trạng thái đơn không phải đơn hàng mới thì sẽ không cho tiếp nhận
        if ($order->status == 2) {
            // Đổi trạng thái tiếp nhận
            $this->models->where('id', $order_id)->update(['status' => 4]);
            // Ghi lịch sử
            OrderHistory::add($order_id, 'order_success');
            return redirect(route('admin.orders.show', $order_id))->with([
                'type' => 'success',
                'message' => 'Cập nhật trạng thái thành công.',
            ]);
        } else {
            return redirect(route('admin.orders.show', $order_id))->with([
                'type' => 'danger',
                'message' => 'Chỉ chuyển được khi đơn đang được tiếp nhận.',
            ]);
        }
    }

    /**
     * Từ chối
     */
    public function denined(Request $requests) {
        // Không có quyền sửa thì trả về trang chủ
        if (!checkRole($this->table_name.'_edit')) {
            return redirect(route('admin.home'))->with([
                'type' => 'danger',
                'message' => 'Core::admin.role.no_permission',
            ]);
        }
        // ID đơn hàng
        $order_id = $requests->order_id;
        // Lấy bản ghi
        $order = $this->models->where('id', $order_id)->first();
        // Nếu trạng thái đơn không phải đơn hàng mới thì sẽ không cho tiếp nhận
        if ($order->status == 2) {
            // Đổi trạng thái tiếp nhận
            $this->models->where('id', $order_id)->update(['status' => 3]);
            // Ghi lịch sử
            OrderHistory::add($order_id, 'order_fail');
            return redirect(route('admin.orders.show', $order_id))->with([
                'type' => 'success',
                'message' => 'Cập nhật trạng thái thành công.',
            ]);
        } else {
            return redirect(route('admin.orders.show', $order_id))->with([
                'type' => 'danger',
                'message' => 'Chỉ chuyển được khi đơn đang được tiếp nhận.',
            ]);
        }
    }

}
