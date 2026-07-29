<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->cascadeOnDelete();
            $table->string('ruta', 500);
            $table->string('nombre_original', 255);
            $table->string('tipo_mime', 100)->nullable();
            $table->unsignedBigInteger('tamano')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes_pago');
    }
};
