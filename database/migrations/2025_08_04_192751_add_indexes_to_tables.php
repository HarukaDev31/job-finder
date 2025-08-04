<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices para tabla users
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('email_verified_at');
            $table->index(['role', 'created_at']);
        });

        // Índices para tabla trabajos
        Schema::table('trabajos', function (Blueprint $table) {
            $table->index('activo');
            $table->index('sueldo');
            $table->index('created_at');
            $table->index(['activo', 'created_at']);
            $table->index(['activo', 'sueldo']);
            $table->fullText(['titulo', 'descripcion']);
        });

        // Índices para tabla postulantes
        Schema::table('postulantes', function (Blueprint $table) {
            $table->index('numero_documento');
            $table->index('tipo_documento');
            $table->index('fecha_nacimiento');
            $table->index(['nombres', 'apellidos']);
            $table->index(['tipo_documento', 'numero_documento']);
        });

        // Índices para tabla postulaciones
        Schema::table('postulaciones', function (Blueprint $table) {
            $table->index('trabajo_id');
            $table->index('postulante_id');
            $table->index('estado');
            $table->index('created_at');
            $table->index(['trabajo_id', 'estado']);
            $table->index(['postulante_id', 'estado']);
            $table->index(['estado', 'created_at']);
            $table->unique(['trabajo_id', 'postulante_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices de tabla users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['email_verified_at']);
            $table->dropIndex(['role', 'created_at']);
        });

        // Remover índices de tabla trabajos
        Schema::table('trabajos', function (Blueprint $table) {
            $table->dropIndex(['activo']);
            $table->dropIndex(['sueldo']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['activo', 'created_at']);
            $table->dropIndex(['activo', 'sueldo']);
            $table->dropFullText(['titulo', 'descripcion']);
        });

        // Remover índices de tabla postulantes
        Schema::table('postulantes', function (Blueprint $table) {
            $table->dropIndex(['numero_documento']);
            $table->dropIndex(['tipo_documento']);
            $table->dropIndex(['fecha_nacimiento']);
            $table->dropIndex(['nombres', 'apellidos']);
            $table->dropIndex(['tipo_documento', 'numero_documento']);
        });

        // Remover índices de tabla postulaciones
        Schema::table('postulaciones', function (Blueprint $table) {
            $table->dropIndex(['trabajo_id']);
            $table->dropIndex(['postulante_id']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['trabajo_id', 'estado']);
            $table->dropIndex(['postulante_id', 'estado']);
            $table->dropIndex(['estado', 'created_at']);
            $table->dropUnique(['trabajo_id', 'postulante_id']);
        });
    }
};
