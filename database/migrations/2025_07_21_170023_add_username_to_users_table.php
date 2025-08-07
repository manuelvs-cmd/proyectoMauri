<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero agregar el campo sin el constraint unique
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });
        
        // Actualizar usuarios existentes con un username basado en su email
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            if (empty($user->username)) {
                $emailUsername = explode('@', $user->email)[0];
                $uniqueUsername = $emailUsername;
                $counter = 1;
                
                // Asegurar que el username sea único
                while (DB::table('users')->where('username', $uniqueUsername)->exists()) {
                    $uniqueUsername = $emailUsername . $counter;
                    $counter++;
                }
                
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => $uniqueUsername]);
            }
        }
        
        // Ahora hacer el campo unique y no nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->change();
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('email')->unique()->change();
        });
    }
};
