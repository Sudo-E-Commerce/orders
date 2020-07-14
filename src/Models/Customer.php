<?php

namespace Sudo\Order\Models;

use Sudo\Base\Models\BaseModel;

class Customer extends BaseModel {
	
	/*
		$customers = [
            'name'      => $name,
            'phone'     => $phone,
            'email'     => $email,
            'address'   => $address,
        ];
	*/
	public static function add($data) {
		$data = json_decode(json_encode(removeScriptArray($data)));
		$time = date('Y-m-d H:i:s');
		// Kiểm tra đã có khách này chưa theo số điện thoại
		$check_exist = Customer::where('phone', $data->phone)->first();
		// Dùng số điện thoại để kiểm tra
		// Nếu có thì cập nhật, không có thì thêm mới
		if ($check_exist == null) {
			// Không có số điện thoại thì trả về 0
			if (empty($data->phone)) {
				return 0;
			}
			$customer_id = Customer::insertGetId([
				'name' 		=> $data->name,
				'phone' 	=> $data->phone,
				'email' 	=> $data->email,
				'address' 	=> $data->address,
				'status' 	=> 1,
				'created_at' => $time,
				'updated_at' => $time,
			]);
			return $customer_id;
		} else {
			Customer::where('id', $check_exist->id)->update([
				'name' 		=> $data->name,
				'email' 	=> $data->email,
				'address' 	=> $data->address,
				'updated_at' => $time,
			]);
			return $check_exist->id;
		}
	}

}