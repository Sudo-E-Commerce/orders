<td style="min-width: 130px; width: 130px;">{{getOrderCode($value->id)}}</td>
<td style="min-width: 200px;">
	@php
		$customer = $customers->where('id', $value->customer_id)->first();
	@endphp
	<p class="mb-1">{{ $customer->name ?? '' }}</p>
	<p class="mb-1"><strong><a href="tel:{{ $customer->phone ?? '' }}">{{ $customer->phone ?? '' }}</a></strong></p>
	<p class="mb-1"><a href="mailto:{{ $customer->email ?? '' }}">{{ $customer->email ?? '' }}</a></p>
	<p class="mb-1">{{ $customer->address ?? '' }}</p>
</td>
<td style="width: 100px;">{!! $value->getStatus()['status_label'] ?? '' !!}</td>
<td style="width: 110px;">{{$value->getTotalPrice()}}</td>
<td style="width: 350px;">{{$value->note}}</td>