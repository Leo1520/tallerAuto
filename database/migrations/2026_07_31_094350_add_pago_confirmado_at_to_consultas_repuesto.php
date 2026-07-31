<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas_repuesto', function (Blueprint $table) {
            // Trail de auditoría: cuándo se confirmó el pago de tienda.
            // Requerido para detectar doble confirmación y para auditorías financieras.
            $table->timestamp('pago_confirmado_at')->nullable()->after('pago_notas');
        });
    }

    public function down(): void
    {
        Schema::table('consultas_repuesto', function (Blueprint $table) {
            $table->dropColumn('pago_confirmado_at');
        });
    }
};
