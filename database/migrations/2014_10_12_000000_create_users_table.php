<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('role')->default(1)->comment("0 => admin , 1 => company , 2 =. user");
            $table->string('location_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('added_by')->default(1);
            $table->string('image')->nullable();
            $table->string('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'role' => 0,
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
