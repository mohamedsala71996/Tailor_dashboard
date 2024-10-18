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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('track_number')->unique();
            // $table->boolean('completed')->default(false); // Corrected default value
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // tailor_id
            // $table->foreignId('master_order_id')->constrained('master_orders')->cascadeOnDelete(); // tailor_id
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); // tailor_id
            $table->foreignId('excel_sheet_id')->constrained('excel_sheets')->cascadeOnDelete();
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
