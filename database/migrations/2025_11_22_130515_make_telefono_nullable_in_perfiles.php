<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // cliente
        Schema::table('perfil_cliente', function (Blueprint $table) {
            $table->string('telefono', 30)->nullable()->change();
        });

        // vendedor (si tienes esta tabla)
        Schema::table('perfil_vendedor', function (Blueprint $table) {
            $table->string('telefono', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Si quisieras volver atrás (no hace falta en tu TFG, pero por dejarlo correcto):
        Schema::table('perfil_cliente', function (Blueprint $table) {
            $table->string('telefono', 30)->nullable(false)->change();
        });

        Schema::table('perfil_vendedor', function (Blueprint $table) {
            $table->string('telefono', 30)->nullable(false)->change();
        });
    }
};
