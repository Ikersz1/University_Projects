<?php

namespace Database\Factories;

use App\Models\Jugador;
use Illuminate\Database\Eloquent\Factories\Factory;

class JugadorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $imagePath = 'images/jugadores/default.jpg';
        $imageFullPath = public_path($imagePath);

        // Verificar y crear la carpeta si no existe
        if (!file_exists(dirname($imageFullPath))) {
            mkdir(dirname($imageFullPath), 0777, true);
        }

        // Descargar una imagen común y guardarla localmente si no existe
        if (!file_exists($imageFullPath)) {
            $this->downloadImage($imageFullPath, 640, 480);
        }

        return [
            'nombre' => $this->faker->name,
            'posicion' => $this->faker->randomElement(['Delantero', 'Mediocampista', 'Defensor', 'Portero']),
            'nacionalidad' => $this->faker->country,
            'edad' => $this->faker->numberBetween(18, 40),
            'equipo_id' => $this->faker->numberBetween(1, 2), // Asegúrate de que estos ID de equipo existen o ajusta según tus datos
            'foto' => $imagePath,
            'biografia' => $this->faker->text
        ];
    }

    /**
     * Descargar una imagen común y guardarla localmente.
     *
     * @param string $path
     * @param int $width
     * @param int $height
     * @return void
     */
    private function downloadImage($path, $width, $height)
    {
        $imageUrl = $this->faker->imageUrl($width, $height, 'sports');

        try {
            file_put_contents($path, file_get_contents($imageUrl));
        } catch (\Exception $e) {
            throw new \Exception("No se pudo descargar la imagen.");
        }
    }
}
