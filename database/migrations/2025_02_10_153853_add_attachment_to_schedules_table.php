<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            // เพิ่มคอลัมน์ attachment สำหรับเก็บไฟล์ (เก็บเป็น string ที่เก็บ path ของไฟล์)
            $table->string('attachment')->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
