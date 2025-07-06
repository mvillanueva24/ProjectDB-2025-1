@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Reservar Película</h2>
    <form method="POST" action="{{ route('cliente.reservar.store') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pelicula">Película</label>
                    <select class="form-control" id="pelicula" name="pelicula" required>
                        <option value="">Seleccione...</option>
                        @foreach($peliculas as $pelicula)
                            <option value="{{ $pelicula->id_pelicula }}">{{ $pelicula->titulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="horario">Horario</label>
                    <select class="form-control" id="horario" name="horario" required>
                        <option value="">Seleccione...</option>
                        @foreach($horarios as $horario)
                            <option value="{{ $horario->id_horario }}">{{ $horario->fecha_funcion }} - Sala: {{ $horario->numero_sala }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="asientos">Asientos</label>
                    <select multiple class="form-control" id="asientos" name="asientos[]" required>
                        @foreach($asientos as $asiento)
                            <option value="{{ $asiento->id_asiento }}">{{ $asiento->ubicacion_asiento }} (Sala {{ $asiento->Sala_id_sala }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ticket">Tipo de Ticket</label>
                    <select class="form-control" id="ticket" name="ticket" required onchange="actualizarPrecio()">
                        <option value="">Seleccione...</option>
                        @foreach($tickets as $ticket)
                            <option value="{{ $ticket->id_ticket }}" data-precio="{{ $ticket->precio_ticket }}">{{ $ticket->tipo_ticket }} (S/ {{ $ticket->precio_ticket }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad_ticket">Cantidad de Tickets</label>
                    <input type="number" class="form-control" id="cantidad_ticket" name="cantidad_ticket" min="1" value="1" required onchange="actualizarPrecio()">
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="precio_total">Precio Total</label>
                    <input type="text" class="form-control" id="precio_total" name="precio_total" readonly>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Reservar</button>
    </form>
</div>
<script>
    function actualizarPrecio() {
        var ticket = document.getElementById('ticket');
        var cantidad = document.getElementById('cantidad_ticket').value;
        var precio = ticket.options[ticket.selectedIndex]?.getAttribute('data-precio') || 0;
        document.getElementById('precio_total').value = 'S/ ' + (precio * cantidad);
        // Guardar el precio en un input oculto si lo necesitas en el backend
        if(!document.getElementById('precio_ticket_hidden')){
            var input = document.createElement('input');
            input.type = 'hidden';
            input.id = 'precio_ticket_hidden';
            input.name = 'precio_ticket';
            document.forms[0].appendChild(input);
        }
        document.getElementById('precio_ticket_hidden').value = precio;
    }
</script>
@endsection 