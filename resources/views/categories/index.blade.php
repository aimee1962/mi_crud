@extends('layouts.app')

@section('title', 'Lista de Categorías')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">📂 Lista de Categorías</h1>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nueva Categoría
        </a>
    </div>

    <!-- Buscador -->
    <form action="{{ route('categorias.index') }}" method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="🔍 Buscar categorías..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
        </div>
        <input type="hidden" name="sort" value="{{ request('sort', 'id') }}">
        <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
    </form>

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
                @if($currentSort == 'id') <i class="fas fa-sort-{{ $currentDir == 'asc' ? 'up' : 'down' }}"></i> @endif
            </a>
        </th>
        <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => ($currentSort == 'name' && $currentDir == 'asc') ? 'desc' : 'asc']) }}" 
               class="text-white text-decoration-none">
                Nombre
                @if($currentSort == 'name') <i class="fas fa-sort-{{ $currentDir == 'asc' ? 'up' : 'down' }}"></i> @endif
            </a>
        </th>
        <th>Productos Asociados</th>
        <th class="text-center">Acciones</th>
    </tr>
</thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td><span class="badge bg-info">{{ $category->products()->count() }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('categorias.edit', $category->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('categorias.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No hay categorías registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $categories->links() }}
    </div>
@endsection