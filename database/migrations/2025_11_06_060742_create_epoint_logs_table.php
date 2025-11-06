<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpointLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epoint_logs', function (Blueprint $table) {
            $table->id();
            $table->string('api_endpoint', 100)->index();
            $table->string('api_name', 150);
            $table->json('request_params');
            $table->text('request_data');
            $table->text('request_signature');
            $table->json('response_data');
            $table->integer('response_status_code');
            $table->string('transaction_id', 50)->nullable()->index();
            $table->string('order_id', 255)->nullable()->index();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('status', 50)->nullable()->index(); // success, failed, error, new
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->decimal('execution_time', 8, 3)->nullable(); // milliseconds
            $table->timestamps();

            // Indexes for searching
            $table->index('created_at');
            $table->index(['api_endpoint', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('epoint_logs');
    }
}
