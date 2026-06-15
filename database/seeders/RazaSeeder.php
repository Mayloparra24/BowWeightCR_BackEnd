<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Raza;
use Illuminate\Database\Seeder;

class RazaSeeder extends Seeder
{
    public function run(): void
    {
        $razas = [
            ['id' => 1, 'nombre_raza' => 'Brahman / Nelore', 'enfoque' => 'Carne', 'constante_peso' => 148.5, 'descripcion' => 'Alta densidad ósea y muscular. Cuerpos compactos y pesados.'],
            ['id' => 2, 'nombre_raza' => 'Angus / Brangus', 'enfoque' => 'Carne', 'constante_peso' => 152.0, 'descripcion' => 'Forma de "barril". Muchísima masa muscular empacada en un área lateral corta.'],
            ['id' => 3, 'nombre_raza' => 'Charolais', 'enfoque' => 'Carne', 'constante_peso' => 155.0, 'descripcion' => 'Animales sumamente voluminosos y de musculatura pesada (el valor más alto).'],
            ['id' => 4, 'nombre_raza' => 'Senepol', 'enfoque' => 'Carne', 'constante_peso' => 145.0, 'descripcion' => 'Muy densos y pesados, pero un poco más cilíndricos que el Brahman.'],
            ['id' => 5, 'nombre_raza' => 'Cruce Cebú (Comercial)', 'enfoque' => 'Doble Propósito', 'constante_peso' => 138.6, 'descripcion' => '(Ancla Calibrada) El estándar promedio en potreros de Guanacaste.'],
            ['id' => 6, 'nombre_raza' => 'Pardo Suizo / Simmental', 'enfoque' => 'Doble Propósito', 'constante_peso' => 135.0, 'descripcion' => 'Animales grandes y largos, con menos densidad muscular extrema que un Brahman.'],
            ['id' => 7, 'nombre_raza' => 'Raza Reyna (Criollo)', 'enfoque' => 'Doble Propósito', 'constante_peso' => 130.0, 'descripcion' => 'Genética criolla adaptada a Centroamérica. Densidad media, rústicos.'],
            ['id' => 8, 'nombre_raza' => 'Gyr', 'enfoque' => 'Leche', 'constante_peso' => 128.0, 'descripcion' => 'Vaca lechera pero con genética cebuina (joroba), lo que la hace un poco más densa que las lecheras europeas.'],
            ['id' => 9, 'nombre_raza' => 'Girolando (Gyr x Holstein)', 'enfoque' => 'Leche', 'constante_peso' => 124.0, 'descripcion' => 'El cruce lechero por excelencia en el trópico. Combina el tamaño de la Holstein con la rusticidad del Gyr. Menos músculo, más capacidad abdominal.'],
            ['id' => 10, 'nombre_raza' => 'Holstein', 'enfoque' => 'Leche', 'constante_peso' => 120.0, 'descripcion' => 'Vacas altas, huesudas, con enorme capacidad abdominal pero muy poca masa muscular.'],
            ['id' => 11, 'nombre_raza' => 'Jersey', 'enfoque' => 'Leche', 'constante_peso' => 115.0, 'descripcion' => 'Animales pequeños, ligeros y angulosos. La menor densidad de la tabla.'],
            ['id' => 12, 'nombre_raza' => 'Guzerat', 'enfoque' => 'Doble Propósito', 'constante_peso' => 142.0, 'descripcion' => 'Raza cebuina de gran porte, cuernos en forma de lira. Excelente adaptabilidad al calor y buena musculatura.'],
            ['id' => 13, 'nombre_raza' => 'Braford / Hereford', 'enfoque' => 'Carne', 'constante_peso' => 150.0, 'descripcion' => 'Alta musculatura y adaptabilidad. Forma compacta similar al Angus, pero con mayor resistencia térmica (Braford).'],
            ['id' => 14, 'nombre_raza' => 'Romosinuano', 'enfoque' => 'Carne', 'constante_peso' => 140.0, 'descripcion' => 'Criollo adaptado al trópico húmedo. Sin cuernos, alta fertilidad y conformación carnicera más densa que el Reyna.'],
            ['id' => 15, 'nombre_raza' => 'Sardo Negro', 'enfoque' => 'Doble Propósito', 'constante_peso' => 136.0, 'descripcion' => 'Cebú lechero/carnicero muy rústico. Estructura ósea fuerte y densidad media superior a las razas lecheras puras.'],
            ['id' => 16, 'nombre_raza' => 'Ayrshire', 'enfoque' => 'Leche', 'constante_peso' => 122.0, 'descripcion' => 'Raza lechera muy rústica, excelente para topografía difícil. Posee un poco más de conformación cárnica que la Jersey y Holstein.'],
        ];

        foreach ($razas as $raza) {
            Raza::updateOrCreate(
                ['id' => $raza['id']],
                $raza,
            );
        }
    }
}
