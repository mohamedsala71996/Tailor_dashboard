<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_orders', function (Blueprint $table) { // شيت الاكسيل هيتضاف هنا
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('excel_sheet_id')->constrained('excel_sheets')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // tailor_id
            $table->foreignId('size_id')->constrained('sizes')->cascadeOnDelete();
            $table->integer('quantity_requested');
            $table->timestamps();

            $table->unique(['product_id', 'user_id', 'size_id','excel_sheet_id'], 'product_user_size_escel_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sizes');
    }
};
