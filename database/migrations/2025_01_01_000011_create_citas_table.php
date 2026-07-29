<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->nullOnDelete();
            $table->foreignId('servicio_id')->nullable()->constrained('servicios')->nullOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('estado', 20)->default('pendiente'); // pendiente, confirmada, cancelada, completada
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['fecha', 'sucursal_id']);
            $table->index('cliente_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
