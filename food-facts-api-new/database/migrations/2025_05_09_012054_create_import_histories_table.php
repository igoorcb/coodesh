<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('import_histories', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('products_imported');
            $table->timestamp('imported_at');
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_histories');
    }
};