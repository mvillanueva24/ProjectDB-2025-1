@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header bg-success text-white">
            <h3>¡Reserva Exitosa!</h3>
        </div>
        <div class="card-body">
            <h4>Ticket de Reserva</h4>
            <p><strong>Reserva N°:</strong> {{ $reserva->id_reserva }}</p>
            <p><strong>Correo:</strong> {{ $reserva->email }}</p>
            <hr>
            <p><strong>Película:</strong> {{ $horario->titulo }}</p>
            <p><strong>Fecha y Hora:</strong> {{ $horario->fecha_funcion }}</p>
            <p><strong>Sala:</strong> {{ $horario->numero_sala }}</p>
            <p><strong>Asientos:</strong> 
                @foreach($asientos as $asiento)
                    <span class="badge badge-info">{{ $asiento->ubicacion_asiento }}</span>
                @endforeach
            </p>
            <hr>
            <p><strong>Tickets:</strong></p>
            <ul>
                @foreach($tickets as $ticket)
                    <li>{{ $ticket->tipo_ticket }} x {{ $ticket->cantidad_ticket }} (S/ {{ $ticket->precio_ticket }} c/u)</li>
                @endforeach
            </ul>
            <h5 class="mt-3">Total: <span class="text-success">S/ {{ $reserva->costo }}</span></h5>
        </div>
    </div>
    <a href="{{ route('cliente.reservar.form') }}" class="btn btn-primary mt-4">Hacer otra reserva</a>
</div>
@endsection 