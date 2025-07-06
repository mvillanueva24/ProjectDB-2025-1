@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Administración de Películas</h1>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('admin.peliculas.form') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Película
            </a>
        </div>
        <div class="col-md-6 text-right">
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" id="dropdownReportes" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-excel"></i> Exportar Reportes
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownReportes">
                    <h6 class="dropdown-header">Reportes de Funciones</h6>
                    <a class="dropdown-item" href="{{ route('admin.reportes.funciones') }}">
                        <i class="fas fa-calendar-alt"></i> Funciones de Películas
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.reportes.peliculas-por-cine') }}">
                        <i class="fas fa-building"></i> Películas por Cine
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Reportes de Asientos</h6>
                    <a class="dropdown-item" href="{{ route('admin.reportes.asientos') }}">
                        <i class="fas fa-chair"></i> Asientos Específicos
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Reportes por Género</h6>
                    <a class="dropdown-item" href="{{ route('admin.reportes.peliculas-por-genero') }}">
                        <i class="fas fa-film"></i> Películas por Género
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.reportes.generos-mas-reservados') }}">
                        <i class="fas fa-chart-line"></i> Géneros Más Reservados
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <table class="table table-striped" style="background: #fff;">
        <thead style="background: #007bff; color: #fff;">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Director</th>
                <th>Estado</th>
                <th>Fecha Estreno</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peliculas as $pelicula)
                <tr>
                    <td>{{ $pelicula->id_pelicula }}</td>
                    <td>{{ $pelicula->titulo }}</td>
                    <td>
                        @php
                            $dir = collect($directores)->firstWhere('id_director', $pelicula->id_director);
                        @endphp
                        {{ $dir ? $dir->nombre_director : '' }}
                    </td>
                    <td>
                        @php
                            $est = collect($estados)->firstWhere('id_estado', $pelicula->id_estado);
                        @endphp
                        {{ $est ? $est->nombre_estado_pelicula : '' }}
                    </td>
                    <td>{{ $pelicula->fecha_estreno }}</td>
                    <td>
                        <a href="{{ route('admin.peliculas.form', $pelicula->id_pelicula) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('admin.peliculas.delete', $pelicula->id_pelicula) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar esta película?')" disabled>Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Scripts de Bootstrap para el dropdown -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection 