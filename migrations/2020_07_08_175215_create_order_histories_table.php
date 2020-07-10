<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            // ID đơn hàng
            $table->integer('order_id');
            // ID quản trị viên thao tác (0 là đơn hàng đặt mới)
            $table->integer('admin_user_id');
            // Loại hành động ( admin_create Admin tạo đơn hàng | customer_create Khách tạo đơn | order_change sửa đơn | order_fail huỷ | order_success thành công | admin_note Ghi chú của admin)
            $table->string('type');
            // Dữ liệu hành động (Lưu dạng json) (Sửa đơn (order_change) và admin ghi chú (admin_note) sẽ có dữ liệu)
            $table->longtext('data')->nullable();
            // Ngày tạo log
            $table->timestamp('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_histories');
    }
}
