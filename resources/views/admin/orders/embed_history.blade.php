@if (isset($data) && !empty($data))
@extends('Core::layouts.embed')
@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">
			<div class="col-lg-12 p-2">
				@php
					$option_orders = [
						[ 
							'title' => 'Tên người đặt', 
							'old' => $data['old']['customers']['name'] ?? '',
							'new' => $data['new']['customers']['name'] ?? '',
						],
						[ 
							'title' => 'Điện thoại', 
							'old' => $data['old']['customers']['phone'] ?? '',
							'new' => $data['new']['customers']['phone'] ?? '',
						],
						[ 
							'title' => 'Email', 
							'old' => $data['old']['customers']['email'] ?? '',
							'new' => $data['new']['customers']['email'] ?? '',
						],
						[ 
							'title' => 'Địa chỉ', 
							'old' => $data['old']['customers']['address'] ?? '',
							'new' => $data['new']['customers']['address'] ?? '',
						],
						[ 
							'title' => 'Ghi chú tại đơn', 
							'old' => $data['old']['orders']['note'] ?? '',
							'new' => $data['new']['orders']['note'] ?? '',
						],
						[ 
							'title' => 'Hình thức thanh toán', 
							'old' => $payment_method[ $data['old']['orders']['payment_method']??'' ] ?? '',
							'new' => $payment_method[ $data['new']['orders']['payment_method']??'' ] ?? '',
						],

					];
				@endphp
				<div class="table-responsive">
					<table class="table table-bordered mb-0" style="min-width: 800px;">
						<tbody>
							<tr>
								<th class="p-2" colspan="3">@lang('Thông tin đơn hàng')</th>
							</tr>
							<tr>
								<th class="p-2" style="width: 200px;"></th>
								<th class="p-2">@lang('Cũ')</th>
								<th class="p-2">@lang('Mới')</th>
							</tr>
							@foreach ($option_orders as $value)
								<tr>
									<th class="p-2">@lang($value['title'] ?? '')</th>
									<td class="p-2">{{ $value['old'] ?? '' }}</td>
									<td class="p-2 @if ($value['old'] != $value['new']) bg-success @endif"
									>{{ $value['new'] ?? '' }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-lg-12 p-2">
				@php
					// ID SP ở đơn hàng cũ
					$product_old = collect($data['old']['products'] ?? '');
					$product_old_array_id = $product_old->pluck('product_id')->toArray();
					// ID SP ở đơn hàng mới
					$product_new = collect($data['new']['products'] ?? '');
					$product_new_array_id = $product_new->pluck('product_id')->toArray();
					// ID SP ở đơn hàng cả cũ và mới
					$product_array_id = array_merge($product_new_array_id, $product_old_array_id);
					// Lấy dữ liệu sản phẩm
					$products = \DB::table('products')->whereIn('id', $product_array_id)->get()->pluck('name', 'id')->toArray();
				@endphp
				<div class="table-responsive">
					<table class="table table-bordered mb-0" style="min-width: 800px;">
						<tbody>
							<tr>
								<th class="p-2" colspan="5">@lang('Thông tin sản phẩm')</th>
							</tr>
							<tr>
								<th class="p-2" style="width: 60px;"></th>
								<th class="p-2">@lang('Tên sản phẩm')</th>
								<th class="p-2" style="width: 150px;">@lang('Giá')</th>
								<th class="p-2" style="width: 100px;">@lang('Số lượng')</th>
								<th class="p-2" style="width: 150px;">@lang('Tổng giá')</th>
							</tr>
							@foreach ($data['old']['products']??[] as $key => $value)
								@php
									$price = $value['price'] ?? 0;
									$quantity = $value['quantity'] ?? 0;
								@endphp
								<tr>
									@if ($key == 0)
									<th class="p-2" rowspan="{{count($data['old']['products'] ?? [])}}">@lang('Cũ')</th>
									@endif
									<td class="p-2"><strong>{{$products[$value['product_id']??0] ?? __('Không xác định')}}</strong></td>
									<td class="p-2">{{ formatPrice($price) }}</td>
									<td class="p-2">{{ $quantity }}</td>
									<td class="p-2">{{ formatPrice($price*$quantity) }}</td>
								</tr>
							@endforeach
							@foreach ($data['new']['products']??[] as $key => $value)
								@php
									$price = $value['price'] ?? 0;
									$quantity = $value['quantity'] ?? 0;
								@endphp
								<tr 
									@if ($data['old']['products'] != $data['new']['products'])
										class="bg-success"
									@endif
								>
									@if ($key == 0)
									<th class="p-2" rowspan="{{count($data['new']['products'] ?? [])}}">@lang('Mới')</th>
									@endif
									<td class="p-2"><strong>{{$products[$value['product_id']??0] ?? __('Không xác định')}}</strong></td>
									<td class="p-2">{{ formatPrice($price) }}</td>
									<td class="p-2">{{ $quantity }}</td>
									<td class="p-2">{{ formatPrice($price*$quantity) }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
    </div>
</section>

@endsection
@endif