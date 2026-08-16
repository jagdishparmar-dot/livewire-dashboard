<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('job_title')->nullable()->after('phone');
            $table->string('company')->nullable()->after('job_title');
            $table->string('location')->nullable()->after('company');
            $table->string('website')->nullable()->after('location');
            $table->date('date_of_birth')->nullable()->after('website');
            $table->text('bio')->nullable()->after('date_of_birth');
            $table->string('profile_photo_path')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'job_title',
                'company',
                'location',
                'website',
                'date_of_birth',
                'bio',
                'profile_photo_path',
            ]);
        });
    }
};
