<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Quién confirmó el pago (puede diferir de user_id = quien lo registró)
            $table->foreignId('confirmado_por_id')
                  ->nullable()
                  ->after('fecha_confirmacion')
                  ->constrained('users')
                  ->nullOnDelete();

            // IP desde donde se ejecutó la confirmación
            $table->string('confirmado_ip', 45)->nullable()->after('confirmado_por_id');

            // Canal de confirmación: 'QR-cajero' | 'Efectivo' | 'Manual'
            $table->string('metodo_confirmacion', 30)->nullable()->after('confirmado_ip');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['confirmado_por_id']);
            $table->dropColumn(['confirmado_por_id', 'confirmado_ip', 'metodo_confirmacion']);
        });
    }
};
