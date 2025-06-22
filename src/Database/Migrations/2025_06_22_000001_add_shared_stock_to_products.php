<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Exécuter les migrations.
     */
    public function up(): void
    {
        Schema::table(config('lunar.database.table_prefix').'products', function (Blueprint $table): void {
            $table->boolean('shared_stock')
                  ->default(false)
                  ->after('status')
                  ->comment('Indicates whether the product uses shared stock between its variants');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::table(config('lunar.database.table_prefix').'products', function (Blueprint $table): void {
            $table->dropColumn(['shared_stock']);
        });
    }
};
