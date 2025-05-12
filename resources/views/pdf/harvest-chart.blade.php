<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .chart-container {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }
        .chart-container img {
            max-width: 100%;
            height: auto;
        }
        .chart-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .chart-data th, .chart-data td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .chart-data th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>{{ $description }}</p>
    </div>
    
    <div class="chart-container">
        <img src="{{ public_path($chartImagePath) }}" alt="Gráfico de Cosechas">
    </div>
    
    <table class="chart-data">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Número de Cosechas</th>
                <th>Peso Total (Lb)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chartData['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $chartData['datasets'][0]['data'][$index] ?? 0 }}</td>
                    <td>{{ $chartData['datasets'][1]['data'][$index] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Generado el: {{ $generatedDate }}</p>
    </div>
</body>
</html>