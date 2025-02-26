<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('color', 7)->default('#007bff')->after('status'); // Default เป็นสีน้ำเงิน
        });
    }

    public function down() {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};

