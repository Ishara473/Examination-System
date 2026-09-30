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
        // First, delete duplicate records keeping only the first one per student/year/study_year combination
        DB::statement('
            DELETE y FROM student_yearly_registration x
            INNER JOIN student_yearly_registration y 
            ON x.student_id = y.student_id 
            AND x.academic_year = y.academic_year 
            AND x.registered_year = y.registered_year
            WHERE x.id < y.id
        ');

        // Add unique constraint to prevent future duplicates
        Schema::table('student_yearly_registration', function (Blueprint $table) {
            $table->unique(
                ['student_id', 'academic_year', 'registered_year'],
                'unique_student_year_registration'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_yearly_registration', function (Blueprint $table) {
            $table->dropUnique('unique_student_year_registration');
        });
    }
};
