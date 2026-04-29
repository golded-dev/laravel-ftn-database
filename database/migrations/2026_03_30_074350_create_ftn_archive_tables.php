<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('echoid')->nullable();
            $table->string('source_type')->nullable();
            $table->string('area_type')->nullable();
            $table->string('source_group_code')->nullable();
            $table->unsignedInteger('source_sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->unsignedInteger('msgno')->nullable();
            $table->string('source_type');
            $table->string('source_uid');
            $table->text('source_locator')->nullable();
            $table->unsignedBigInteger('source_offset')->nullable();
            $table->string('external_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_name')->nullable();
            $table->string('to_address')->nullable();
            $table->longText('body_text');
            $table->longText('body_raw')->nullable();
            $table->string('body_encoding')->nullable();
            $table->unsignedInteger('reply_to_msgno')->nullable();
            $table->string('reply_to_external_id')->nullable();
            $table->unsignedInteger('reply1st_msgno')->nullable();
            $table->unsignedInteger('replynext_msgno')->nullable();
            $table->unsignedInteger('attributes_raw')->default(0);
            $table->dateTime('posted_at')->nullable();
            $table->dateTime('arrived_at')->nullable();
            $table->longText('control_lines_json')->nullable();
            $table->longText('provenance_json')->nullable();
            $table->timestamps();

            $table->unique(['area_id', 'source_type', 'source_uid'], 'messages_area_source_unique');
            $table->index(['area_id', 'msgno'], 'messages_area_msgno_index');
            $table->unique(['area_id', 'external_id'], 'messages_area_external_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('areas');
    }
};
