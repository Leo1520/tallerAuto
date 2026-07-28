<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 50)->nullable();
            $table->string('nit', 30)->nullable()->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('repuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->string('nombre', 100);
            $table->string('codigo', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->decimal('precio_venta', 10, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('inventario_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos')->cascadeOnDelete();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('stock_minimo')->default(0);
            $table->timestamp('updated_at')->nullable();

            $table->unique(['sucursal_id', 'repuesto_id']);
        });

        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repuesto_id')->constrained('repuestos');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo', 20);
            $table->integer('cantidad');
            $table->string('motivo', 100)->nullable();
            $table->string('referencia', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['repuesto_id', 'sucursal_id']);
        });

        Schema::create('detalle_orden_repuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_servicio')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos');
            $table->unsignedSmallInteger('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_orden_repuesto');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('inventario_sucursal');
        Schema::dropIfExists('repuestos');
        Schema::dropIfExists('proveedores');
    }
};
