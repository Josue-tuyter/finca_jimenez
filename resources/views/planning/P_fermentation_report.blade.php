<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: left;
            font-size: 14px;
            line-height: 1.5;
        }
        .logo {
            text-align: right;
        }
        .logo img {
            max-width: 90px;
            max-height: 90px;
        }
        .title {
            text-align: center;
            margin: 10px 0;
            font-size: 18px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <!-- Información de la empresa -->
            <td class="company-info">
                <strong>Finca Jimenez</strong><br>
                Correo: santiagorodrigoj@gmail.com<br>
                Dirección: El Triunfo, K48 vía Durán Tambo<br>
                Ruc: 0921084679001
            </td>
            <!-- Logo -->
            <td class="logo">
                <img src="{{ public_path('images/finca_logo.png') }}" alt="Finca Logo">
            </td>
        </tr>
    </table>
    <div class="title">
        <h2>Reporte de planificación de la fermentación</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Número de fermentación</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Trabajador</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->F_date_start }}</td>
                    <td>{{ $record->F_date_end   }}</td>
                    <td>{{ $record->worker->name ?? 'Sin asignar' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Generado automáticamente el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>

