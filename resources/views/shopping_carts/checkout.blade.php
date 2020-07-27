{{-- Giỏ hàng --}}
<link rel="stylesheet" href="{{ asset('platforms/orders/web/css/checkout.min.css') }}">

<form action="{{ route('app.orders.store') }}" method="POST" class="checkout-form-submit">
@csrf
<section class="checkout">
	<div class="checkout-form">
		<div class="checkout-title">@lang('Thông tin mua hàng')</div>
		@if (count($errors) > 0)
			@foreach ($errors->all() as $error)
				<p class="checkout-error">@lang($error??'') <span class="checkout-error__close">&#x2716;</span></p>
			@endforeach
		@endif

		<div class="checkout-form__group">
			<input type="text" class="checkout-form__control" name="customer_name" placeholder="@lang('Họ và tên*')">
		</div>
		<div class="checkout-form__group">
			<input type="text" class="checkout-form__control" name="customer_phone" placeholder="@lang('Điện thoại*')">
		</div>
		<div class="checkout-form__group">
			<input type="text" class="checkout-form__control" name="customer_email" placeholder="@lang('Email*')">
		</div>
		<div class="checkout-form__group">
			<input type="text" class="checkout-form__control" name="customer_address" placeholder="@lang('Địa chỉ')">
		</div>
		<div class="checkout-form__group">
			<textarea name="note" class="checkout-form__control" placeholder="@lang('Ghi chú')"></textarea>
		</div>
		<div class="checkout-helper">@lang('Lưu ý: Trường có dấu (*) là bắt buộc')</div>
	</div>
	<div class="checkout-info">
		<div class="checkout-title">@lang('Hình thức thanh toán')</div>
		<div class="checkout-info__payment-method">
			@foreach (config('SudoOrder.payment_method') as $key => $value)
				<div class="checkout-form__group">
					<input type="radio" class="cart-radio" name="payment_method" id="payment_method_{{$key ?? ''}}" value="{{$key ?? ''}}"
						@if (array_keys(config('SudoOrder.payment_method'))[0] == $key) checked  @endif
					>
					<label for="payment_method_{{$key ?? ''}}">@lang($value ?? '')</label>
				</div>
			@endforeach
		</div>
	</div>
	<div class="checkout-submit">
		@php
			$cart = \Cart::content();
		@endphp
		@if (count($cart) > 0)
			<div class="checkout-submit__cart">
				<div class="checkout-title">@lang('Đơn hàng') (1 @lang('sản phẩm'))</div>
				<div class="checkout-submit__cart-list">
					@foreach ($cart as $item)
						<div class="item">
							<div class="item-image">
								<img src="{{getImage($item->options->image ?? '')}}" alt="">
							</div>
							<div class="item-info">
								<h3 class="item-info__name"><a href="#">{{$item->name ?? ''}}</a></h3>
								<p class="item-info__price">{{formatPrice($item->price ?? '')}}</p>
								<p class="item-info__quantity">{{ $item->qty ?? 0 }}</p>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		@endif
		<div class="checkout-submit__content">
			<table class="checkout-submit__content-table" border="1">
				<tbody>
					<tr>
						<td>@lang('Thành tiền')</td>
						<td>{{formatPrice(\Cart::priceTotal(0, '', ''))}}</td>
					</tr>
					<tr>
						<td>@lang('Phí vận chuyển')</td>
						<td>{{formatPrice(0, null)}}</td>
					</tr>
					<tr>
						<td>@lang('Tổng tiền')</td>
						<td>{{formatPrice(\Cart::priceTotal(0, '', ''))}}</td>
					</tr>
				</tbody>
			</table>
			<div class="checkout-submit__content-submit">
				<button type="submit">@lang('Đặt hàng')</button>
			</div>
		</div>
	</div>
</section>
</form>

<section class="cart-popup" id="checkout-notificate">
	<div class="cart-popup__close" data-cart_popup_close></div>
	<div class="cart-popup__dialog">
		<div class="cart-popup__header">
			<div class="cart-popup__header__text">@lang('Thông báo')</div>
			<div class="cart-popup__header__close" data-cart_popup_close><i class="fa fa-remove"></i></div>
		</div>
		<div class="cart-popup__body">
			<p class="cart-popup__body__notificate"></p>
		</div>
		<div class="cart-popup__footer">
			<a href="/" class="btn-close" data-cart_popup_close>@lang('Đóng')</a>
		</div>
	</div>
</section>

<div class="cart-loading"><div class="cart-loading__box"></div></div>
@php
	$variable = base64_encode(json_encode([
		'require_customer' => __('Thông tin bắt buộc').' '.__('không được để trống!'),
		'require_customer_phone_format' => __('Định dạng Điện thoại không chính xác!'),
		'require_customer_email_format' => __('Định dạng Email không chính xác!'),
		'require_payment_method' => __('Vui lòng chọn một hình thức thanh toán!'),
	]));
@endphp
<div class="lang_orders" data-value="{{$variable ?? ''}}" ></div>
<script src="{{ asset('platforms/orders/web/js/checkout.min.js') }}"></script>