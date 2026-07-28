<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->boolean('requiere_referencia')->default(false);
            $table->boolean('activo')->default(true);
            $table->decimal('comision', 5, 2)->nullable();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_servicio')->cascadeOnDelete();
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 10)->default('BOB');
            $table->string('estado', 20)->default('Pendiente');
            $table->string('referencia', 100)->nullable();
            $table->string('transaccion_externa', 255)->nullable()->unique();
            $table->boolean('webhook_verificado')->default(false);
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('orden_id');
        });

        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->unique()->constrained('ordenes_servicio')->cascadeOnDelete();
            $table->string('numero', 30)->unique();
            $table->timestamp('fecha_emision')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('iva', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('estado', 20)->default('Borrador');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_servicio')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre', 255)->nullable();
            $table->string('ruta', 500)->nullable();
            $table->string('tipo', 50)->nullable();
            $table->unsignedBigInteger('tamano')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjuntos');
        Schema::dropIfExists('facturas');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('metodos_pago');
    }
};
