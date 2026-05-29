<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago #{{ $venta->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #343c4c; margin: 0; padding: 0; }
        .container { width: 100%; margin: 0 auto; padding: 20px; }
        
        /* Tipografía */
        .company-name { font-size: 20px; font-weight: bold; color: #0464a4; margin-bottom: 5px; }
        .receipt-title { font-size: 24px; font-weight: bold; color: #343c4c; text-align: center; margin: 20px 0; text-transform: uppercase; letter-spacing: 2px;}
        .info-title { font-size: 10px; text-transform: uppercase; font-weight: bold; color: #0464a4; margin-bottom: 5px; }
        .info-text { font-size: 13px; margin: 3px 0; }
        
        /* Tablas Generales */
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-items th { background-color: #343c4c; color: white; padding: 12px; text-align: left; font-size: 11px; text-transform: uppercase; }
        .table-items td { padding: 12px; border-bottom: 1px solid #eeeeee; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Tabla de Totales (Alineada a la derecha) */
        .table-totals { width: 40%; float: right; margin-top: 10px; }
        .table-totals td { padding: 8px 0; border-bottom: 1px solid #eeeeee; border-top: none;}
        .table-totals tr:last-child td { border-bottom: none; border-top: 2px solid #343c4c; font-size: 16px; font-weight: bold; color: #dc043c; padding-top: 10px; }
        
        /* Pie de Página */
        .footer { clear: both; text-align: center; padding-top: 50px; font-size: 10px; color: #999; }
        
        /* Tablas de Estructura (Para evitar recortes de dompdf) */
        .layout-table { width: 100%; border: none; margin-bottom: 20px; }
        .layout-table td { border: none; padding: 0; vertical-align: top; }
    </style>
</head>
<body>
    <div class="container">
        <table class="layout-table" style="border-bottom: 3px solid #dcb47c; padding-bottom: 20px;">
            <tr>
                <td style="width: 50%;">
                    @if(file_exists(public_path('logo/logo.png')))
                        <img src="{{ public_path('logo/logo.png') }}" style="max-width: 150px;" alt="E-Sports Logo">
                    @else
                        <h2 style="color: #dc043c; margin:0;">E-SPORTS STORE</h2>
                    @endif
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="company-name">E-SPORTS BOLIVIA</div>
                    <div class="info-text">NIT: 123456789</div>
                    <div class="info-text">Av. Murillo #123, Potosí, Bolivia</div>
                    <div class="info-text">Tel: +591 60000000 | info@esports.com</div>
                </td>
            </tr>
        </table>

        <div class="receipt-title">Comprobante de Pago</div>

        <table class="layout-table">
            <tr>
                <td style="width: 48%; background: #f4f4f4; padding: 15px; border-radius: 5px;">
                    <div class="info-title">Datos del Cliente</div>
                    <div class="info-text"><strong>Señor(a):</strong> {{ $venta->user->persona->nombre }} {{ $venta->user->persona->apellidos }}</div>
                    <div class="info-text"><strong>CI/NIT:</strong> {{ $venta->user->persona->ci ?? 'S/N' }}</div>
                    <div class="info-text"><strong>Correo:</strong> {{ $venta->user->email }}</div>
                </td>
                <td style="width: 4%;"></td> <td style="width: 48%; background: #f4f4f4; padding: 15px; border-radius: 5px; text-align: right;">
                    <div class="info-title">Detalles de la Transacción</div>
                    <div class="info-text"><strong>Nro. Orden:</strong> #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="info-text"><strong>Fecha:</strong> {{ $venta->fecha_venta->format('d/m/Y H:i') }}</div>
                    <div class="info-text"><strong>Método de Pago:</strong> {{ $venta->pago->tipoPago->nombre }}</div>
                    <div class="info-text"><strong>Estado:</strong> Pagado y Verificado</div>
                </td>
            </tr>
        </table>

        <table class="table-items">
            <thead>
                <tr>
                    <th style="width: 10%;" class="text-center">Cant.</th>
                    <th style="width: 50%;">Descripción del Artículo</th>
                    <th style="width: 20%;" class="text-right">Precio Unit.</th>
                    <th style="width: 20%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $det)
                    <tr>
                        <td class="text-center">{{ $det->cantidad }}</td>
                        <td>
                            <strong>{{ $det->variante->producto->nombre }}</strong><br>
                            <span style="font-size:10px; color:#666;">
                                @if($det->variante->talla) Talla: {{ $det->variante->talla }} @endif
                                @if($det->variante->color) | Color: {{ $det->variante->color }} @endif
                            </span>
                        </td>
                        <td class="text-right">Bs {{ number_format($det->precio_unitario_venta, 2) }}</td>
                        <td class="text-right">Bs {{ number_format($det->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="table-totals">
            @if($venta->descuento_aplicado > 0)
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">Bs {{ number_format($venta->precio_total + $venta->descuento_aplicado, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Descuento:</strong></td>
                    <td class="text-right">- Bs {{ number_format($venta->descuento_aplicado, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td><strong>Total a Pagar:</strong></td>
                <td class="text-right">Bs {{ number_format($venta->precio_total, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Este documento es una representación impresa de un comprobante de pago digital generado por E-Sports Store.</p>
            <p><strong>¡Gracias por su preferencia y confianza!</strong></p>
        </div>
    </div>
</body>
</html>