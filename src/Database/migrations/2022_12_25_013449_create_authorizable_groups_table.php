<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Minigyima\Warden\Models\AuthorizableGroup;
use Minigyima\Warden\Services\Warden;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorizable_groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->longText('permissions');
        });

        if (config('warden.create_default_group')) {
            $group = new AuthorizableGroup();
            $group->name = 'Default (user)';
            $group->permissions = Warden::getAllScopedPermissions();
            $group->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authorizable_groups');
    }
};
