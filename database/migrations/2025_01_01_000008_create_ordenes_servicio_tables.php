<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('mecanico_id')->nullable()->constrained('mecanicos')->nullOnDelete();
            $table->timestamp('fecha_ingreso')->useCurrent();
            $table->timestamp('fecha_entrega_estimada')->nullable();
            $table->timestamp('fecha_entrega_real')->nullable();
            $table->string('estado', 30)->default('Recibido');
            $table->string('prioridad', 20)->default('Media');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('sucursal_id');
            $table->index('mecanico_id');
            $table->index('fecha_ingreso');
        });

        Schema::create('detalle_orden_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_servicio')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios');
            $table->unsignedSmallInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->string('estado', 20)->default('Pendiente');
            $table->text('observaciones')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_orden_servicio');
        Schema::dropIfExists('ordenes_servicio');
    }
};
