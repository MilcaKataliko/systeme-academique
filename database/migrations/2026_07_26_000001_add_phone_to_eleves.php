<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (!Schema::hasColumn('eleves', 'telephone')) {
                $table->string('telephone')->nullable()->after('code_matricule');
            }
            if (!Schema::hasColumn('eleves', 'email')) {
                $table->string('email')->nullable()->after('telephone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropColumn(['telephone', 'email']);
        });
    }
};

