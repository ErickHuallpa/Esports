## ⚙️ Configuración Inicial de la Base de Datos

Para que el sistema funcione correctamente, es estrictamente necesario inicializar los diccionarios de datos base antes de registrar al primer usuario administrador. 

Ejecuta las siguientes sentencias SQL directamente en tu gestor de base de datos PostgreSQL (como pgAdmin, DBeaver, o la consola psql) apuntando a la base de datos del proyecto:

### 1. Inicialización de Roles del Sistema
El sistema cuenta con un control de acceso basado en 4 roles definidos.

```sql
INSERT INTO roles (nombre, descripcion, created_at, updated_at) VALUES
('admin', 'Administrador del sistema con control total sobre catálogos, proveedores, roles y configuraciones.', NOW(), NOW()),
('personal', 'Encargado de la logística de rutas de envío, despachos y control de movimientos en el inventario.', NOW(), NOW()),
('cajero', 'Responsable de la validación manual de capturas de pago QR, registro de observaciones y estados de venta.', NOW(), NOW()),
('cliente', 'Usuario final con permisos para explorar el catálogo público, interactuar con modelos 3D y gestionar su carrito.', NOW(), NOW());


INSERT INTO tipo_pagos (nombre, descripcion, created_at, updated_at) VALUES
('QR', 'Pago electrónico mediante escaneo de código QR interbancario.', NOW(), NOW()),
('Efectivo', 'Pago presencial al momento de recoger los artículos en la tienda física.', NOW(), NOW());