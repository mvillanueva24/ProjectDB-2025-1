<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteReservasController extends Controller
{
    public function form()
    {
        $peliculas = DB::select('SELECT p.id_pelicula, p.titulo FROM Pelicula p');
        $horarios = DB::select('SELECT h.id_horario, h.fecha_funcion, h.Pelicula_id_pelicula, s.numero_sala FROM Horario_funcion h JOIN Sala s ON h.Sala_id_sala = s.id_sala');
        $asientos = DB::select('SELECT a.id_asiento, a.ubicacion_asiento, a.Sala_id_sala FROM Asiento a');
        $tickets = DB::select('SELECT id_ticket, tipo_ticket, precio_ticket FROM Ticket');
        return view('cliente.reservar', [
            'peliculas' => $peliculas,
            'horarios' => $horarios,
            'asientos' => $asientos,
            'tickets' => $tickets,
        ]);
    }

    public function store(Request $request)
    {
        // Obtener el siguiente id_reserva
        $maxId = DB::selectOne('SELECT MAX(id_reserva) as max_id FROM Reserva');
        $nextId = ($maxId && $maxId->max_id) ? $maxId->max_id + 1 : 1;
        $fecha = now();
        $costo = $request->cantidad_ticket * $request->precio_ticket;
        // Insertar reserva
        DB::insert('INSERT INTO Reserva (id_reserva, costo, email, fecha_creacion, Horario_funcion_id_horario) VALUES (?, ?, ?, ?, ?)', [
            $nextId,
            $costo,
            $request->email,
            $fecha,
            $request->horario
        ]);
        // Insertar tickets
        DB::insert('INSERT INTO Reserva_ticket (Ticket_id_ticket, Reserva_id_reserva, cantidad_ticket) VALUES (?, ?, ?)', [
            $request->ticket,
            $nextId,
            $request->cantidad_ticket
        ]);
        // Insertar asientos
        foreach ($request->asientos as $asiento) {
            DB::insert('INSERT INTO Asiento_reserva (Reserva_id_reserva, Asiento_id_asiento) VALUES (?, ?)', [
                $nextId,
                $asiento
            ]);
        }
        return redirect()->route('cliente.reservar.ticket', $nextId);
    }

    public function ticket($id)
    {
        $reserva = DB::selectOne('SELECT * FROM Reserva WHERE id_reserva = ?', [$id]);
        $horario = DB::selectOne('SELECT h.*, p.titulo, s.numero_sala FROM Horario_funcion h JOIN Pelicula p ON h.Pelicula_id_pelicula = p.id_pelicula JOIN Sala s ON h.Sala_id_sala = s.id_sala WHERE h.id_horario = ?', [$reserva->Horario_funcion_id_horario]);
        $tickets = DB::select('SELECT t.tipo_ticket, t.precio_ticket, rt.cantidad_ticket FROM Reserva_ticket rt JOIN Ticket t ON rt.Ticket_id_ticket = t.id_ticket WHERE rt.Reserva_id_reserva = ?', [$id]);
        $asientos = DB::select('SELECT a.ubicacion_asiento FROM Asiento_reserva ar JOIN Asiento a ON ar.Asiento_id_asiento = a.id_asiento WHERE ar.Reserva_id_reserva = ?', [$id]);
        return view('cliente.ticket', [
            'reserva' => $reserva,
            'horario' => $horario,
            'tickets' => $tickets,
            'asientos' => $asientos,
        ]);
    }
} 