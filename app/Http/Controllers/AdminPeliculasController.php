<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AdminPeliculasController extends Controller
{
    private function getDbData()
    {
        $peliculas = DB::select('SELECT * FROM Pelicula');
        return $peliculas;
    }

    public function index()
    {
        $peliculas = DB::select('
            SELECT 
                p.id_pelicula,
                p.titulo,
                p.clasificacion_edad,
                p.duracion,
                p.fecha_estreno,
                p.descripcion,
                p.sinopsis,
                p.link_trailer,
                p.Director_id_director AS id_director,
                p.Estado_pelicula_id_estado AS id_estado,
                d.nombre_director,
                e.nombre_estado_pelicula
            FROM Pelicula p
            JOIN Director d ON p.Director_id_director = d.id_director
            JOIN Estado_pelicula e ON p.Estado_pelicula_id_estado = e.id_estado
            ORDER BY p.id_pelicula ASC
        ');

        $generos = DB::select('
            SELECT 
                gp.Pelicula_id_pelicula,
                GROUP_CONCAT(g.nombre_genero) AS generos
            FROM Genero_pelicula gp
            JOIN Genero g ON gp.Genero_id_genero = g.id_genero
            GROUP BY gp.Pelicula_id_pelicula
        ');

        return view('admin.peliculas', [
            'peliculas' => $peliculas,
            'generos' => $generos,
            'directores' => DB::select('SELECT * FROM Director'),
            'estados' => DB::select('SELECT * FROM Estado_pelicula'),
            'genero_pelicula' => DB::select('SELECT * FROM Genero_pelicula'),
            'repartos' => DB::select('SELECT * FROM Reparto_pelicula'),
            'miembros' => DB::select('SELECT * FROM Miembro_reparto'),
        ]);
    }

    public function form($id = null)
    {
        $pelicula = null;
        if ($id) {
            $pelicula = DB::selectOne('
                SELECT 
                    id_pelicula,
                    titulo,
                    clasificacion_edad,
                    duracion,
                    fecha_estreno,
                    descripcion,
                    sinopsis,
                    link_trailer,
                    Director_id_director AS id_director,
                    Estado_pelicula_id_estado AS id_estado
                FROM Pelicula WHERE id_pelicula = ?
            ', [$id]);
        }
        return view('admin.peliculas_form', [
            'pelicula' => $pelicula,
            'directores' => DB::select('SELECT * FROM Director'),
            'estados' => DB::select('SELECT * FROM Estado_pelicula'),
            'generos' => DB::select('SELECT * FROM Genero'),
            'genero_pelicula' => DB::select('SELECT * FROM Genero_pelicula'),
            'repartos' => DB::select('SELECT * FROM Reparto_pelicula'),
            'miembros' => DB::select('SELECT * FROM Miembro_reparto'),
        ]);
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM Pelicula WHERE id_pelicula = ?', [$id]);
        return redirect()->route('admin.peliculas')->with('success', 'Película eliminada correctamente.');
    }

    public function store(Request $request)
    {
        // Obtener el siguiente id_pelicula
        $maxId = DB::selectOne('SELECT MAX(id_pelicula) as max_id FROM Pelicula');
        $nextId = ($maxId && $maxId->max_id) ? $maxId->max_id + 1 : 1;

        DB::insert('INSERT INTO Pelicula (id_pelicula, titulo, clasificacion_edad, duracion, fecha_estreno, descripcion, sinopsis, link_trailer, Director_id_director, Estado_pelicula_id_estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $nextId,
            $request->titulo,
            $request->clasificacion_edad,
            $request->duracion,
            $request->fecha_estreno,
            $request->descripcion,
            $request->sinopsis,
            $request->link_trailer,
            $request->director,
            $request->estado
        ]);
        // Aquí puedes agregar lógica para géneros y reparto si lo deseas
        return redirect()->route('admin.peliculas')->with('success', 'Película agregada correctamente.');
    }

    public function update(Request $request, $id)
    {
        DB::update('UPDATE Pelicula SET titulo = ?, clasificacion_edad = ?, duracion = ?, fecha_estreno = ?, descripcion = ?, sinopsis = ?, link_trailer = ?, Director_id_director = ?, Estado_pelicula_id_estado = ? WHERE id_pelicula = ?', [
            $request->titulo,
            $request->clasificacion_edad,
            $request->duracion,
            $request->fecha_estreno,
            $request->descripcion,
            $request->sinopsis,
            $request->link_trailer,
            $request->director,
            $request->estado,
            $id
        ]);
        // Aquí puedes agregar lógica para géneros y reparto si lo deseas
        return redirect()->route('admin.peliculas')->with('success', 'Película actualizada correctamente.');
    }

    /**
     * Exportar reporte de funciones de películas
     */
    public function exportarReporteFunciones()
    {
        $data = DB::select('
            SELECT 
                p.titulo AS nombre_pelicula,
                h.fecha_funcion,
                s.numero_sala,
                COUNT(DISTINCT r.id_reserva) AS cantidad_reservas,
                COUNT(DISTINCT ar.Asiento_id_asiento) AS asientos_reservados
            FROM Horario_funcion h
            JOIN Pelicula p ON h.Pelicula_id_pelicula = p.id_pelicula
            JOIN Sala s ON h.Sala_id_sala = s.id_sala
            LEFT JOIN Reserva r ON h.id_horario = r.Horario_funcion_id_horario
            LEFT JOIN Asiento_reserva ar ON r.id_reserva = ar.Reserva_id_reserva
            GROUP BY h.id_horario, p.titulo, h.fecha_funcion, s.numero_sala
            ORDER BY p.titulo, h.fecha_funcion
        ');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar título
        $sheet->setCellValue('A1', 'REPORTE DE FUNCIONES DE PELÍCULAS');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Configurar encabezados
        $headers = ['Película', 'Fecha y Hora', 'Sala', 'Cantidad de Reservas', 'Asientos Reservados'];
        $col = 'A';
        $row = 3;
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        
        // Agregar datos
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->nombre_pelicula);
            $sheet->setCellValue('B' . $row, $item->fecha_funcion);
            $sheet->setCellValue('C' . $row, $item->numero_sala);
            $sheet->setCellValue('D' . $row, $item->cantidad_reservas);
            $sheet->setCellValue('E' . $row, $item->asientos_reservados);
            $row++;
        }
        
        // Autoajustar columnas
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Agregar bordes
        $sheet->getStyle('A3:E' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Crear archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_funciones_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Exportar reporte de asientos específicos
     */
    public function exportarReporteAsientos()
    {
        $data = DB::select('
            SELECT 
                a.ubicacion_asiento,
                s.numero_sala,
                COUNT(ar.Reserva_id_reserva) AS veces_reservado,
                GROUP_CONCAT(DISTINCT p.titulo ORDER BY p.titulo SEPARATOR ", ") AS peliculas
            FROM Asiento a
            JOIN Sala s ON a.Sala_id_sala = s.id_sala
            LEFT JOIN Asiento_reserva ar ON a.id_asiento = ar.Asiento_id_asiento
            LEFT JOIN Reserva r ON ar.Reserva_id_reserva = r.id_reserva
            LEFT JOIN Horario_funcion h ON r.Horario_funcion_id_horario = h.id_horario
            LEFT JOIN Pelicula p ON h.Pelicula_id_pelicula = p.id_pelicula
            GROUP BY a.id_asiento, a.ubicacion_asiento, s.numero_sala
            ORDER BY s.numero_sala, a.ubicacion_asiento
        ');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar título
        $sheet->setCellValue('A1', 'REPORTE DE ASIENTOS ESPECÍFICOS');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Configurar encabezados
        $headers = ['Asiento', 'Sala', 'Veces Reservado', 'Películas'];
        $col = 'A';
        $row = 3;
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        
        // Agregar datos
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->ubicacion_asiento);
            $sheet->setCellValue('B' . $row, $item->numero_sala);
            $sheet->setCellValue('C' . $row, $item->veces_reservado);
            $sheet->setCellValue('D' . $row, $item->peliculas ?: 'Ninguna');
            $row++;
        }
        
        // Autoajustar columnas
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Agregar bordes
        $sheet->getStyle('A3:D' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Crear archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_asientos_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Exportar reporte de películas por cine
     */
    public function exportarReportePeliculasPorCine()
    {
        $data = DB::select('
            SELECT 
                c.nombre_cine,
                p.titulo AS nombre_pelicula,
                h.fecha_funcion,
                s.numero_sala
            FROM Horario_funcion h
            JOIN Pelicula p ON h.Pelicula_id_pelicula = p.id_pelicula
            JOIN Sala s ON h.Sala_id_sala = s.id_sala
            JOIN Cine c ON s.Cine_id_cine = c.id_cine
            ORDER BY c.nombre_cine, p.titulo, h.fecha_funcion
        ');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar título
        $sheet->setCellValue('A1', 'REPORTE DE PELÍCULAS POR CINE');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Configurar encabezados
        $headers = ['Cine', 'Película', 'Fecha y Hora', 'Sala'];
        $col = 'A';
        $row = 3;
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        
        // Agregar datos
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->nombre_cine);
            $sheet->setCellValue('B' . $row, $item->nombre_pelicula);
            $sheet->setCellValue('C' . $row, $item->fecha_funcion);
            $sheet->setCellValue('D' . $row, $item->numero_sala);
            $row++;
        }
        
        // Autoajustar columnas
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Agregar bordes
        $sheet->getStyle('A3:D' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Crear archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_peliculas_por_cine_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Exportar reporte de películas por género
     */
    public function exportarReportePeliculasPorGenero()
    {
        $data = DB::select('
            SELECT 
                g.nombre_genero,
                p.titulo AS nombre_pelicula,
                p.fecha_estreno,
                p.duracion,
                p.clasificacion_edad
            FROM Genero_pelicula gp
            JOIN Genero g ON gp.Genero_id_genero = g.id_genero
            JOIN Pelicula p ON gp.Pelicula_id_pelicula = p.id_pelicula
            ORDER BY g.nombre_genero, p.titulo
        ');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar título
        $sheet->setCellValue('A1', 'REPORTE DE PELÍCULAS POR GÉNERO');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Configurar encabezados
        $headers = ['Género', 'Película', 'Fecha Estreno', 'Duración (min)', 'Clasificación'];
        $col = 'A';
        $row = 3;
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        
        // Agregar datos
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->nombre_genero);
            $sheet->setCellValue('B' . $row, $item->nombre_pelicula);
            $sheet->setCellValue('C' . $row, $item->fecha_estreno);
            $sheet->setCellValue('D' . $row, $item->duracion);
            $sheet->setCellValue('E' . $row, $item->clasificacion_edad);
            $row++;
        }
        
        // Autoajustar columnas
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Agregar bordes
        $sheet->getStyle('A3:E' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Crear archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_peliculas_por_genero_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Exportar reporte de géneros con más reservas
     */
    public function exportarReporteGenerosMasReservados()
    {
        $data = DB::select('
            SELECT 
                g.nombre_genero,
                COUNT(DISTINCT r.id_reserva) AS total_reservas,
                COUNT(DISTINCT ar.Asiento_id_asiento) AS total_asientos_reservados,
                SUM(r.costo) AS ingresos_totales,
                COUNT(DISTINCT p.id_pelicula) AS cantidad_peliculas
            FROM Genero g
            LEFT JOIN Genero_pelicula gp ON g.id_genero = gp.Genero_id_genero
            LEFT JOIN Pelicula p ON gp.Pelicula_id_pelicula = p.id_pelicula
            LEFT JOIN Horario_funcion h ON p.id_pelicula = h.Pelicula_id_pelicula
            LEFT JOIN Reserva r ON h.id_horario = r.Horario_funcion_id_horario
            LEFT JOIN Asiento_reserva ar ON r.id_reserva = ar.Reserva_id_reserva
            GROUP BY g.id_genero, g.nombre_genero
            HAVING total_reservas > 0
            ORDER BY total_reservas DESC, ingresos_totales DESC
        ');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar título
        $sheet->setCellValue('A1', 'REPORTE DE GÉNEROS CON MÁS RESERVAS');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Configurar encabezados
        $headers = ['Género', 'Total Reservas', 'Asientos Reservados', 'Ingresos Totales (S/)', 'Cantidad Películas'];
        $col = 'A';
        $row = 3;
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        
        // Agregar datos
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->nombre_genero);
            $sheet->setCellValue('B' . $row, $item->total_reservas);
            $sheet->setCellValue('C' . $row, $item->total_asientos_reservados);
            $sheet->setCellValue('D' . $row, number_format($item->ingresos_totales, 2));
            $sheet->setCellValue('E' . $row, $item->cantidad_peliculas);
            $row++;
        }
        
        // Autoajustar columnas
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Agregar bordes
        $sheet->getStyle('A3:E' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Crear archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_generos_mas_reservados_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
