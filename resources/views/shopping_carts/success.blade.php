<link rel="stylesheet" href="{{ asset('platforms/orders/web/css/cart.min.css') }}">

<section class="cart-notificate">
	<div class="cart-notificate__image">
		<img src="{{asset('platforms/orders/web/img/img-success.png')}}" alt="img-success">
	</div>
	<div class="cart-notificate__title">@lang('Đặt hàng thành công!')</div>
	@if(isset($order_id) && !empty($order_id))
		<div class="cart-notificate__code">
			<p class="text">@lang('Mã đơn hàng của bạn là:')</p>
			<p class="code">{{getOrderCode($order_id)}}</p>
		</div>
	@endif
	<div class="cart-notificate__subtitle">@lang('Cảm ơn bạn đã quan tâm đến sản phẩm của chúng tôi. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất!')</div>
	<div class="cart-notificate__gohome">
		<a href="{{ $coutinue_buy ?? '/' }}">@lang('Tiếp tục mua sắm')</a>
	</div>
</section>