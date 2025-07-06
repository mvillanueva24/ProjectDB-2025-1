@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Administración de Películas</h1>
    <a href="{{ route('admin.peliculas.form') }}" class="btn btn-primary" style="margin-bottom: 20px;">Agregar Película</a>
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
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar esta película?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 