@extends('layouts.app')

@section('title', 'Lista de Categorías')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">📂 Lista de Categorías</h1>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nueva Categoría
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Buscador -->
    <form action="{{ route('categorias.index') }}" method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="🔍 Buscar categorías..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        <!-- Mantener el orden actual en la búsqueda -->
        <input type="hidden" name="sort" value="{{ request('sort', 'name') }}">
        <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
    </form>

    <!-- TABLA DE CATEGORÍAS (SIN ID) -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    @php
                        $currentSort = request('sort', 'name');
                        $currentDir = request('direction', 'asc');
                    @endphp

                    {{-- Nombre (ordenable) --}}
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => ($currentSort == 'name' && $currentDir == 'asc') ? 'desc' : 'asc']) }}"
                            class="text-white text-decoration-none">
                            Nombre
                            @if($currentSort == 'name')
                                <i class="fas fa-sort-{{ $currentDir == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>

                    {{-- Productos Asociados (ordenable) --}}
                    <th class="text-center">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'products_count', 'direction' => ($currentSort == 'products_count' && $currentDir == 'asc') ? 'desc' : 'asc']) }}"
                            class="text-white text-decoration-none">
                            Productos Asociados
                            @if($currentSort == 'products_count')
                                <i class="fas fa-sort-{{ $currentDir == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>

                    {{-- Acciones --}}
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        {{-- Nombre --}}
                        <td>{{ $category->name }}</td>

                        {{-- Conteo de productos --}}
                        <td class="text-center">
                            <span class="badge bg-info">{{ $category->products()->count() }}</span>
                        </td>

                        {{-- Acciones --}}
                        <td class="text-center">
                            <a href="{{ route('categorias.edit', $category->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('categorias.destroy', $category->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Seguro que quieres eliminar esta categoría?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No hay categorías registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN PERSONALIZADA (igual que en productos) -->
    <div class="d-flex justify-content-center mt-4">
        <div class="d-flex justify-content-between align-items-center" style="gap: 20px;">
            <div>
                @if ($categories->onFirstPage())
                    <span class="btn btn-outline-secondary btn-sm disabled">Anterior</span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">Anterior</a>
                @endif
            </div>
            <div>
                <span class="text-muted small">
                    Página {{ $categories->currentPage() }} de {{ $categories->lastPage() }}
                </span>
            </div>
            <div>
                @if ($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">Siguiente</a>
                @else
                    <span class="btn btn-outline-secondary btn-sm disabled">Siguiente</span>
                @endif
            </div>
        </div>
    </div>
@endsection