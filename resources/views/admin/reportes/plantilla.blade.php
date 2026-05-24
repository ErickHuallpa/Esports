<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $titulo }} - E-Sports Store</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
        font-size: 12px;
        line-height: 1.4;
        color: #333;
        padding: 20px;
    }

    /* Header */
    .header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid #2563eb;
    }

    .logo {
        font-size: 28px;
        font-weight: bold;
        color: #2563eb;
        margin-bottom: 5px;
    }

    .titulo {
        font-size: 22px;
        font-weight: bold;
        color: #1f2937;
        margin-top: 10px;
    }

    .subtitulo {
        font-size: 11px;
        color: #6b7280;
        margin-top: 5px;
    }

    .fecha {
        font-size: 10px;
        color: #9ca3af;
        margin-top: 5px;
    }

    /* Tablas */
    .tabla {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .tabla th {
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        padding: 10px 8px;
        text-align: left;
        font-weight: bold;
        font-size: 11px;
        color: #374151;
    }

    .tabla td {
        border: 1px solid #e5e7eb;
        padding: 8px;
        font-size: 10px;
        color: #4b5563;
    }

    .tabla .text-right {
        text-align: right;
    }

    .tabla .text-center {
        text-align: center;
    }

    /* Totales y resumen */
    .resumen {
        margin-top: 20px;
        padding: 15px;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 5px;
    }

    .resumen-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 11px;
    }

    .resumen-total {
        font-size: 14px;
        font-weight: bold;
        color: #2563eb;
        border-top: 2px solid #e5e7eb;
        padding-top: 8px;
        margin-top: 8px;
    }

    /* Footer */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 9px;
        color: #9ca3af;
        padding: 10px;
        border-top: 1px solid #e5e7eb;
        margin-top: 30px;
    }

    /* Badges y estados */
    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: bold;
    }

    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .badge-warning {
        background-color: #fed7aa;
        color: #92400e;
    }

    .badge-info {
        background-color: #dbeafe;
        color: #1e40af;
    }

    /* Gráficos simples */
    .progress-bar {
        background-color: #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        margin: 5px 0;
    }

    .progress-fill {
        background-color: #2563eb;
        height: 8px;
        border-radius: 10px;
    }

    /* Utilidades */
    .mb-20 {
        margin-bottom: 20px;
    }

    .mt-20 {
        margin-top: 20px;
    }

    .text-bold {
        font-weight: bold;
    }

    .text-primary {
        color: #2563eb;
    }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">⚡ E-SPORTS STORE</div>
        <div class="titulo">{{ $titulo }}</div>
        <div class="subtitulo">{{ $subtitulo ?? 'Reporte del Sistema' }}</div>
        <div class="fecha">Generado: {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <!-- Contenido principal -->
    <div class="contenido">
        {!! $contenidoHtml !!}
    </div>

    <!-- Footer -->
    <div class="footer">
        E-Sports Store - Potosí, Bolivia - Todos los derechos reservados
    </div>
</body>

</html>
