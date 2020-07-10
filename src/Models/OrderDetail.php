<?php

namespace Sudo\Order\Models;

use Sudo\Base\Models\BaseModel;

class OrderDetail extends BaseModel {
	
	/*
		$data = [
            'order_id'      => $order_id,
            'product_id'    => $product_id,
            'price'         => $price,
            'quantity'      => $quantity,
        ];
	*/
	public static function add($data) {
		$data = json_decode(json_encode(removeScriptArray($data)));
		OrderDetail::insert([
			'order_id' 		=> $data->order_id,
			'product_id' 	=> $data->product_id,
			'price' 		=> $data->price,
			'quantity' 		=> $data->quantity,
		]);
	}

}