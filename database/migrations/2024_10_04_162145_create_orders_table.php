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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();

            $table->foreignId('master_order_id')->constrained('master_orders')->cascadeOnDelete();
            $table->foreignId('size_id')->constrained('sizes')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->integer('quantity_requested');
            $table->integer('quantity_delivered_tailor')->nullable();//الكمية التي سلمها الخياط
            $table->integer('quantity_delivered_supervisor')->nullable();//الكمية التي استلمها المراقب
            $table->integer('remaining_quantity_admin')->nullable();// الكمية المتبقية على الخياط تضع تلقائيا
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
        Schema::dropIfExists('product_sizes');
    }
};
