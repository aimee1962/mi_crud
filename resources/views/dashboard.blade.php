@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Productos</h5>
                    <h2 class="display-4">{{ $totalProducts }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Categorías</h5>
                    <h2 class="display-4">{{ $totalCategories }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Producto más caro</h5>
                    <h4>${{ $mostExpensive ? number_format($mostExpensive->price, 2) : 'N/A' }}</h4>
                    <small>{{ $mostExpensive->name ?? 'Sin productos' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Producto más barato</h5>
                    <h4>${{ $cheapest ? number_format($cheapest->price, 2) : 'N/A' }}</h4>
                    <small>{{ $cheapest->name ?? 'Sin productos' }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos Recientes -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-clock"></i> Últimos productos agregados
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentProducts as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No hay productos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- GRÁFICO SELECTIVO -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-chart-bar"></i> Resumen de Productos por Categoría (Haz clic en la leyenda para ocultar/mostrar)
    </div>
    <div class="card-body">
        <canvas id="myChart" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels), // Nombres de categorías
                datasets: [
                    {
                        label: 'Cantidad de Productos',
                        data: @json($chartCounts),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Stock Total',
                        data: @json($chartStocks),
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        onClick: function(e, legendItem, legend) {
                            // Alterna la visibilidad de la serie al hacer clic en la leyenda (efecto "selectivo")
                            const index = legendItem.datasetIndex;
                            const ci = legend.chart;
                            const meta = ci.getDatasetMeta(index);
                            meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
                            ci.update();
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection