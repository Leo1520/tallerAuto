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
        Schema::table('consultas_repuesto', function (Blueprint $table) {
            $table->string('token', 64)->nullable()->unique()->after('estado');
            $table->string('comprobante_path', 500)->nullable()->after('token');
            $table->string('pago_estado', 20)->nullable()->after('comprobante_path');
            $table->text('pago_notas')->nullable()->after('pago_estado');
        });
    }

    public function down(): void
    {
        Schema::table('consultas_repuesto', function (Blueprint $table) {
            $table->dropColumn(['token', 'comprobante_path', 'pago_estado', 'pago_notas']);
        });
    }
};
