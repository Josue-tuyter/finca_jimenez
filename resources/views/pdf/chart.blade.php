<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Seguimiento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Reporte de Seguimiento de Cosecha</h1>
    
    <table>
        <thead>
            <tr>
                <th>Tamaño</th>
                <th>Humedad</th>
                <th>Enfermedad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->size }}</td>
                <td>{{ $record->humidity }}</td>
                <td>{{ $record->disease }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>