# CLAUDE CODE — CONTEXTO MAESTRO DEL PROYECTO TALLER PRO

## Objetivo
Desarrollar una plataforma web profesional para talleres automotrices usando Laravel, MySQL, Docker, AWS, Stripe y Leaflet.

## Arquitectura
- Laravel
- Blade + Tailwind
- MySQL
- Redis
- Docker Compose
- AWS EC2
- Stripe
- Leaflet + OpenStreetMap

## Reglas
- Seguridad primero
- No duplicar lógica
- Usar Form Requests
- Usar Policies
- Usar transacciones
- Evitar N+1

## Fases
1. Infraestructura
2. Seguridad
3. Base de datos
4. Clientes y vehículos
5. Órdenes de servicio
6. Inventario
7. Pagos
8. Mapa
9. Reportes
10. AWS

## Seguridad
- CSRF
- XSS
- SQL Injection
- Rate limiting
- Validación de archivos
- Webhooks verificados

## Rendimiento
- Eager loading
- Índices
- Cache
- Jobs
- Paginación

## Defensa
- APP_DEBUG=false
- HTTPS
- Backups
- Logs
- Tests principales

## Base de datos
// =============================================================
// TALLER PRO - BASE DE DATOS MEJORADA
// Laravel 12 + MySQL 8 + Docker + AWS + Stripe + Leaflet
// =============================================================

// =============================================================
// PERSONAS Y USUARIOS
// =============================================================

Table persona {
  id bigint [pk, increment]
  nombre varchar(100) [not null]
  telefono varchar(20)
  email varchar(100)
  activo boolean [default: true]
  created_at timestamp
  updated_at timestamp

  Indexes {
    email [unique]
  }
}

Table users {
  id bigint [pk, increment]
  persona_id bigint [ref: > persona.id, not null]
  email varchar(100) [not null]
  password varchar(255) [not null]
  email_verified_at timestamp
  remember_token varchar(100)
  ultimo_acceso timestamp
  created_at timestamp
  updated_at timestamp

  Indexes {
    email [unique]
    persona_id [unique]
  }
}

// =============================================================
// ROLES Y PERMISOS
// =============================================================

Table roles {
  id bigint [pk, increment]
  nombre varchar(50) [unique, not null]
  descripcion varchar(255)
  activo boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table permissions {
  id bigint [pk, increment]
  nombre varchar(100) [unique, not null]
  modulo varchar(50)
  accion varchar(50)
  created_at timestamp
  updated_at timestamp
}

Table role_user {
  role_id bigint [ref: > roles.id, not null]
  user_id bigint [ref: > users.id, not null]

  Indexes {
    (role_id, user_id) [pk]
  }
}

Table permission_role {
  permission_id bigint [ref: > permissions.id, not null]
  role_id bigint [ref: > roles.id, not null]

  Indexes {
    (permission_id, role_id) [pk]
  }
}

// =============================================================
// AUDITORÍA
// =============================================================

Table auditorias {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id]
  tipo_operacion varchar(50)
  tabla varchar(50)
  registro_id bigint
  cambios json
  ip varchar(45)
  user_agent varchar(255)
  created_at timestamp
}

// =============================================================
// SUCURSALES (MAPA)
// =============================================================

