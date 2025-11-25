<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('producto_id');
            $table->string('path');          // ruta en storage/public
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('producto_id')
                  ->references('id_producto')->on('productos')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_images');
    }
};
