<?php 
// Chuyển id thành mã đơn hàng
function getOrderCode($number) {
	$code = '';
	for ($i=0; $i < 9-strlen($number); $i++) { 
		$code = $code.'0';
	}
	$code = $code.$number;
	return $code;
}

// Tách mã đơn hàng được chuyển sang id
function getOrderDecode($str) {
	$number = ltrim($str, '0');
	return $number;
}