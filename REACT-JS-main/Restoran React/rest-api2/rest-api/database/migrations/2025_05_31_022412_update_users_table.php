<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('kasir'); // admin, manager, kasir
            $table->string('status')->default('active'); // active, inactive
            $table->string('api_token', 60)->nullable()->unique();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'api_token', 'deleted_at']);
        });
    }
}
