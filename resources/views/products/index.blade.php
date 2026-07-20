@extends('layouts.app')

@section('title', 'Lista de Productos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">📦 Lista de Productos</h1>
        <div>
            <a href="{{ route('productos.export') }}" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> Exportar CSV
            </a>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Buscador -->
    <form action="{{ route('productos.index') }}" method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-8">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="🔍 Buscar por nombre..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        <!-- Mantener el orden actual en la búsqueda -->
        <input type="hidden" name="sort" value="{{ request('sort', 'id') }}">
        <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
    </form>

    <!-- TABLA DE PRODUCTOS -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    @php
                        $currentSort = request('sort', 'id');
                        $currentDir = request('direction', 'asc');
                    @endphp

                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => ($currentSort == 'id' && $currentDir == 'asc') ? 'desc' : 'asc']) }}" 
                           class="text-white text-decoration-none">
                            ID
                            @if($currentSort == 'id') 
                                <i class="fas fa-sort-{{ $currentDir == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Imagen</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => ($currentSort == 'name' && $currentDir == 'asc') ? 'desc' : 'asc']) }}" 
                           class="text-white text-decoration-none">
                            Nombre
                            @if($currentSort == 'name') 
                                <i class="fas fa-sort-{{ $currentDir == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Categoría</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => ($currentSort == 'price' && $currentDir == 'asc') ? 'desc' : 'asc']) }}" 
                           class="text-white text-decoration-none">
                            Precio
                            @if($currentSort == 'price') 
                                <i class="fas fa-sort-{{ $currentDir == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Stock</th>
                    <th>Descripción</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     width="50" height="50" 
                                     class="img-thumbnail product-thumbnail" 
                                     style="cursor: pointer;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#zoomModal"
                                     onclick="document.getElementById('zoomModalImage').src='{{ asset('storage/' . $product->image) }}'">
                            @else
                                <span class="text-muted">Sin imagen</span>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>
                            @if($product->category)
                                <span class="badge bg-info">{{ $product->category->name }}</span>
                            @else
                                <span class="text-muted">Sin categoría</span>
                            @endif
                        </td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ Str::limit($product->description, 30) }}</td>
                        <td class="text-center">
                            <a href="{{ route('productos.edit', $product->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('productos.destroy', $product->id) }}" 
                                  method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('¿Seguro?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN CLÁSICA DE LARAVEL (Solo "Anterior" y "Siguiente" + números) -->
    <div class="d-flex justify-content-center mt-4">
       <!-- PAGINACIÓN PERSONALIZADA SIN FLECHAS (Solo Anterior/Siguiente) -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div>
        @if ($products->onFirstPage())
            <span class="btn btn-outline-secondary btn-sm disabled">Anterior</span>
        @else
            <a href="{{ $products->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">Anterior</a>
        @endif
    </div>

    <div>
        <span class="text-muted small">
            Página {{ $products->currentPage() }} de {{ $products->lastPage() }}
        </span>
    </div>

    <div>
        @if ($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">Siguiente</a>
        @else
            <span class="btn btn-outline-secondary btn-sm disabled">Siguiente</span>
        @endif
    </div>
</div>
    </div>

    <!-- MODAL ZOOM DE IMAGEN -->
    <div class="modal fade" id="zoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🖼️ Vista previa de la imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="zoomModalImage" src="" alt="Zoom" class="img-fluid" style="max-height: 75vh; border-radius: 8px;">
                </div>
            </div>
        </div>
    </div>
@endsection