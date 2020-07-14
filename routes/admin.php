<?php
App::booted(function() {
	$namespace = 'Sudo\Order\Http\Controllers\Admin';
	
	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin'])->group(function() {
		// Đơn hàng
		Route::resource('orders', 'OrderController');
		Route::post('orders/{order_id}/admin_note', 'OrderController@adminNote')->name('orders.admin_note');
		Route::get('orders/{order_id}/accepts', 'OrderController@accepts')->name('orders.accepts');
		Route::get('orders/{order_id}/success', 'OrderController@success')->name('orders.success');
		Route::get('orders/{order_id}/denined', 'OrderController@denined')->name('orders.denined');
		Route::get('orders/embed_history/{order_history_id}', 'OrderController@embedHistory')->name('orders.embed_history');
		// Khách hàng
		Route::resource('customers', 'CustomerController');
		Route::post('customers/exports', 'CustomerController@exports')->name('customers.exports');
	});
});