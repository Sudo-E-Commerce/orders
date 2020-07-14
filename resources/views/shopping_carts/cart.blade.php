{{-- Giỏ hàng --}}
<link rel="stylesheet" href="{{ asset('platforms/orders/web/css/cart.min.css') }}">
<section class="cart">
	@if (count(\Cart::content()) > 0)
		<h3 class="cart-title">@lang('Giỏ hàng')</h3>
		<section class="cart-content">
			<table class="cart-table" border="1">
				<thead>
					<tr>
						<th>@lang('Ảnh')</th>
						<th>@lang('Tên sản phẩm')</th>
						<th>@lang('Số lượng')</th>
						<th>@lang('Thành tiền')</th>
						<th>@lang('Giá')</th>
						<th>@lang('Xoá')</th>
					</tr>
				</thead>
				<tbody>
					@foreach (\Cart::content() as $item)
						@php
							$price = $item->price ?? 0;
							$quantity = $item->qty ?? 0;
						@endphp
						<tr data-rowID="{{$item->rowId ?? ''}}">
							<td class="cart-table__image">
								<img src="{{getImage($item->options->image ?? '')}}">
							</td>
							<td class="cart-table__name">
								<a href="javascript:;">{{$item->name ?? ''}}</a>
							</td>
							<td class="cart-table__price">{{formatPrice($price)}}</td>
							<td class="cart-table__quantity">
								<button type="button" class="cart-update cart-minus">-</button>
								<input type="text" name="quantity" value="{{$quantity}}" disabled>
								<button type="button" class="cart-update cart-plus">+</button>
							</td>
							<td class="cart-table__subtotal">{{formatPrice($item->subtotal(0,'',''))}}</td>
							<td class="cart-table__action">
								<button type="button" class="cart-delete" data-cart_delete_one_popup><i class="fa fa-trash"></i></button>
							</td>
						</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" class="cart-total">@lang('Phí vận chuyển')</td>
						<td colspan="2">@lang('Miễn phí')</td>
					</tr>
					<tr>
						<td colspan="4" class="cart-total">@lang('Tổng tiền')</td>
						<td colspan="2" id="cart-total">{{formatPrice(\Cart::priceTotal(0, '', ''))}}</td>
					</tr>
				</tfoot>
			</table>
		</section>
		<section class="cart-checkout">
			<a href="{{ $coutinue_buy ?? '#' }}" class="btn-gohome">@lang('Tiếp tục mua')</a>
			<a href="/" class="btn-deleteall" data-cart_delete_all_popup>@lang('Xoá giỏ hàng')</a>
			<a href="{{$link_checkout ?? '#'}}" class="btn-checkout">@lang('Đặt hàng')</a>
		</section>
	@endif

	<section class="cart-notificate cart-empty" @if (count(\Cart::content()) > 0) style="display: none;" @endif>
		<div class="cart-notificate__image">
			<img src="{{asset('platforms/orders/web/img/img-success.png')}}" alt="img-success">
		</div>
		<div class="cart-notificate__title">@lang('Giỏ hàng trống!')</div>
		<div class="cart-notificate__subtitle">@lang('Không có sản phẩm nào trong giỏ hàng của bạn!')</div>
		<div class="cart-notificate__gohome">
			<a href="{{ $coutinue_buy ?? '/' }}">@lang('Tiếp tục mua sắm')</a>
		</div>
	</section>
</section>
<section class="cart-popup" id="cart-delete">
	<div class="cart-popup__close" data-cart_popup_close></div>
	<div class="cart-popup__dialog">
		<div class="cart-popup__header">
			<div class="cart-popup__header__text">@lang('Thông báo')</div>
			<div class="cart-popup__header__close" data-cart_popup_close><i class="fa fa-remove"></i></div>
		</div>
		<div class="cart-popup__body">
			<p class="cart-popup__body__notificate">@lang('Bạn có chắc là muốn xoá sản phẩm tại giỏ hàng?')</p>
		</div>
		<div class="cart-popup__footer">
			<a href="/" class="btn-close" data-cart_popup_close>@lang('Đóng')</a>
			<a href="{{ $shopping_cart_link ?? '#' }}" class="btn-gotocart" data-cart_delete_all>@lang('Xác nhận')</a>
		</div>
	</div>
</section>
<section class="cart-popup" id="cart-notificate">
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
<script src="{{ asset('platforms/orders/web/js/cart.min.js') }}"></script>