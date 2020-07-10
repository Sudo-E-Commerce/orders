<?php

namespace Sudo\Order\Models;

use Sudo\Base\Models\BaseModel;

class OrderHistory extends BaseModel {
	
	/**
	 * Thêm lịch sử đơn hàng
	 * @param number  	$order_id: ID của đơn hàng
	 * @param string  	$type: Loại hành động 
	 					* admin_create Admin tạo đơn hàng 
	 					* customer_create Khách tạo đơn 
	 					* order_change sửa đơn 
	 					* order_fail huỷ 
	 					* order_success thành công 
	 					* admin_note Ghi chú của admin
	 * @param  array  	$type: Dữ liệu hành động (Lưu dạng json) (Sửa đơn (order_change) và admin ghi chú (admin_note) sẽ có dữ liệu)
	 */
	public static function add($order_id, $type, $data = []) {
		if (!empty($data)) {
			$data = base64_encode(json_encode($data));
		} else {
			$data = null;
		}
		OrderHistory::insert([
			'order_id' => $order_id,
			'admin_user_id' => \Auth::guard('admin')->user()->id ?? 0,
			'type' => $type,
			'data' => $data,
			'time' => date('Y-m-d H:i:s'),
		]);
	}

	/**
	 * Lấy toàn bộ lịch sử hành động của đơn hàng
	 */
	public static function getOrderHistory($order_id) {
		$histories = OrderHistory::where('order_id', $order_id)->orderBy('time', 'desc')->get();
		return $histories;
	}
}