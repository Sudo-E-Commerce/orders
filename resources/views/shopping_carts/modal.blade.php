{{-- Modal hiển thị khi thêm vào giỏ --}}
<link rel="stylesheet" href="{{ asset('platforms/orders/web/css/modal.min.css') }}">
<div class="cart-popup" id="cart-add-popup">
	<div class="cart-popup__close" data-cart_popup_close></div>
	<div class="cart-popup__dialog">
		<div class="cart-popup__header">
			<div class="cart-popup__header__text">@lang('Thông báo')</div>
			<div class="cart-popup__header__close" data-cart_popup_close><i class="fa fa-remove"></i></div>
		</div>
		<div class="cart-popup__body">
			<p class="cart-popup__body__notificate">@lang('Cập nhật vào giỏ hàng thành công.')</p>
		</div>
		<div class="cart-popup__footer">
			<a href="{{ $coutinue_buy ?? '#' }}" class="btn-close" @if (!isset($coutinue_buy)) data-cart_popup_close @endif >@lang('Tiếp tục mua sắm')</a>
			<a href="{{ $shopping_cart_link ?? '#' }}" class="btn-gotocart">@lang('Đi đến giỏ hàng')</a>
		</div>
	</div>
</div>
<script src="{{ asset('platforms/orders/web/js/modal.min.js') }}"></script>