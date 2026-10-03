<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::create('contact_messages',function(Blueprint $t){$t->id();$t->string('name');$t->string('email')->index();$t->string('phone',40)->nullable();$t->string('subject');$t->text('message');$t->string('status',30)->default('new')->index();$t->timestamps();});}public function down():void{Schema::dropIfExists('contact_messages');}};
