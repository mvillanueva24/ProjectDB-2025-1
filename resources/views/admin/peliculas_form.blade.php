@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $pelicula ? 'Editar Película' : 'Agregar Película' }}</h2>
    <form method="POST" action="{{ isset($pelicula) ? route('admin.peliculas.update', $pelicula->id_pelicula) : route('admin.peliculas.store') }}">
        @csrf
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="{{ $pelicula->titulo ?? '' }}">
        </div>
        <div class="form-group">
            <label for="director">Director</label>
            <select class="form-control" id="director" name="director">
                <option value="">Seleccione...</option>
                @foreach($directores as $director)
                    <option value="{{ $director->id_director }}" {{ (isset($pelicula) && $pelicula->id_director == $director->id_director) ? 'selected' : '' }}>{{ $director->nombre_director }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" id="estado" name="estado">
                <option value="">Seleccione...</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->id_estado }}" {{ (isset($pelicula) && $pelicula->id_estado == $estado->id_estado) ? 'selected' : '' }}>{{ $estado->nombre_estado_pelicula }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="clasificacion_edad">Clasificación Edad</label>
            <input type="text" class="form-control" id="clasificacion_edad" name="clasificacion_edad" value="{{ $pelicula->clasificacion_edad ?? '' }}">
        </div>
        <div class="form-group">
            <label for="duracion">Duración (minutos)</label>
            <input type="number" class="form-control" id="duracion" name="duracion" value="{{ $pelicula->duracion ?? '' }}">
        </div>
        <div class="form-group">
            <label for="fecha_estreno">Fecha Estreno</label>
            <input type="date" class="form-control" id="fecha_estreno" name="fecha_estreno" value="{{ $pelicula->fecha_estreno ?? '' }}">
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="2">{{ $pelicula->descripcion ?? '' }}</textarea>
        </div>
        <div class="form-group">
            <label for="sinopsis">Sinopsis</label>
            <textarea class="form-control" id="sinopsis" name="sinopsis" rows="3">{{ $pelicula->sinopsis ?? '' }}</textarea>
        </div>
        <div class="form-group">
            <label for="link_trailer">Link Trailer</label>
            <input type="url" class="form-control" id="link_trailer" name="link_trailer" value="{{ $pelicula->link_trailer ?? '' }}">
        </div>
        <div class="form-group">
            <label for="generos">Géneros</label>
            <select multiple class="form-control" id="generos" name="generos[]">
                @php
                    $generos_pelicula = collect($genero_pelicula)->where('id_pelicula', $pelicula->id_pelicula ?? null)->pluck('id_genero')->toArray();
                @endphp
                @foreach($generos as $genero)
                    <option value="{{ $genero->id_genero }}" {{ (isset($pelicula) && in_array($genero->id_genero, $generos_pelicula)) ? 'selected' : '' }}>{{ $genero->nombre_genero }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="reparto">Reparto</label>
            <select multiple class="form-control" id="reparto" name="reparto[]">
                @php
                    $reparto_pelicula = collect($repartos)->where('id_pelicula', $pelicula->id_pelicula ?? null)->pluck('id_miembro')->toArray();
                @endphp
                @foreach($miembros as $miembro)
                    <option value="{{ $miembro->id_miembro }}" {{ (isset($pelicula) && in_array($miembro->id_miembro, $reparto_pelicula)) ? 'selected' : '' }}>{{ $miembro->nombre_miembro }}</option>
                @endforeach
            </select>
        </div>
        <a href="{{ route('admin.peliculas') }}" class="btn btn-secondary">Volver</a>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>
@endsection 