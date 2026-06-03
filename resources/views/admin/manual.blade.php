@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden mt-8 mb-16 border-t-4 border-[#dc043c]">
    <div class="bg-[#343c4c] px-8 py-10 flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
        <div class="relative z-10 text-center md:text-left">
            <h1 class="text-4xl md:text-5xl font-black text-[#dcb47c] uppercase tracking-wider mb-2">Manual de Usuario Master</h1>
            <p class="text-white/80 font-medium text-lg">Documentación técnica y operativa integral del sistema E-Sports</p>
        </div>
        <div class="relative z-10 mt-6 md:mt-0 flex flex-col gap-3">
            <a href="{{ route('admin.manual.descargar') }}" class="bg-[#dc043c] hover:bg-[#dcb47c] hover:text-[#343c4c] text-white px-6 py-3 rounded-xl font-black shadow-lg transition-colors flex items-center justify-center uppercase tracking-widest text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Descargar en PDF
            </a>
            <a href="{{ route('home') }}" class="bg-white/10 hover:bg-white/20 text-white px-6 py-2 rounded-xl font-bold transition-colors text-center text-xs uppercase tracking-widest border border-white/20">
                Volver al Sistema
            </a>
        </div>
        <svg class="absolute -bottom-24 -right-10 w-96 h-96 text-white/5 pointer-events-none" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.2l7.1 14.2H4.9L12 6.2z"></path></svg>
    </div>

    <div class="p-8 md:p-12 space-y-12 text-[#343c4c]">
        
        <!-- INTRODUCCIÓN -->
        <section>
            <h2 class="text-2xl font-black text-[#0464a4] uppercase border-b-2 border-gray-100 pb-3 mb-6">1. Introducción al Sistema</h2>
            <div class="prose max-w-none text-gray-600">
                <p>Bienvenido al <strong>Manual de Usuario Master</strong>. Este sistema ha sido diseñado como una solución integral <strong>Omnicanal</strong> para la gestión de la tienda física y virtual <em>E-Sports</em>. Combina funcionalidades avanzadas de Punto de Venta (POS), Logística, Inventario, Reportes analíticos y E-Commerce.</p>
                <p>El sistema soporta múltiples perfiles de usuario:</p>
                <ul class="list-disc pl-6 space-y-2 font-medium mt-3">
                    <li><strong>Administrador:</strong> Acceso total, gestión de personal, inventario maestro, reportes, cupones y ofertas.</li>
                    <li><strong>Cajero:</strong> Gestión del Punto de Venta, validación de pagos QR e historial de transacciones en tienda.</li>
                    <li><strong>Personal de Logística:</strong> Ingreso de mercancía al almacén (Kardex) y actualización de estados de envíos.</li>
                    <li><strong>Cliente:</strong> Compras online, uso de cupones, calificaciones y visualización del catálogo.</li>
                </ul>
            </div>
        </section>

        <!-- MÓDULO ADMINISTRADOR -->
        <section>
            <h2 class="text-2xl font-black text-[#dc043c] uppercase border-b-2 border-gray-100 pb-3 mb-6">2. Módulo de Administración</h2>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <h3 class="text-lg font-bold text-[#343c4c] mb-3 flex items-center"><span class="bg-[#343c4c] text-[#dcb47c] w-6 h-6 rounded-full inline-flex items-center justify-center mr-2 text-xs">A</span> Gestión de Usuarios</h3>
                    <p class="text-sm text-gray-600 mb-2">Permite la creación de cuentas para el personal interno (Cajeros y Logística). Al crearlos, sus cuentas ya quedan habilitadas. También se puede suspender a un usuario mediante el botón "Bloquear/Desbloquear".</p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <h3 class="text-lg font-bold text-[#343c4c] mb-3 flex items-center"><span class="bg-[#343c4c] text-[#dcb47c] w-6 h-6 rounded-full inline-flex items-center justify-center mr-2 text-xs">B</span> Inventario Maestro (Artículos)</h3>
                    <p class="text-sm text-gray-600 mb-2">La creación de productos requiere asociar una <strong>Categoría</strong> y crear <strong>Variantes</strong> (Talla y Color). Opcionalmente se pueden subir múltiples imágenes.</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <h3 class="text-lg font-bold text-[#343c4c] mb-3 flex items-center"><span class="bg-[#343c4c] text-[#dcb47c] w-6 h-6 rounded-full inline-flex items-center justify-center mr-2 text-xs">C</span> Cupones de Descuento</h3>
                    <p class="text-sm text-gray-600 mb-2">Creación de códigos promocionales limitados por fecha y cantidad de usos. Estos cupones los puede canjear el cliente al finalizar su compra en la web.</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <h3 class="text-lg font-bold text-[#343c4c] mb-3 flex items-center"><span class="bg-[#343c4c] text-[#dcb47c] w-6 h-6 rounded-full inline-flex items-center justify-center mr-2 text-xs">D</span> Ofertas Dinámicas</h3>
                    <p class="text-sm text-gray-600 mb-2">Permite seleccionar productos específicos del inventario y aplicarles un porcentaje de descuento que será visible en el catálogo de manera automática y temporal.</p>
                </div>
            </div>
        </section>

        <!-- MÓDULO CAJERO -->
        <section>
            <h2 class="text-2xl font-black text-[#dcb47c] uppercase border-b-2 border-gray-100 pb-3 mb-6">3. Módulo de Cajero (Punto de Venta)</h2>
            <div class="prose max-w-none text-gray-600">
                <p>El cajero es responsable de procesar las ventas físicas y la validación de pagos digitales.</p>
                <ul class="list-disc pl-6 space-y-3 font-medium mt-4">
                    <li><strong>Sistema POS:</strong> Interfaz rápida para agregar artículos mediante escaneo (o buscador manual), calcular el total, emitir factura/recibo en PDF de 80mm e imprimir directamente.</li>
                    <li><strong>Validación de Pagos QR:</strong> Panel interactivo que lista los comprobantes de pago enviados por los clientes online. El cajero verifica la transferencia y "Aprueba" el pago, lo que desencadena la orden de envío hacia logística.</li>
                    <li><strong>Cierre de Caja (Ventas Históricas):</strong> Visualización completa de todas las transacciones procesadas.</li>
                </ul>
            </div>
        </section>

        <!-- MÓDULO LOGÍSTICA -->
        <section>
            <h2 class="text-2xl font-black text-[#0464a4] uppercase border-b-2 border-gray-100 pb-3 mb-6">4. Módulo de Logística (Almacén y Envíos)</h2>
            <div class="prose max-w-none text-gray-600">
                <p>Este módulo administra el flujo de la mercancía, tanto su ingreso de fábrica como su salida hacia el cliente final.</p>
                <div class="mt-6 border-l-4 border-[#0464a4] pl-4">
                    <h4 class="font-black text-[#343c4c] mb-1">Ingreso de Almacén (Kardex)</h4>
                    <p class="text-sm">Todo aumento de stock debe procesarse mediante una <strong>Nota de Ingreso</strong>. El sistema requiere seleccionar el Proveedor, el Documento de Respaldo y añadir la cantidad a ingresar, lo cual afecta automáticamente el inventario maestro.</p>
                </div>
                <div class="mt-4 border-l-4 border-[#0464a4] pl-4">
                    <h4 class="font-black text-[#343c4c] mb-1">Control de Despachos</h4>
                    <p class="text-sm">Una vez que la venta es pagada, pasa a la cola de Logística. El personal debe empaquetar el producto, proporcionar un <strong>Código de Seguimiento (Tracking)</strong> y actualizar el estado de "Pendiente" a "En Camino", o a "Listo para Recojo" en su defecto.</p>
                </div>
            </div>
        </section>

        <!-- MÓDULO CLIENTE E-COMMERCE -->
        <section>
            <h2 class="text-2xl font-black text-[#343c4c] uppercase border-b-2 border-gray-100 pb-3 mb-6">5. Experiencia del Cliente (E-Commerce)</h2>
            <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-600">
                <div>
                    <h4 class="font-bold text-[#dc043c] mb-2 uppercase tracking-wide">Proceso de Compra</h4>
                    <p>El cliente añade productos al carrito interactivo, aplica cupones si los tiene, y procesa el Checkout. Puede elegir entre envío a domicilio o retiro en sucursal.</p>
                </div>
                <div>
                    <h4 class="font-bold text-[#dc043c] mb-2 uppercase tracking-wide">Confirmación de Pago (QR)</h4>
                    <p>Si opta por QR, el sistema retiene los productos y le pide adjuntar una captura del depósito. Este proceso se gestiona luego por caja.</p>
                </div>
                <div>
                    <h4 class="font-bold text-[#dc043c] mb-2 uppercase tracking-wide">Marcar como Recibido</h4>
                    <p>Cuando el producto llega físicamente a su destino, el cliente puede ingresar a su panel de "Mis Pedidos" y confirmarlo mediante un botón de recepción segura.</p>
                </div>
                <div>
                    <h4 class="font-bold text-[#dc043c] mb-2 uppercase tracking-wide">Sistema de Reseñas</h4>
                    <p>Tras la recepción del pedido, se habilita automáticamente un botón para calificar la compra con un sistema de 1 a 5 estrellas.</p>
                </div>
            </div>
        </section>

        <!-- MÓDULO REPORTES -->
        <section>
            <h2 class="text-2xl font-black text-green-600 uppercase border-b-2 border-gray-100 pb-3 mb-6">6. Analítica y Reportes</h2>
            <div class="prose max-w-none text-gray-600">
                <p>Panel de Business Intelligence dedicado a la toma de decisiones:</p>
                <ul class="list-disc pl-6 space-y-2 mt-3">
                    <li><strong>Dashboard:</strong> Resumen financiero mensual y productos de alto impacto.</li>
                    <li><strong>Gráficos Interactivos:</strong> Visualización de ventas a través del tiempo y tortas de porcentaje por categoría de producto.</li>
                    <li><strong>Alertas de Bajo Stock:</strong> Identificación inmediata de productos que requieren un pronto abastecimiento en almacén.</li>
                    <li><strong>Exportación:</strong> Generación de reportes tabulares y contables tanto en formato Excel como PDF corporativo.</li>
                </ul>
            </div>
        </section>

        <!-- SOPORTE -->
        <div class="bg-[#f4f4f4] p-6 rounded-xl border border-gray-200 text-center mt-12">
            <h3 class="font-black text-[#343c4c] uppercase tracking-widest text-sm mb-2">Nota Técnica</h3>
            <p class="text-sm text-gray-600">La integridad referencial de este sistema está protegida. Las operaciones de eliminación de datos transaccionales se han deshabilitado en favor de anulaciones lógicas para mantener el registro contable impecable. En caso de emergencias sistémicas, refiérase al administrador de Base de Datos.</p>
        </div>
    </div>
</div>
@endsection
