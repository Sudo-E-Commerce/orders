{{-- 
	$form->custom('Order::admin.orders.form.product_in_orders', [
        'name' => 'products',
        'value' => [
            ['product_id' => 1, 'price' => '1000000', 'quantity' => 2],
            ['product_id' => 2, 'price' => '2000000', 'quantity' => 1],
        ],
    ]);
--}}
<div class="row listdata">
	<div class="col-lg-12 suggest">
		<input type="text" class="form-control" id="search_products" placeholder="@lang('Tìm sản phẩm thêm vào đơn hàng...')">
		<div class="suggest-result"><ul></ul></div>
	</div>
	<div class="col-lg-12">
		<table class="table table-striped table-bordered mb-0">
			<thead>
				<tr>
					<th class="text-center">@lang('Ảnh')</th>
					<th class="text-center">@lang('Tên sản phẩm')</th>
					<th class="text-center" style="width: 120px;">@lang('Số lượng')</th>
					<th class="text-center" style="width: 120px;">@lang('Giá')</th>
					<th class="text-center" style="width: 120px;">@lang('Tổng giá')</th>
					<th class="text-center">@lang('Xoá')</th>
				</tr>
			</thead>
			<tbody>
				@php
					$order_detail_array = collect($value ?? []);
					$product_array_id = $order_detail_array->pluck('product_id')->toArray();
					$products = \DB::table('products')->whereIn('id', $product_array_id)->get();
					$total_price = 0;
				@endphp
				@if (isset($products) && count($products) > 0)
					@foreach ($products as $item)
						@php
							$order_detail = $order_detail_array->where('product_id', $item->id)->first();
							$quantity = $order_detail['quantity'] ?? 0;
							$price = (int)$order_detail['price'] ?? 0;
							$total_price = $total_price+($price*$quantity);
						@endphp
						<tr data-product_item="{{$item->id}}">
							<td class="table-image">
								<img src="{{getImage($item->image ?? '')}}">
							</td>
							<td>
								<a href="{{ route('admin.products.edit', $item->id) }}"><strong>{{ $item->name }}</strong></a>
								<input type="hidden" name="{{$name}}[{{$item->id}}][id]" value="{{ $item->id }}">
							</td>
							<td>
								<input type="number" class="form-control input-sm quantity" name="{{$name}}[{{$item->id}}][quantity]" value="{{ $quantity }}">
							</td>
							<td>
								<input type="number" class="form-control input-sm price" name="{{$name}}[{{$item->id}}][price]" value="{{ $price }}">
							</td>
							<td>{{ formatPrice($quantity*$price, null) }}</td>
							<td class="text-center table-action">
					            <a class="delete-record" href="javascript:;" data-delete_item data-message="@lang('Table::table.delete_question')"><i class="fas fa-trash text-red"></i></a>
					        </td>
						</tr>
					@endforeach
				@else
					<tr>
						<td colspan="6" class="text-center"><strong>@lang('Table::table.no_record')</strong></td>
					</tr>
				@endif
			</tbody>
			<tfoot> 
				<tr>
					<td colspan="4" class="text-right"><strong>@lang('Tổng giá trị đơn')</strong></td>
					<td colspan="2">
						<strong id="total_price">{{formatPrice($total_price ?? 0, null)}}</strong>
						<input type="hidden" name="total_price" value="{{ $total_price ?? 0 }}">
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<script>
		$(document).ready(function() {
			$('.listdata').closest('.card').css('marginBottom', '65px');
			// Click xoá sản phẩm
			$('body').on('click', '*[data-delete_item]', function(e) {
				e.preventDefault();
				if ($('*[data-product_item]').length-1 == 0) {
					$(this).closest('tbody').html(`
						<tr>
							<td colspan="6" class="text-center"><strong>@lang('Table::table.no_record')</strong></td>
						</tr>
					`);
				}
				$(this).closest('tr').remove();
				setTotalPrice();
			});
			// Thay đổi số lượng
			$('body').on('keyup', '.quantity', function() {
				if ($(this).val() <= 0) {
					$(this).val(1);
				}
				setTotalPrice();
			});
			// Thay đổi giá
			$('body').on('keyup', '.price', function() {
				setTotalPrice();
			});
			// Tìm kiếm sản phẩm
			suggest = null;
			$('body').on('keyup', '#search_products', function(e) {
				e.preventDefault();
				clearTimeout(suggest);
				e = $(this);
				keyword = $(this).val();
				if (keyword.length == 0) {
					$(this).closest('.suggest').find('.suggest-result').css('display','none').find('ul').empty();
				} else {
					data = {
						keyword: keyword,
						table: 'products',
						table_field: 'name',
					};
					// Không tìm theo sản phẩm đã lấy
	                id_not_where = [];
	                $.each($('*[data-product_item]') ,function() {
	                	id_not_where.push($(this).data('product_item'));
	                });
	                if(!checkEmpty(id_not_where)) {
	                    data.id_not_where = id_not_where;
	                }
					suggest = setTimeout(function() {
						loadAjaxPost('{{ route('admin.ajax.suggest_table') }}', data, {
							beforeSend: function(){},
					        success:function(result){
					        	if (result.status == 1) {
					        		e.closest('.suggest').find('.suggest-result').css('display','block');
					        		str = '';
					        		$.each(result.data, function(index, item) {
					        			str += `<li 
				        						data-suggest_{{$name}}
				        						data-id_{{$name}}="${item.id}"
				        						data-image_{{$name}}="${item.image}"
				        						data-price_{{$name}}="${item.price}"
				        						data-name_{{$name}}="${item.name}"
					        				>${item.name}</li>`;
					        		})
					        		e.closest('.suggest').find('.suggest-result').find('ul').append(str);
					        	} else {
					        		e.closest('.suggest').find('.suggest-result').css('display','none').find('ul').empty();
					        		alertText(result.message, 'error');
					        	}
					        },
					        error: function (error) {}
						});
					}, 1000);
				}
			});
			// Click vào thẻ li
			$('body').on('click', '*[data-suggest_{{$name}}]', function(e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				id = $(this).data('id_{{$name}}');
				image = $(this).data('image_{{$name}}');
				price = $(this).data('price_{{$name}}');
				name = $(this).data('name_{{$name}}');
				$(this).closest('.listdata').find('table').find('tbody').append(`
					<tr data-product_item="${id}">
						<td class="table-image">
							<img src="${image}">
						</td>
						<td>
							<strong>${name}</strong>
							<input type="hidden" name="{{$name}}[${id}][id]" value="${id}">
						</td>
						<td>
							<input type="number" class="form-control input-sm quantity" name="{{$name}}[${id}][quantity]" value="1">
						</td>
						<td>
							<input type="number" class="form-control input-sm price" name="{{$name}}[${id}][price]" value="${price}">
						</td>
						<td>${formatPrice(price)}</td>
						<td class="text-center table-action">
				            <a class="delete-record" href="javascript:;" data-delete_item data-message="@lang('Table::table.delete_question')"><i class="fas fa-trash text-red"></i></a>
				        </td>
					</tr>
				`);
				$(this).remove();
				setTotalPrice();
			})
			// Nếu click ra vùng bất kỳ thì ẩn box suggest
	        $(document).bind('click', function(e) {
	            var clicked = $(e.target);
	            if (!clicked.parent('ul').parent().hasClass('suggest-result'))
	                $('.suggest-result').css('display','none').find('ul').empty();
	        });
		});
		// Lấy tổng giá
		function setTotalPrice() {
			total_price = 0;
			$.each($('tr[data-product_item]'), function() {
				quantity = $(this).find('.quantity').val();
				price = $(this).find('.price').val();
				total_price = total_price+(price*quantity);
			});
			$('#total_price').text(formatPrice(total_price));
			$('input[name=total_price]').val(total_price);
		}
		// Định dạng giá
		function formatPrice(number) {
		    number += '';
		    x = number.split('.');
		    x1 = x[0];
		    x2 = x.length > 1 ? '.' + x[1] : '';
		    var rgx = /(\d+)(\d{3})/;
		    while (rgx.test(x1)) {
		        x1 = x1.replace(rgx, '$1' + '.' + '$2');
		    }
		    number = x1 + x2 +"đ";
		    return number;
		}
	</script>
</div>