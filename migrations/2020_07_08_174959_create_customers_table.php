<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            // ID khách hàng
            $table->id();
            // Tên khách hàng
            $table->string('name')->nullable();
            // Điện thoại
            $table->string('phone')->nullable();
            // Email
            $table->string('email')->nullable();
            // Địa chỉ
            $table->string('address')->nullable();
            // Trạng thái
            $table->tinyInteger('status')->default(1);
            // Ngày tạo/cập nhật
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
