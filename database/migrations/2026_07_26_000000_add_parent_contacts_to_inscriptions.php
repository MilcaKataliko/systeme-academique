<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('inscriptions', 'email_parent')) {
                $table->string('email_parent')->nullable()->after('statut');
            }
            if (!Schema::hasColumn('inscriptions', 'telephone_parent')) {
                $table->string('telephone_parent')->nullable()->after('email_parent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn(['email_parent', 'telephone_parent']);
        });
    }
};

