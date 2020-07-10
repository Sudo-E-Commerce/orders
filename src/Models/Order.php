<?php

namespace Sudo\Order\Models;

use Sudo\Base\Models\BaseModel;

class Order extends BaseModel {
	
	public static function add($data) {
		$data = json_decode(json_encode(removeScriptArray($data)));
		$time = date('Y-m-d H:i:s');
		$order_id = Order::insertGetId([
			'customer_id' 		=> $data->customer_id,
			'total_price' 		=> $data->total_price,
			'note' 				=> $data->note,
			'payment_method' 	=> $data->payment_method,
			'status' 			=> 1,
			'created_at' 		=> $time,
			'updated_at' 		=> $time,
		]);
		\Sudo\Order\Models\OrderHistory::add($order_id, 'admin_create');
		return $order_id;
	}

	public function getTotalPrice() {
		return formatPrice($this->total_price, null);
	}

	public function getStatus() {
		switch ($this->status) {
			case '1':
				$status = $this->status;
				$status_text = __('Đơn hàng mới');
				$status_label = '<p class="badge badge-info m-0">'.$status_text.'</p>';
			break;
			case '2': 
				$status = $this->status;
				$status_text = __('Đã tiếp nhận');
				$status_label = '<p class="badge badge-primary m-0">'.$status_text.'</p>';
			break;
			case '3': 
				$status = $this->status;
				$status_text = __('Huỷ');
				$status_label = '<p class="badge badge-danger m-0">'.$status_text.'</p>';
			break;
			case '4': 
				$status = $this->status;
				$status_text = __('Thành công');
				$status_label = '<p class="badge badge-success m-0">'.$status_text.'</p>';
			break;
		}
		return [
			'status' 		=> $status,
			'status_text' 	=> $status_text,
			'status_label' 	=> $status_label,
		];
	}
}