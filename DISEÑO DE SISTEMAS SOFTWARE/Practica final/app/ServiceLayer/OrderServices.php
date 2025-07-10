<?php

namespace App\ServiceLayer;

use App\Models\Jugador;
use App\Models\Est_jugador;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderServices
{
    public static function createPlayer($validatedData)
    {
        DB::beginTransaction();

        try {
            $jugador = Jugador::create($validatedData);

            DB::commit();
            return $jugador;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear jugador: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function updatePlayer($id, $validatedData, $estadisticas, $foto = null)
    {
        DB::beginTransaction();

        try {
            $jugador = Jugador::findOrFail($id);
            $jugador->update($validatedData);

            if ($foto) {
                $path = $foto->store('public/fotos');
                $jugador->foto = basename($path);
                $jugador->save();
            }

            $jugador->estadisticas()->updateOrCreate(['jugador_id' => $jugador->id], $estadisticas);

            DB::commit();
            return $jugador;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar jugador: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function deleteAllPlayers()
    {
        DB::beginTransaction();

        try {
            Est_jugador::query()->delete();
            Jugador::query()->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar todos los jugadores: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function getPlayersWithStatisticsAndTeams($request)
    {
        $query = Jugador::with(['estadisticas', 'equipo']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('nombre', 'LIKE', "%{$search}%")
                      ->orWhere('posicion', 'LIKE', "%{$search}%");
            });
        }

        $orderField = $request->input('orderField', 'nombre');
        $orderDirection = $request->input('orderDirection', 'asc');

        if (in_array($orderField, ['nombre', 'posicion', 'edad'])) {
            $query->orderBy($orderField, $orderDirection);
        } else if (in_array($orderField, ['goles', 'asistencias', 'amarillas', 'rojas'])) {
            $query->leftJoin('est_jugadors', 'jugadors.id', '=', 'est_jugadors.jugador_id')
                  ->select('jugadors.*', 'est_jugadors.goles', 'est_jugadors.asistencias', 'est_jugadors.amarillas', 'est_jugadors.rojas')
                  ->orderBy('est_jugadors.' . $orderField, $orderDirection);
        }

        return $query->paginate(10);
    }
}
