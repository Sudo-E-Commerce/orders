@extends('Core::layouts.app')

@section('title') @lang('Chi tiết đơn hàng')  @endsection
@section('content')

<div class="row">
	<div class="col-lg-6 col-md-12">
		{{-- Đơn hàng --}}
		<div class="card">
			<div class="card-header" data-card-widget="collapse">
				<div class="card-title">@lang('Thông tin đơn hàng')</div>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				</div>
			</div>
			<div class="card-body p-3">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th class="p-2" style="width: 200px;">@lang('Mã đơn hàng')</th>
							<td class="p-2">{{getOrderCode($order->id)}}</td>
						</tr>
						<tr>
							<th class="p-2" style="width: 200px;">@lang('Giá trị đơn')</th>
							<td class="p-2">{{$order->getTotalPrice()}}</td>
						</tr>
						<tr>
							<th class="p-2" style="width: 200px;">@lang('Hình thức thanh toán')</th>
							<td class="p-2">@lang($payment_method[$order->payment_method] ?? '')</td>
						</tr>
						<tr>
							<th class="p-2" style="width: 200px;">@lang('Trạng thái')</th>
							<td class="p-2">{!!$order->getStatus()['status_label']!!}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		{{-- Khách --}}
		@if (isset($customers) && !empty($customers))
			<div class="card">
				<div class="card-header" data-card-widget="collapse">
					<div class="card-title">@lang('Thông tin khách hàng')</div>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
					</div>
				</div>
				<div class="card-body p-3">
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th class="p-2" style="width: 200px;">@lang('Tên')</th>
								<td class="p-2">{{!empty($customers->phone) ? $customers->name : __('Không cung cấp')}}</td>
							</tr>
							<tr>
								<th class="p-2" style="width: 200px;">@lang('Điện thoại')</th>
								<td class="p-2">{{!empty($customers->phone) ? $customers->phone : __('Không cung cấp')}}</td>
							</tr>
							<tr>
								<th class="p-2" style="width: 200px;">@lang('Email')</th>
								<td class="p-2">{{!empty($customers->email) ? $customers->email : __('Không cung cấp')}}</td>
							</tr>
							<tr>
								<th class="p-2" style="width: 200px;">@lang('Địa chỉ')</th>
								<td class="p-2">{{!empty($customers->address) ? $customers->address : __('Không cung cấp')}}</td>
							</tr>
							<tr>
								<th class="p-2" style="width: 200px;">@lang('Ghi chú tại đơn')</th>
								<td class="p-2">{{$order->note ?? __('Không')}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		@endif
		{{-- Sản phẩm --}}
		@if (isset($order_details) && !empty($order_details))
			@php
				$product_array_id = $order_details->pluck('product_id')->toArray();
				$products = \DB::table('products')->whereIn('id', $product_array_id)->get();
				$total_price = 0;
			@endphp
			@if (isset($products) && count($products) > 0)
				<div class="card">
					<div class="card-header" data-card-widget="collapse">
						<div class="card-title">@lang('Thông tin sản phẩm')</div>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-3">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th class="text-center p-2" style="width: 60px;">@lang('Ảnh')</th>
									<th class="text-center p-2">@lang('Tên sản phẩm')</th>
									<th class="text-center p-2" style="width: 130px;">@lang('Giá')</th>
									<th class="text-center p-2" style="width: 100px;">@lang('Số lượng')</th>
									<th class="text-center p-2" style="width: 130px;">@lang('Tổng giá')</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($products as $item)
									@php
										$product_details = $order_details->where('product_id', $item->id)->first();
										$price = $product_details->price ?? $item->price ?? 0;
										$quantity = $product_details->quantity ?? 0;
										$total_price = $total_price+($price*$quantity);
									@endphp
									<tr>
										<td class="p-2" style="height: 60px;">
											<img src="{{getImage($item->image)}}" style="width: 100%; height: 100%; object-fit: contain;">
										</td>
										<td class="p-2">{{$item->name}}</td>
										<td class="p-2">{{formatPrice($price)}}</td>
										<td class="p-2">{{$quantity}}</td>
										<td class="p-2">{{formatPrice($price*$quantity)}}</td>
									</tr>
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4" class="text-right"><strong>@lang('Tổng giá trị đơn')</strong></td>
									<td>{{formatPrice($total_price)}}</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			@endif
		@endif
	</div>
	<div class="col-lg-6 col-md-12">
		<div class="col-lg-12 p-0">
			<div class="card">
				<div class="card-header" data-card-widget="collapse">
					<div class="card-title">@lang('Thêm ghi chú')</div>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
					</div>
				</div>
				<div class="card-body p-3">
					<form action="{{ route('admin.orders.admin_note', $order->id) }}" method="POST">
						@csrf
						<div class="form-group">
							<textarea name="admin_note" id="admin_note" rows="3" class="form-control" placeholder="@lang('Thêm ghi chú')"></textarea>
						</div>
						<div class="form-group mb-0">
							<button class="btn btn-info btn-sm" type="submit">@lang('Thêm ghi chú')</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-lg-12 p-0">
			<div class="card">
				<div class="card-header" data-card-widget="collapse">
					<div class="card-title">@lang('Lịch sử đơn hàng')</div>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
					</div>
				</div>
				<div class="card-body p-3">
					<div class="timeline">
						 @php
				            $date_array = [];
				            foreach ($order_histories as $value){
				                $time = date("d-m-Y",strtotime($value->time));
				                if (!in_array($time, $date_array)) {
				                    array_push($date_array, $time);
				                }
				            }
				        @endphp
				        @foreach ($date_array as $date)
							<div class="time-label">
								<span class="bg-red">{{ $date ?? '' }}</span>
							</div>
							@foreach ($order_histories as $value)
								@php
				                    $time = date("d-m-Y",strtotime($value->time));
				                @endphp
				                @if ($date == $time)
									@switch($value->type)

									    @case('admin_create')
									        <div>
												<i class="fas fa-shopping-cart bg-primary"></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">
														<a href="{{ route('admin.admin_users.edit', $value->admin_user_id) }}" target="_blank">{{$admin_users[$value->admin_user_id] ?? ''}}</a> 
														@lang('đã tạo đơn hàng')
													</h3>
												</div>
											</div>
								        @break

								        @case('customer_create')
											<div>
												<i class="fas fa-shopping-cart bg-primary"></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">
														<a href="javascript:;">@lang('Khách hàng')</a> 
														@lang('đã tạo đơn hàng')
													</h3>
												</div>
											</div>
								        @break

								        @case('order_fail') 
											<div>
												<i class="fas fa-ban bg-danger"></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">
														<a href="{{ route('admin.admin_users.edit', $value->admin_user_id) }}" target="_blank">{{$admin_users[$value->admin_user_id] ?? ''}}</a> 
														@lang('đã cập nhật trạng thái đơn hàng') 
														<span class="badge badge-danger">@lang('Huỷ')</span>
													</h3>
												</div>
											</div>
								        @break

								        @case('order_success') 
											<div>
												<i class="fas fa-check-circle bg-success"></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">
														<a href="{{ route('admin.admin_users.edit', $value->admin_user_id) }}" target="_blank">{{$admin_users[$value->admin_user_id] ?? ''}}</a> 
														@lang('đã cập nhật trạng thái đơn hàng') 
														<span class="badge badge-success">@lang('Thành công')</span>
													</h3>
												</div>
											</div>
								        @break

								        @case('received') 
											<div>
												<i class="fas fa-user bg-primary"></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">
														<a href="{{ route('admin.admin_users.edit', $value->admin_user_id) }}" target="_blank">{{$admin_users[$value->admin_user_id] ?? ''}}</a> 
														@lang('đã cập nhật trạng thái đơn hàng') 
														<span class="badge badge-primary">@lang('Đã tiếp nhận')</span>
													</h3>
												</div>
											</div>
								        @break

								        @case('admin_note')
								        	@php
								        		$note = json_decode(base64_decode($value->data ?? ''));
								        	@endphp
											<div>
												<i class="fas fa-pencil-alt bg-warning "></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">
														<a href="{{ route('admin.admin_users.edit', $value->admin_user_id) }}" target="_blank">{{$admin_users[$value->admin_user_id] ?? ''}}</a> 
														@lang('đã thêm ghi chú') 
													</h3>
													<div class="timeline-body" style="white-space: pre;">{{ $note ?? '' }}</div>
												</div>
											</div>
								        @break

								        @case('order_change')
											<div>
												<i class="fas fa-edit bg-info"></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">
														<a href="{{ route('admin.admin_users.edit', $value->admin_user_id) }}" target="_blank">{{$admin_users[$value->admin_user_id] ?? ''}}</a> 
														@lang('đã chỉnh sửa chi tiết đơn hàng') 
													</h3>
													<div class="timeline-footer">
														<a class="btn btn-info btn-sm" data-order_history="{{ route('admin.orders.embed_history', $value->id) }}">@lang('Click xem chi tiết')</a>
													</div>
												</div>
											</div>
								        @break

									@endswitch
				                @endif
							@endforeach
				        @endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="form-actions">
	<div class="form-actions__group">
		@if ($order->status == 1)
			<a href="{{ route('admin.orders.accepts', $order->id) }}" class="btn btn-sm btn-success">
				<i class="fa fa-check mr-1"></i> 
				@lang('Tiếp nhận đơn')
			</a>
		@endif
		@if ($order->status == 2)
			<a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-primary">
				<i class="fa fa-edit mr-1"></i> 
				@lang('Chỉnh sửa đơn hàng')
			</a>
			<a href="{{ route('admin.orders.success', $order->id) }}" class="btn btn-sm btn-success">
				<i class="fa fa-check-circle mr-1"></i> 
				@lang('Thành công')
			</a>
			<a href="{{ route('admin.orders.denined', $order->id) }}" class="btn btn-sm btn-danger">
				<i class="fa fa-ban mr-1"></i> 
				@lang('Từ chối')
			</a>
		@endif
		<a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-danger">
			<i class="fa fa-sign-out-alt mr-1"></i> 
			@lang('Form::form.action.exit')
		</a>
	</div>
</div>
<div class="modal fade" id="order_history">
	<div class="modal-dialog" style="max-width: 50%;">
		<form action="{{ route('admin.ajax.comments.quick_reply', $value->id) }}" method="POST">
			<div class="modal-content">
				<div class="modal-body p-0" style="height: calc(100vh - 60px);">
					<iframe src="" frameborder="0" class="float-left" style="width: 100%; height: 100%;"></iframe>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('body').on('click', '*[data-order_history]', function() {
			$('#order_history').find('iframe').attr('src', $(this).data('order_history'));
			$('#order_history').modal();
		});
		$('#order_history').on('hidden.bs.modal', function() {
			$(this).find('iframe').attr('src', '');
		})
	});
</script>
@endsection