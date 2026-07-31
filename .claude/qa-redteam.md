# TALLER PRO — AUDITORÍA EXTREMA (QA + RED TEAM + FINANZAS)

Actúa como un equipo completo de:

- QA Engineer Senior
- Pentester Web
- Auditor Financiero
- Auditor Contable
- Auditor de Caja
- Especialista en Concurrencia
- Especialista en Fraude
- Especialista en Laravel Security
- Especialista en AWS/Docker
- Especialista en Integridad de Datos

Tu objetivo es intentar romper el sistema de todas las formas posibles y determinar el estado real del sistema.


---

# REGLA PRINCIPAL

No desarrolles funcionalidades nuevas.

Debes:

- iniciar sesión
- navegar por el sistema
- ejecutar pruebas destructivas
- intentar fraude
- intentar escalación de privilegios
- intentar corrupción contable
- intentar corrupción de inventario
- intentar acceso a datos ajenos
- intentar duplicar pagos
- intentar romper PDFs y correos
- intentar saturar el sistema

Quiero un diagnóstico realista como si fueras un auditor contratado para aprobar o rechazar la puesta en producción.

---

# FASE 1 — AUTENTICACIÓN

## Cliente

Probar:

- login correcto
- login incorrecto
- mayúsculas/minúsculas
- espacios
- múltiples sesiones
- logout
- sesión expirada
- recordar sesión

## Admin

Probar lo mismo.

Verificar:

- separación total de sesiones
- cliente nunca accede a /admin
- admin sí accede

---

# FASE 2 — ESCALACIÓN DE PRIVILEGIOS

Como cliente:

- entrar a /admin
- entrar a /admin/caja
- entrar a /admin/pagos
- entrar a /admin/reportes
- entrar a /admin/usuarios
- cambiar IDs en URLs
- cambiar parámetros GET/POST
- manipular formularios ocultos

Objetivo: obtener cualquier dato administrativo o financiero.

---

# FASE 3 — IDOR (DATOS AJENOS)

Como cliente:

- ver órdenes de otros
- ver pagos de otros
- ver facturas de otros
- descargar PDFs de otros
- descargar adjuntos de otros
- ver vehículos de otros
- ver citas de otros

Cambiar IDs manualmente.

---

# FASE 4 — FRAUDE DE PAGOS QR

Como cliente:

- crear orden
- marcar "Ya pagué" sin pagar
- subir comprobante falso
- subir imagen editada
- subir PDF falso
- subir archivo enorme
- subir extensión peligrosa
- reenviar la misma orden
- abrir dos pestañas
- intentar pagar dos veces
- refrescar durante el pago

Como admin/cajero:

- confirmar dos veces
- rechazar y luego confirmar
- confirmar desde dos sesiones simultáneas

Verificar:

- una sola factura
- un solo ingreso en caja
- un solo cambio de estado

---

# FASE 5 — AUDITORÍA FINANCIERA

Verificar:

- total de la orden
- total del pago
- total de la factura
- total en caja
- descuentos
- impuestos
- redondeos
- anulaciones
- devoluciones

Intentar:

- precios negativos
- cantidades negativas
- decimales extremos
- números gigantes
- manipulación desde frontend

Resultado esperado:
El backend debe recalcular TODO.

---

# FASE 6 — CAJA Y EFECTIVO

Probar:

- monto exacto
- monto mayor
- monto menor
- monto negativo
- doble clic en confirmar
- dos cajeros a la vez
- cierre de caja con pagos pendientes
- reapertura de caja

Verificar integridad del libro de caja.

---

# FASE 7 — INVENTARIO

Probar:

- vender sin stock
- vender stock insuficiente
- dos ventas simultáneas del último repuesto
- anular venta
- editar stock mientras hay venta
- stock negativo
- duplicar descuento de stock

Verificar consistencia final.

---

# FASE 8 — XSS

Intentar en todos los campos:

<script>alert(1)</script>
<img src=x onerror=alert(1)>
javascript:alert(1)

Verificar almacenamiento y renderizado.

---

# FASE 9 — SQL INJECTION

Intentar:

' OR 1=1 --
" OR 1=1 --
admin' --

En login, búsquedas, filtros y formularios.

---

# FASE 10 — CSRF

Verificar que formularios sensibles tengan protección CSRF:

- pagos
- caja
- usuarios
- roles
- inventario
- órdenes

---

# FASE 11 — SUBIDA DE ARCHIVOS

Intentar:

- .php
- .exe
- .js
- .svg con script
- zip bomb
- imagen de 50MB
- PDF corrupto

Verificar:

- validación
- almacenamiento
- ejecución remota
- acceso directo

---

# FASE 12 — TRÁFICO Y CARGA

Simular:

- 50 clientes
- 10 cajeros
- 5 administradores
- 20 pagos simultáneos
- 20 órdenes simultáneas

Identificar:

- endpoints lentos
- deadlocks
- timeouts
- duplicados
- corrupción de datos

---

# FASE 13 — CONCURRENCIA CRÍTICA

Escenarios:

## A
Dos cajeros confirman el mismo pago.

## B
Dos clientes compran el último repuesto.

## C
Admin edita precio mientras cliente paga.

## D
Admin elimina cliente durante pago.

## E
Se genera factura mientras se anula la orden.

---

# FASE 14 — PDF Y CORREOS

Verificar:

- datos correctos
- numeración única
- no duplicados
- no acceso por URL
- adjuntos correctos
- correos repetidos

---

# FASE 15 — DOCKER / AWS

Revisar:

- APP_DEBUG=false
- logs expuestos
- .env accesible
- storage público
- permisos de archivos
- headers de seguridad
- rate limiting
- CORS
- HTTPS

---

# FASE 16 — AUDITORÍA DE ROLES

Construir matriz completa:

| Módulo | Cliente | Mecánico | Inventario | Cajero | Supervisor | Admin |
|-------|----------|-----------|-------------|--------|-------------|------|

Marcar cualquier permiso incorrecto.

---

# FASE 17 — RECUPERACIÓN DE CONTRASEÑA

Probar:

- usuario inexistente
- múltiples solicitudes
- token expirado
- reutilización de token
- enumeración de usuarios

---

# FASE 18 — PRIVACIDAD Y DATOS

Verificar exposición de:

- teléfonos
- direcciones
- correos
- documentos
- historial de vehículos
- historial financiero

---

# REPORTE FINAL OBLIGATORIO

Genera:

## 1. Resumen ejecutivo

## 2. Riesgos críticos

## 3. Riesgos altos

## 4. Riesgos medios

## 5. Riesgos bajos

## 6. Hallazgos financieros

## 7. Hallazgos contables

## 8. Hallazgos de seguridad

## 9. Hallazgos de concurrencia

## 10. Hallazgos de UX

## 11. Hallazgos móviles

## 12. Hallazgos de rendimiento

## 13. Evidencias (URLs, pasos y payloads)

## 14. Recomendaciones priorizadas

## 15. Estado final

- APTO PARA PRODUCCIÓN
- APTO PARA PRUEBAS CONTROLADAS
- NO APTO

---

# CRITERIO DE SEVERIDAD

- CRÍTICA: fraude, acceso a datos ajenos, escalación de privilegios, corrupción contable.
- ALTA: duplicados, pérdida de integridad, bypass de validaciones.
- MEDIA: errores funcionales importantes.
- BAJA: UX, textos, estilos.

---

# MODO RED TEAM

Piensa como:

- cliente malicioso
- empleado deshonesto
- cajero fraudulento
- administrador descuidado
- atacante externo
- bot automatizado

Sé extremadamente agresivo en las pruebas.