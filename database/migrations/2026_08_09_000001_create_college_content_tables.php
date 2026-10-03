<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{
Schema::create('programs',function(Blueprint $t){$t->id();$t->string('name')->unique();$t->string('slug')->unique();$t->string('duration');$t->text('description');$t->unsignedSmallInteger('sort_order')->default(0)->index();$t->boolean('is_active')->default(true)->index();$t->timestamps();});
Schema::create('faculty',function(Blueprint $t){$t->id();$t->string('name');$t->string('title');$t->string('qualification')->nullable();$t->string('image_path')->nullable();$t->unsignedSmallInteger('sort_order')->default(0)->index();$t->boolean('is_active')->default(true)->index();$t->timestamps();});
Schema::create('events',function(Blueprint $t){$t->id();$t->string('title');$t->string('slug')->unique();$t->text('excerpt');$t->date('event_date')->nullable()->index();$t->string('image_path')->nullable();$t->boolean('is_published')->default(true)->index();$t->timestamps();});
Schema::create('admission_milestones',function(Blueprint $t){$t->id();$t->string('title');$t->string('icon')->nullable();$t->unsignedSmallInteger('sort_order')->default(0)->index();$t->timestamps();});
Schema::create('inquiries',function(Blueprint $t){$t->id();$t->string('name');$t->string('email')->index();$t->string('phone')->nullable();$t->string('program');$t->string('status')->default('new')->index();$t->timestamps();});}
public function down():void{Schema::dropIfExists('inquiries');Schema::dropIfExists('admission_milestones');Schema::dropIfExists('events');Schema::dropIfExists('faculty');Schema::dropIfExists('programs');}};
