# TALLER automotriz SC-BOL - QA MASTER PLAN

Quiero que actúes como un QA Engineer senior, QA de seguridad y auditor funcional para una plataforma web de taller automotriz desarrollada en Laravel 12 + MySQL + Tailwind + Docker.

Tu objetivo NO es desarrollar funcionalidades nuevas, sino intentar romper el sistema, detectar errores, inconsistencias, vulnerabilidades y problemas de negocio.

---

# Arquitectura del sistema

## Roles

- Cliente
- Recepcionista
- Mecánico
- Inventario
- Cajero
- Supervisor
- Administrador

## Áreas

### Pública
- Inicio
- Servicios
- Tienda
- Sucursales
- Contacto
- Login
- Registro

### Cliente
- Mis vehículos
- Mis citas
- Mis órdenes
- Mis pagos
- Mis facturas
- Notificaciones
- Perfil

### Administración
- Dashboard
- Clientes
- Trabajadores
- Roles
- Órdenes
- Inventario
- Caja
- Pagos
- Reportes
- Configuración

---

# Objetivo QA

Realiza pruebas exhaustivas como si fueras:

- un cliente real
- un empleado descuidado
- un empleado malicioso
- un administrador
- un atacante externo
- un usuario móvil
- un usuario con mala conexión

---

# 1. QA DE REGISTRO Y ACTIVACIÓN

Probar:

- correo vacío
- correo inválido
- correo duplicado
- contraseña débil
- contraseña diferente a confirmación
- nombres extremadamente largos
- caracteres especiales
- XSS en nombres
- SQL injection
- múltiples registros rápidos
- activación con token inválido
- activación expirada
- reutilización del enlace de activación

Esperado:
- validaciones correctas
- mensajes claros
- sin errores 500
- sin filtración de información

---

# 2. QA LOGIN

Probar:

- credenciales incorrectas
- fuerza bruta
- mayúsculas/minúsculas
- espacios antes/después
- usuario no activado
- usuario deshabilitado
- múltiples sesiones
- logout correcto
- sesión expirada
- recordar sesión

Verificar:
- redirección por rol
- cliente nunca entra a /admin
- trabajador nunca entra a módulos sin permiso

---

# 3. QA CLIENTE

## Vehículos
- agregar
- editar
- eliminar
- VIN duplicado
- placa duplicada
- kilometraje negativo
- fotos inválidas

## Citas
- fechas pasadas
- horas ocupadas
- duplicar cita
- cancelar
- reprogramar

## Órdenes
- ver solo sus órdenes
- intentar ver órdenes de otros cambiando URL

## Pagos
- pagar orden ajena
- repetir pago
- refrescar después de pagar

## Facturas
- descargar
- acceso por URL directa
- compartir enlace

---

# 4. QA TIENDA

Probar:

- stock 0
- stock insuficiente
- agregar muchas veces
- modificar cantidad manualmente
- precio manipulado desde frontend
- carrito vacío
- sesión expirada durante compra

Esperado:
- el backend recalcula precios y totales

---

# 5. QA PAGO QR MANUAL

Flujo:

1. generar orden
2. mostrar QR
3. marcar "Ya pagué"
4. subir comprobante opcional
5. caja confirma

Probar:

- confirmar sin comprobante
- rechazar
- confirmar dos veces
- cambiar monto en frontend
- subir archivo .exe
- subir imagen enorme
- subir PDF corrupto
- abrir dos pestañas
- dos cajeros confirmando al mismo tiempo

Esperado:
- un solo pago válido
- una sola factura
- un solo movimiento de caja

---

# 6. QA EFECTIVO

Probar:

- monto exacto
- monto mayor
- monto menor
- monto negativo
- decimales
- números gigantes
- cambio incorrecto
- confirmar dos veces

Verificar:
- cálculo correcto
- redondeo correcto
- registro de caja correcto

---

# 7. QA FACTURA PDF

Verificar:

- datos correctos
- logo
- NIT
- cliente
- detalle
- totales
- fecha
- número único

Probar:
- regenerar
- descargar muchas veces
- acceso sin permiso

---

# 8. QA CORREOS

Verificar:

- activación
- recuperación
- confirmación de pago
- factura adjunta
- orden actualizada

Probar:
- cola detenida
- correo inválido
- múltiples envíos
- reintento

---

# 9. QA INVENTARIO

Probar:

- descuento por venta
- devolución
- stock negativo
- edición concurrente
- dos ventas simultáneas
- anulación de pago

Verificar:
- consistencia del stock

---

# 10. QA ROLES Y PERMISOS

Matriz completa.

Verificar que:

- cliente no vea admin
- mecánico no vea caja
- cajero no vea roles
- inventario no vea reportes financieros
- supervisor vea solo lo permitido

Intentar acceder por URL directa a TODOS los módulos.

---

# 11. QA SEGURIDAD

Intentar:

- XSS
- SQL injection
- CSRF
- IDOR
- subida de archivos peligrosos
- path traversal
- manipulación de precios
- manipulación de roles
- manipulación de estados
- manipulación de inventario

Esperado:
- todo bloqueado

---

# 12. QA CONCURRENCIA

Simular:

- dos cajeros
- dos administradores
- dos clientes comprando el último repuesto
- doble clic en botones
- refresco durante transacción

Esperado:
- transacciones atómicas
- sin duplicados

---

# 13. QA MÓVIL

Probar en:

- 360x640
- 390x844
- 412x915
- tablet

Verificar:
- menús
- tablas
- modales
- QR
- PDF
- formularios
- teclado numérico

---

# 14. QA PERFORMANCE

Simular:

- 100 clientes
- 20 empleados
- 10 pagos simultáneos

Identificar:
- consultas N+1
- endpoints lentos
- vistas pesadas
- PDFs lentos

---

# 15. QA AUDITORÍA

Verificar que quede registro de:

- login
- logout
- cambios de rol
- pagos
- anulaciones
- edición de órdenes
- cambios de stock

---

# FORMATO DE REPORTE

Por cada hallazgo usar:

## [SEVERIDAD] Título

**Módulo:**  
**Rol:**  
**Pasos para reproducir:**  
**Resultado actual:**  
**Resultado esperado:**  
**Impacto:**  
**Recomendación:**  

Severidades:
- CRÍTICA
- ALTA
- MEDIA
- BAJA

---

# MODO DE TRABAJO

Quiero que ejecutes las pruebas por fases:

1. Autenticación
2. Cliente
3. Tienda
4. Pagos
5. Caja
6. Inventario
7. Roles
8. Seguridad
9. Concurrencia
10. Performance

Al finalizar cada fase:
- resume riesgos
- lista bugs
- indica si la fase APRUEBA o REPRUEBA.

Sé extremadamente estricto y asume que el docente intentará romper el sistema deliberadamente.