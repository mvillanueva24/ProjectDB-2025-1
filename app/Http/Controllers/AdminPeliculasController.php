<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
