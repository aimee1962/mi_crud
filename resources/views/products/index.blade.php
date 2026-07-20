@extends('layouts.app')

@section('title', 'Lista de Productos')

@section('content')
    <h1 class="mb-4">📦 Lista de Productos</h1>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- BUSCADOR + ORDENAMIENTO DINÁMICO -->
    <form action="{{ route('productos.index') }}" method="GET" class="mb-3">
        <div class="row g-2">
            <!-- Búsqueda por nombre -->
            <div class="col-md-3">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="🔍 Buscar por nombre..." 
                       value="{{ request('search') }}">
            </div>

            <!-- Botón Buscar -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>

            <!-- Botón Limpiar -->
            <div class="col-md-2">
                <a href="{{ route('productos.index') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </div>

            <!-- Ordenar por campo -->
            <div class="col-md-2">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>Orden por ID</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Orden por Nombre</option>
                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Orden por Precio</option>
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Orden por Fecha</option>
                </select>
            </div>

            <!-- Ordenar dirección -->
            <div class="col-md-2">
                <select name="direction" class="form-select" onchange="this.form.submit()">
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>🔼 Ascendente</option>
                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>🔽 Descendente</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Botón Crear -->
    <a href="{{ route('productos.create') }}" class="btn btn-success mb-3">
        <i class="fas fa-plus-circle"></i> Crear Nuevo Producto
    </a>

    <!-- TABLA DE PRODUCTOS -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Descripción</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>

                        <!-- Columna de Imagen -->
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     width="50" 
                                     height="50" 
                                     class="img-thumbnail">
                            @else
                                <span class="text-muted">Sin imagen</span>
                            @endif
                        </td>

                        <td>{{ $product->name }}</td>

                        <!-- Columna de Categoría -->
                        <td>
                            @if($product->category)
                                <span class="badge bg-info">{{ $product->category->name }}</span>
                            @else
                                <span class="text-muted">Sin categoría</span>
                            @endif
                        </td>

                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->description }}</td>

                        <!-- Acciones -->
                        <td class="text-center">
                            <a href="{{ route('productos.edit', $product->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>

                            <form action="{{ route('productos.destroy', $product->id) }}" 
                                  method="POST" 
                                  style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('¿Seguro que quieres eliminar este producto?')">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

   <!-- PAGINACION -->    
<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination pagination-sm" style="margin-bottom: 0;">
            <!-- Botón Anterior -->
            @if ($products->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">&larr;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $products->previousPageUrl() }}" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">&larr;</a>
                </li>
            @endif

            <!-- Números de página -->
            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if ($page == $products->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background-color: #0d6efd; border-color: #0d6efd;">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            <!-- Botón Siguiente -->
            @if ($products->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $products->nextPageUrl() }}" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">&rarr;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">&rarr;</span>
                </li>
            @endif
        </ul>
    </nav>
 </div>   
</div> {{ $products->links() }}
</div>
@endsection