## Hướng dẫn sử dụng Sudo Order ##

**Giới thiệu:** Đây là package mẫu dùng để xử lý Giỏ hàng và đơn hàng và khách hàng.

### Cài đặt để sử dụng ###

- Package cần phải có base `sudo/core` và package `sudo/product` để có thể hoạt động không gây ra lỗi
- Để có thể sử dụng Package cần tải về và lưu trữ tại source Laravel 
- Buộc phải có package `sudo/product` để hoạt động. Package `sudo/product` cũng cần đc tải về và lưu trữ tại source Laravel (Nên để chung 2 package tại cùng 1 thư mục cho dễ quản lý)

### Cấu hình tại Menu ###

	[
    	'type' 				=> 'multiple',
    	'name' 				=> 'Đơn hàng',
		'icon' 				=> 'fas fa-shopping-cart',
		'childs' => [
			[
				'name' 		=> 'Danh sách',
				'route' 	=> 'admin.orders.index',
				'role' 		=> 'orders_index',
				'active' 	=> [ 'admin.orders.create', 'admin.orders.show', 'admin.orders.edit' ]
			],
			[
				'name' 		=> 'Khách hàng',
				'route' 	=> 'admin.customers.index',
				'role' 		=> 'customers_index',
				'active' 	=> [ 'admin.customers.create', 'admin.customers.show', 'admin.customers.edit' ]
			],
		]
    ],
 
- Vị trí cấu hình được đặt tại `config/SudoMenu.php`
- Để có thể hiển thị tại menu, chúng ta có thể đặt đoạn cấu hình trên tại `config('SudoMenu.menu')`

### Cấu hình tại Module ###
	
	'orders' => [
		'name' 			=> 'Đơn hàng',
		'permision' 	=> [
			[ 'type' => 'index', 'name' => 'Truy cập' ],
			[ 'type' => 'create', 'name' => 'Thêm' ],
			[ 'type' => 'edit', 'name' => 'Sửa' ],
			[ 'type' => 'restore', 'name' => 'Lấy lại' ],
			[ 'type' => 'delete', 'name' => 'Xóa' ],
		],
	],
	'customers' => [
		'name' 			=> 'Khách hàng',
		'permision' 	=> [
			[ 'type' => 'index', 'name' => 'Truy cập' ],
			[ 'type' => 'create', 'name' => 'Thêm' ],
			[ 'type' => 'edit', 'name' => 'Sửa' ],
			[ 'type' => 'restore', 'name' => 'Lấy lại' ],
			[ 'type' => 'delete', 'name' => 'Xóa' ],
		],
	],

- Vị trí cấu hình được đặt tại `config/SudoModule.php`
- Để có thể phân quyền, chúng ta có thể đặt đoạn cấu hình trên tại `config('SudoModule.modules')`
 
### Publish ###

Mặc định khi chạy lệnh `php artisan sudo/core` đã sinh luôn cho package này, nhưng có một vài trường hợp chỉ muốn tạo lại riêng cho package này thì sẽ chạy các hàm dưới đây:

* Khởi tạo chung theo core
	- Tạo assets và configs `php artisan vendor:publish --tag=sudo/core`
	- Chỉ tạo assets `php artisan vendor:publish --tag=sudo/core/assets`
	- Chỉ tạo configs `php artisan vendor:publish --tag=sudo/core/config`
* Khởi tạo riêng theo package
	- Tạo assets và configs `php artisan vendor:publish --tag=sudo/order`
	- Chỉ tạo assets `php artisan vendor:publish --tag=sudo/order/assets`
	- Chỉ tạo configs `php artisan vendor:publish --tag=sudo/order/config`

### Sử dụng ###
### Shopping Cart ### 
**Modal thêm vào giỏ hàng**
Để có thể sử dụng Shopping cart ở giao diện thì phải nhúng đoạn code dưới đây vào các trang có nút `Thêm vào giỏ hàng`
    
    @include('Order::shopping_carts.modal', [
		'shopping_cart_link' => '#',
		'coutinue_buy' => '/'
	])
	
Trong đó:
`shopping_cart_link`: Là đường dẫn đến trang giỏ hàng
`coutinue_buy`: Là đường dẫn đến trang muốn khách tiếp tục mua sản phẩm

**Nút thêm vào giỏ hàng**
Sau khi thêm Modal thì bây chúng ta có 2 cách để cấu hình nút `Thêm vào giỏ hàng` một cách đơn giản
C1: Thêm thuộc tính ngay tại nút. VD
    
    <a href="#" add_to_cart="{{product_id}}" add_to_cart_quantity="{{so_luong_muon_them}}">Thêm vào giỏ hàng</a>

Trong đó
`add_to_cart`: là thuộc tính bắt buộc phải có và có giá trị là ID của sản phẩm
`add_to_cart_quantity`: là số lượng muốn thêm cho sản phẩm tương ứng. Thuộc tính này không bắt buộc, nếu không có mặc định số lượng sẽ là 1

C2: Thêm function addToCart() vào thuộc tính onclick. VD:

    <a href="#" onclick="addToCart(event, '{{product_id}}', '{{input_so_luong}}')">Thêm vào giỏ hàng</a>
    
rong đó
`event`: là giá trị bắt buộc và giá trị cũng là `event` luôn không cần thay đổi
`{{product_id}}`: là ID của sản phẩm
`{{input_so_luong}}`: Là định danh của input số lượng dùng để xác định số lượng muốn thêm của sản phẩm tương ứng, có thể là tên class hoặc tên id. Giá trị này không bắt buộc nếu không có mặc định số lượng là 1.

**Giao diện giỏ hàng**
Sử dụng giao diện giỏ hàng chúng ta sẽ inlcude view dưới đây vào view của giao diện giỏ hàng

    @include('Order::shopping_carts.cart', [
		'link_checkout' => '#',
		'coutinue_buy' => '/'
	])
	
Trong đó
`link_checkout`: là Link đến trang đặt hàng
`coutinue_buy`: là Link tiếp tục mua

### Order ### 
**Giao diện đặt hàng**
Sử dụng giao diện đặt hàng chúng ta sẽ inlcude view dưới đây vào view của giao diện đặt hàng

    @include('Order::shopping_carts.checkout')
    
**Giao diện đặt hàng thành công**
Hãy sửa giao diện đặt hàng thành công kế thừa lại Blade Master để có thể lấy Header và Footer