Table sucursales {
  id bigint [pk, increment]
  nombre varchar(100) [not null]
  direccion varchar(255)
  ciudad varchar(50)
  telefono varchar(20)
  email varchar(100)
  latitud decimal(10,8)
  longitud decimal(11,8)
  activo boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

// =============================================================
// CLIENTES
// =============================================================

Table clientes {
  id bigint [pk, increment]
  persona_id bigint [ref: > persona.id, not null]
  direccion varchar(255)
  ciudad varchar(50)
  tipo_documento varchar(20)
  numero_documento varchar(50)
  created_at timestamp
  updated_at timestamp

  Indexes {
    numero_documento [unique]
  }
}

// =============================================================
// VEHÍCULOS
// =============================================================

Table marcas {
  id bigint [pk, increment]
  nombre varchar(50) [unique, not null]
  pais varchar(50)
  activo boolean [default: true]
}

Table modelos {
  id bigint [pk, increment]
  marca_id bigint [ref: > marcas.id, not null]
  nombre varchar(50) [not null]
  activo boolean [default: true]

  Indexes {
    (marca_id, nombre) [unique]
  }
}

Table vehiculos {
  id bigint [pk, increment]
  cliente_id bigint [ref: > clientes.id, not null]
  modelo_id bigint [ref: > modelos.id, not null]
  placa varchar(10) [not null]
  vin varchar(17) [not null]
  ano int [not null]
  color varchar(30)
  kilometraje int [default: 0]
  activo boolean [default: true]
  created_at timestamp
  updated_at timestamp

  Indexes {
    placa [unique]
    vin [unique]
    cliente_id
  }
}

// =============================================================
// MANTENIMIENTO PREVENTIVO
// =============================================================

Table mantenimientos_preventivos {
  id bigint [pk, increment]
  vehiculo_id bigint [ref: > vehiculos.id, not null]
  tipo varchar(100)
  fecha_ultimo date
  proxima_fecha date
  kilometraje_ultimo int
  kilometraje_proximo int
  notificar boolean [default: true]
  estado varchar(20) [default: 'Proximo']
  observaciones text
  created_at timestamp
  updated_at timestamp
}

// =============================================================
// SERVICIOS
// =============================================================

Table tipos_servicio {
  id bigint [pk, increment]
  nombre varchar(50) [unique, not null]
  descripcion varchar(255)
  activo boolean [default: true]
}

Table servicios {
  id bigint [pk, increment]
  tipo_servicio_id bigint [ref: > tipos_servicio.id]
  nombre varchar(100) [not null]
  descripcion text
  precio decimal(10,2) [not null]
  tiempo_estimado int
  requiere_repuestos boolean [default: false]
  activo boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

// =============================================================
// MECÁNICOS
// =============================================================

Table especialidades {
  id bigint [pk, increment]
  nombre varchar(50) [unique, not null]
  descripcion varchar(255)
}

Table mecanicos {
  id bigint [pk, increment]
  persona_id bigint [ref: > persona.id, not null]
  sucursal_id bigint [ref: > sucursales.id]
  especialidad_id bigint [ref: > especialidades.id]
  cedula varchar(20) [not null]
  fecha_ingreso date
  salario decimal(10,2)
  activo boolean [default: true]
  created_at timestamp
  updated_at timestamp

  Indexes {
    cedula [unique]
    persona_id [unique]
  }
}

// =============================================================
// ÓRDENES DE SERVICIO
// =============================================================

Table ordenes_servicio {
  id bigint [pk, increment]
  numero varchar(20) [not null]
  vehiculo_id bigint [ref: > vehiculos.id, not null]
  sucursal_id bigint [ref: > sucursales.id]
  mecanico_id bigint [ref: > mecanicos.id]
  fecha_ingreso timestamp
  fecha_entrega_estimada timestamp
  fecha_entrega_real timestamp
  estado varchar(30) [default: 'Recibido']
  prioridad varchar(20) [default: 'Media']
  subtotal decimal(10,2) [default: 0]
  descuento decimal(10,2) [default: 0]
  impuestos decimal(10,2) [default: 0]
  total decimal(10,2) [default: 0]
  observaciones text
  created_at timestamp
  updated_at timestamp

  Indexes {
    numero [unique]
    estado
    sucursal_id
    mecanico_id
    fecha_ingreso
  }
}

Table detalle_orden_servicio {
  id bigint [pk, increment]
  orden_id bigint [ref: > ordenes_servicio.id, not null]
  servicio_id bigint [ref: > servicios.id, not null]
  cantidad int [default: 1]
  precio_unitario decimal(10,2) [not null]
  subtotal decimal(10,2)
  estado varchar(20) [default: 'Pendiente']
  observaciones text
}

// =============================================================
// INVENTARIO
// =============================================================

Table proveedores {
  id bigint [pk, increment]
  nombre varchar(100) [not null]
  telefono varchar(20)
  email varchar(100)
  direccion varchar(255)
  ciudad varchar(50)
  nit varchar(30)
  activo boolean [default: true]
  created_at timestamp
  updated_at timestamp

  Indexes {
    nombre [unique]
    nit [unique]
  }
}

Table repuestos {
  id bigint [pk, increment]
  proveedor_id bigint [ref: > proveedores.id]
  nombre varchar(100) [not null]
  codigo varchar(50) [not null]
  descripcion text
  precio_compra decimal(10,2)
  precio_venta decimal(10,2) [not null]
  activo boolean [default: true]
  created_at timestamp
  updated_at timestamp

  Indexes {
    codigo [unique]
  }
}

Table inventario_sucursal {
  id bigint [pk, increment]
  sucursal_id bigint [ref: > sucursales.id, not null]
  repuesto_id bigint [ref: > repuestos.id, not null]
  stock int [default: 0]
  stock_minimo int [default: 0]
  updated_at timestamp

  Indexes {
    (sucursal_id, repuesto_id) [unique]
  }
}

Table movimientos_inventario {
  id bigint [pk, increment]
  repuesto_id bigint [ref: > repuestos.id, not null]
  sucursal_id bigint [ref: > sucursales.id, not null]
  user_id bigint [ref: > users.id]
  tipo varchar(20) [not null]
  cantidad int [not null]
  motivo varchar(100)
  referencia varchar(50)
  created_at timestamp
}

// =============================================================
// REPUESTOS USADOS EN ORDEN (NUEVO)
// =============================================================

Table detalle_orden_repuesto {
  id bigint [pk, increment]
  orden_id bigint [ref: > ordenes_servicio.id, not null]
  repuesto_id bigint [ref: > repuestos.id, not null]
  cantidad int [not null]
  precio_unitario decimal(10,2) [not null]
  subtotal decimal(10,2)
}

// =============================================================
// PAGOS REALES
// =============================================================

Table metodos_pago {
  id bigint [pk, increment]
  nombre varchar(50) [unique, not null]
  requiere_referencia boolean [default: false]
  activo boolean [default: true]
  comision decimal(5,2)
}

Table pagos {
  id bigint [pk, increment]
  orden_id bigint [ref: > ordenes_servicio.id, not null]
  metodo_pago_id bigint [ref: > metodos_pago.id, not null]
  user_id bigint [ref: > users.id]
  monto decimal(10,2) [not null]
  moneda varchar(10) [default: 'BOB']
  estado varchar(20) [default: 'Pendiente']
  referencia varchar(100)
  transaccion_externa varchar(255)
  webhook_verificado boolean [default: false]
  fecha_confirmacion timestamp
  observaciones text
  created_at timestamp
  updated_at timestamp

  Indexes {
    estado
    transaccion_externa [unique]
  }
}

// =============================================================
// FACTURAS
// =============================================================

Table facturas {
  id bigint [pk, increment]
  orden_id bigint [ref: > ordenes_servicio.id, not null]
  numero varchar(30) [not null]
  fecha_emision timestamp
  subtotal decimal(10,2)
  iva decimal(10,2)
  total decimal(10,2)
  estado varchar(20) [default: 'Borrador']
  observaciones text
  created_at timestamp
  updated_at timestamp

  Indexes {
    numero [unique]
    orden_id [unique]
  }
}

// =============================================================
// ARCHIVOS ADJUNTOS
// =============================================================

Table adjuntos {
  id bigint [pk, increment]
  orden_id bigint [ref: > ordenes_servicio.id, not null]
  user_id bigint [ref: > users.id]
  nombre varchar(255)
  ruta varchar(500)
  tipo varchar(50)
  tamano bigint
  created_at timestamp
}

name data base in MySQL-PhpMyAdmin: db_taller
correo con 2FA
- contraseñas de aplicación 'taller automotrices': dqzq lwnr orbn myup
usala para configurar el .env para correo emisor oficial del sistema

