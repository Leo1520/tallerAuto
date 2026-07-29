<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo', 20);       // 'Ingreso' | 'Egreso'
            $table->string('concepto', 255);
            $table->decimal('monto', 10, 2);
            $table->string('referencia', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tipo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
