<?php
App::booted(function() {
	$namespace = 'Sudo\Order\Http\Controllers\App';

	Route::namespace($namespace)->name('app.')->middleware(['web'])->group(function() {
		// submit form
		Route::post('order-success', 'OrderController@store')->name('orders.store');
	});
	
	Route::namespace($namespace)->name('app.ajax.')->prefix('ajax')->middleware(['web'])->group(function() {
		// Thêm vào giỏ hàng
		Route::post('shopping_cart/add', 'ShoppingCartController@addToCart')->name('shopping_cart.add');
		// Xoá toàn bộ giỏ hàng
		Route::post('shopping_cart/destroy', 'ShoppingCartController@destroyCart')->name('shopping_cart.destroy');
		// Cập nhật số lượng sản phẩm
		Route::post('shopping_cart/update', 'ShoppingCartController@updateCart')->name('shopping_cart.update');
	});
});