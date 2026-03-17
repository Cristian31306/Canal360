<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla de categorías de minerales
        Schema::create('cat_minerales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Precargar Oro, Plata, Platino
        $now = now();
        $minerales = [
            ['nombre' => 'Oro', 'slug' => 'oro', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Plata', 'slug' => 'plata', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Platino', 'slug' => 'platino', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('cat_minerales')->insert($minerales);

        // 3. Crear tabla de valores
        Schema::create('precio_mineral_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('precio_mineral_id')->constrained('precio_minerales')->onDelete('cascade');
            $table->foreignId('cat_mineral_id')->constrained('cat_minerales')->onDelete('cascade');
            $table->decimal('precio', 12, 2);
            $table->timestamps();
        });

        // 4. Migrar datos existentes
        $preciosExistentes = DB::table('precio_minerales')->get();
        $oroId = DB::table('cat_minerales')->where('slug', 'oro')->value('id');
        $plataId = DB::table('cat_minerales')->where('slug', 'plata')->value('id');
        $platinoId = DB::table('cat_minerales')->where('slug', 'platino')->value('id');

        foreach ($preciosExistentes as $p) {
            if (isset($p->oro) && $p->oro > 0) {
                DB::table('precio_mineral_valores')->insert([
                    'precio_mineral_id' => $p->id,
                    'cat_mineral_id' => $oroId,
                    'precio' => $p->oro,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                ]);
            }
            if (isset($p->plata) && $p->plata > 0) {
                DB::table('precio_mineral_valores')->insert([
                    'precio_mineral_id' => $p->id,
                    'cat_mineral_id' => $plataId,
                    'precio' => $p->plata,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                ]);
            }
            if (isset($p->platino) && $p->platino > 0) {
                DB::table('precio_mineral_valores')->insert([
                    'precio_mineral_id' => $p->id,
                    'cat_mineral_id' => $platinoId,
                    'precio' => $p->platino,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                ]);
            }
        }

        // 5. Eliminar columnas viejas de precio_minerales
        Schema::table('precio_minerales', function (Blueprint $table) {
            $table->dropColumn(['oro', 'plata', 'platino']);
        });
    }

    public function down(): void
    {
        Schema::table('precio_minerales', function (Blueprint $table) {
            $table->decimal('oro', 12, 2)->default(0);
            $table->decimal('plata', 12, 2)->default(0);
            $table->decimal('platino', 12, 2)->default(0);
        });

        Schema::dropIfExists('precio_mineral_valores');
        Schema::dropIfExists('cat_minerales');
    }
};
