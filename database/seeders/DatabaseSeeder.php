<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Crear datos de ejemplo para los reportes
        $this->crearDatosEjemplo();
    }

    private function crearDatosEjemplo()
    {
        // Crear salas
        DB::insert('INSERT INTO Sala (id_sala, numero_sala, capacidad) VALUES (1, 1, 50), (2, 2, 40), (3, 3, 60) ON DUPLICATE KEY UPDATE numero_sala = VALUES(numero_sala)');

        // Crear horarios de función
        DB::insert('INSERT INTO Horario_funcion (id_horario, fecha_funcion, Pelicula_id_pelicula, Sala_id_sala) VALUES 
            (1, "2024-01-15 19:00:00", 1, 1),
            (2, "2024-01-15 21:30:00", 1, 1),
            (3, "2024-01-16 19:00:00", 2, 2),
            (4, "2024-01-16 21:30:00", 3, 3),
            (5, "2024-01-17 19:00:00", 4, 1),
            (6, "2024-01-17 21:30:00", 5, 2)
        ON DUPLICATE KEY UPDATE fecha_funcion = VALUES(fecha_funcion)');

        // Crear asientos
        $asientos = [];
        for ($sala = 1; $sala <= 3; $sala++) {
            $capacidad = $sala == 1 ? 50 : ($sala == 2 ? 40 : 60);
            for ($i = 1; $i <= $capacidad; $i++) {
                $asientos[] = "($i, 'A$i', $sala)";
            }
        }
        DB::insert('INSERT INTO Asiento (id_asiento, ubicacion_asiento, Sala_id_sala) VALUES ' . implode(', ', $asientos) . ' ON DUPLICATE KEY UPDATE ubicacion_asiento = VALUES(ubicacion_asiento)');

        // Crear tickets
        DB::insert('INSERT INTO Ticket (id_ticket, tipo_ticket, precio_ticket) VALUES 
            (1, "General", 12.00),
            (2, "Estudiante", 8.00),
            (3, "Niño", 6.00)
        ON DUPLICATE KEY UPDATE tipo_ticket = VALUES(tipo_ticket)');

        // Crear reservas
        DB::insert('INSERT INTO Reserva (id_reserva, costo, email, fecha_creacion, Horario_funcion_id_horario) VALUES 
            (1, 24.00, "cliente1@email.com", "2024-01-10 10:00:00", 1),
            (2, 16.00, "cliente2@email.com", "2024-01-10 11:00:00", 1),
            (3, 12.00, "cliente3@email.com", "2024-01-10 12:00:00", 2),
            (4, 24.00, "cliente4@email.com", "2024-01-10 13:00:00", 3),
            (5, 18.00, "cliente5@email.com", "2024-01-10 14:00:00", 4)
        ON DUPLICATE KEY UPDATE costo = VALUES(costo)');

        // Crear reserva_ticket
        DB::insert('INSERT INTO Reserva_ticket (Ticket_id_ticket, Reserva_id_reserva, cantidad_ticket) VALUES 
            (1, 1, 2),
            (2, 2, 2),
            (1, 3, 1),
            (1, 4, 2),
            (3, 5, 3)
        ON DUPLICATE KEY UPDATE cantidad_ticket = VALUES(cantidad_ticket)');

        // Crear asiento_reserva
        DB::insert('INSERT INTO Asiento_reserva (Reserva_id_reserva, Asiento_id_asiento) VALUES 
            (1, 1), (1, 2),
            (2, 3), (2, 4),
            (3, 51),
            (4, 101), (4, 102),
            (5, 41), (5, 42), (5, 43)
        ON DUPLICATE KEY UPDATE Reserva_id_reserva = VALUES(Reserva_id_reserva)');
    }
}
