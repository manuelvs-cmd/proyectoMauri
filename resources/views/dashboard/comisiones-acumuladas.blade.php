@extends('layouts.app')

@section('title', 'Comisiones Acumuladas')

@section('content-class', 'full-width')

@section('content')
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Comisiones Acumuladas de {{ $fechaInicioCarbon->format('d/m/Y') }} a {{ $fechaFinCarbon->format('d/m/Y') }}</h1>
        </div>
        
        <div class="date-filter">
            <form method="GET" action="{{ route('dashboard.comisiones-acumuladas') }}" class="filter-form">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}" class="date-input">
                </div>
                <div class="form-group">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}" class="date-input">
                </div>
                <button type="submit" class="btn">Filtrar</button>
            </form>
        </div>

        <div class="estadisticas">
            @foreach ($comisionesAcumuladas as $vendedor)
                <div class="stat-card">
                    <h3>{{ $vendedor['vendedor_nombre'] }}</h3>
                    <p>Total Ventas: ${{ number_format($vendedor['total_ventas'], 2, ',', '.') }}</p>
                    <p>Pedidos: {{ $vendedor['total_pedidos'] }}</p>
                    <p>Comisión ({{ $vendedor['comision_porcentaje'] }}%): ${{ number_format($vendedor['comision_monto'], 2, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .date-filter {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .date-input {
            padding: 8px;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .estadisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .stat-card p {
            margin: 5px 0;
            color: #666;
        }
    </style>
@endsection
