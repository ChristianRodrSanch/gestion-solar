<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('perfil_vendedor', function (Blueprint $table) {

            // Campos extra del vendedor (opcionales para no romper datos existentes)
            $table->string('brand', 120)->nullable()->after('zona');
            $table->string('company', 150)->nullable()->after('brand');
            $table->string('website', 255)->nullable()->after('company');
            $table->text('message')->nullable()->after('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perfil_vendedor', function (Blueprint $table) {
            $table->dropColumn(['brand', 'company', 'website', 'message']);
        });
    }
};
