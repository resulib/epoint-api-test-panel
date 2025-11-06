<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomKeysToEpointLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('epoint_logs', function (Blueprint $table) {
            $table->string('public_key_used', 50)->nullable()->after('api_name');
            $table->boolean('used_custom_keys')->default(false)->after('public_key_used');
        });
    }

    public function down()
    {
        Schema::table('epoint_logs', function (Blueprint $table) {
            $table->dropColumn(['public_key_used', 'used_custom_keys']);
        });
    }
}
