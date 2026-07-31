<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            // Índice UNIQUE en referencia como red de seguridad contra doble registro.
            // En MySQL, UNIQUE sobre columna nullable permite múltiples NULLs —
            // solo impide duplicados cuando referencia es NOT NULL.
            // Las ventas de tienda usan "consulta_{id}" (único por definición).
            // Los pagos de órdenes usan NULL en referencia — no se ven afectados.
            $table->unique('referencia', 'unq_movimientos_referencia');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->dropUnique('unq_movimientos_referencia');
        });
    }
};
