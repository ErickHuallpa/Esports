<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de Usuario Master - E-Sports S.R.L.</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
        .header { background-color: #343c4c; color: #fff; padding: 20px; text-align: center; border-bottom: 4px solid #dc043c; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px; color: #dcb47c; }
        .header p { margin: 5px 0 0 0; font-size: 12px; color: #ddd; }
        
        .content { padding: 30px; }
        h2 { color: #0464a4; font-size: 18px; text-transform: uppercase; border-bottom: 2px solid #ddd; padding-bottom: 5px; margin-top: 30px; }
        h3 { color: #343c4c; font-size: 14px; margin-top: 20px; text-transform: uppercase; }
        
        .box { background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .box-title { font-weight: bold; color: #dc043c; margin-bottom: 5px; font-size: 13px; text-transform: uppercase; }
        
        ul { margin-top: 5px; margin-bottom: 15px; padding-left: 20px; }
        li { margin-bottom: 5px; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 30px; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 5px; }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Manual de Usuario Master</h1>
        <p>Documentación técnica y operativa integral del sistema E-Sports</p>
    </div>

    <div class="footer">
        E-Sports S.R.L. - Todos los derechos reservados &copy; {{ date('Y') }}
    </div>

    <div class="content">
        <h2>1. Introducción al Sistema</h2>
        <p>Bienvenido al <strong>Manual de Usuario Master</strong>. Este sistema ha sido diseñado como una solución integral <strong>Omnicanal</strong> para la gestión de la tienda física y virtual <em>E-Sports</em>. Combina funcionalidades avanzadas de Punto de Venta (POS), Logística, Inventario, Reportes analíticos y E-Commerce.</p>
        <p>El sistema soporta múltiples perfiles de usuario:</p>
        <ul>
            <li><strong>Administrador:</strong> Acceso total, gestión de personal, inventario maestro, reportes, cupones y ofertas.</li>
            <li><strong>Cajero:</strong> Gestión del Punto de Venta, validación de pagos QR e historial de transacciones en tienda.</li>
            <li><strong>Personal de Logística:</strong> Ingreso de mercancía al almacén (Kardex) y actualización de estados de envíos.</li>
            <li><strong>Cliente:</strong> Compras online, uso de cupones, calificaciones y visualización del catálogo.</li>
        </ul>

        <h2>2. Módulo de Administración</h2>
        
        <div class="box">
            <div class="box-title">A. Gestión de Usuarios</div>
            <p>Permite la creación de cuentas para el personal interno (Cajeros y Logística). Al crearlos, sus cuentas ya quedan habilitadas. También se puede suspender a un usuario mediante el botón "Bloquear/Desbloquear".</p>
        </div>
        
        <div class="box">
            <div class="box-title">B. Inventario Maestro (Artículos)</div>
            <p>La creación de productos requiere asociar una <strong>Categoría</strong> y crear <strong>Variantes</strong> (Talla y Color). Opcionalmente se pueden subir múltiples imágenes.</p>
        </div>

        <div class="box">
            <div class="box-title">C. Cupones de Descuento</div>
            <p>Creación de códigos promocionales limitados por fecha y cantidad de usos. Estos cupones los puede canjear el cliente al finalizar su compra en la web.</p>
        </div>

        <div class="box">
            <div class="box-title">D. Ofertas Dinámicas</div>
            <p>Permite seleccionar productos específicos del inventario y aplicarles un porcentaje de descuento que será visible en el catálogo de manera automática y temporal.</p>
        </div>

        <div class="page-break"></div>

        <h2>3. Módulo de Cajero (Punto de Venta)</h2>
        <p>El cajero es responsable de procesar las ventas físicas y la validación de pagos digitales.</p>
        <ul>
            <li><strong>Sistema POS:</strong> Interfaz rápida para agregar artículos mediante escaneo (o buscador manual), calcular el total, emitir factura/recibo en PDF de 80mm e imprimir directamente.</li>
            <li><strong>Validación de Pagos QR:</strong> Panel interactivo que lista los comprobantes de pago enviados por los clientes online. El cajero verifica la transferencia y "Aprueba" el pago, lo que desencadena la orden de envío hacia logística.</li>
            <li><strong>Cierre de Caja (Ventas Históricas):</strong> Visualización completa de todas las transacciones procesadas.</li>
        </ul>

        <h2>4. Módulo de Logística (Almacén y Envíos)</h2>
        <p>Este módulo administra el flujo de la mercancía, tanto su ingreso de fábrica como su salida hacia el cliente final.</p>
        <div class="box">
            <div class="box-title">Ingreso de Almacén (Kardex)</div>
            <p>Todo aumento de stock debe procesarse mediante una <strong>Nota de Ingreso</strong>. El sistema requiere seleccionar el Proveedor, el Documento de Respaldo y añadir la cantidad a ingresar, lo cual afecta automáticamente el inventario maestro.</p>
        </div>
        <div class="box">
            <div class="box-title">Control de Despachos</div>
            <p>Una vez que la venta es pagada, pasa a la cola de Logística. El personal debe empaquetar el producto, proporcionar un <strong>Código de Seguimiento (Tracking)</strong> y actualizar el estado de "Pendiente" a "En Camino", o a "Listo para Recojo" en su defecto.</p>
        </div>

        <h2>5. Experiencia del Cliente (E-Commerce)</h2>
        <ul>
            <li><strong>Proceso de Compra:</strong> El cliente añade productos al carrito interactivo, aplica cupones si los tiene, y procesa el Checkout. Puede elegir entre envío a domicilio o retiro en sucursal.</li>
            <li><strong>Confirmación de Pago (QR):</strong> Si opta por QR, el sistema retiene los productos y le pide adjuntar una captura del depósito. Este proceso se gestiona luego por caja.</li>
            <li><strong>Marcar como Recibido:</strong> Cuando el producto llega físicamente a su destino, el cliente puede ingresar a su panel de "Mis Pedidos" y confirmarlo mediante un botón de recepción segura.</li>
            <li><strong>Sistema de Reseñas:</strong> Tras la recepción del pedido, se habilita automáticamente un botón para calificar la compra con un sistema de 1 a 5 estrellas.</li>
        </ul>

        <h2>6. Analítica y Reportes</h2>
        <p>Panel de Business Intelligence dedicado a la toma de decisiones:</p>
        <ul>
            <li><strong>Dashboard:</strong> Resumen financiero mensual y productos de alto impacto.</li>
            <li><strong>Gráficos Interactivos:</strong> Visualización de ventas a través del tiempo y tortas de porcentaje por categoría de producto.</li>
            <li><strong>Alertas de Bajo Stock:</strong> Identificación inmediata de productos que requieren un pronto abastecimiento en almacén.</li>
            <li><strong>Exportación:</strong> Generación de reportes tabulares y contables tanto en formato Excel como PDF corporativo.</li>
        </ul>

    </div>
</body>
</html>
