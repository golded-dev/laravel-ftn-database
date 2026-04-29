<?php

declare(strict_types=1);

use Golded\Ftn\Database\Models\Area;
use Golded\Ftn\Database\Models\Message;
use Golded\Ftn\Database\Tests\Fixtures\Models\ConfiguredArea;
use Golded\Ftn\Database\Tests\Fixtures\Models\ConfiguredMessage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

it('creates the FTN archive tables', function (): void {
    expect(Schema::hasColumns('areas', [
        'id',
        'code',
        'name',
        'echoid',
        'source_type',
        'area_type',
        'source_group_code',
        'source_sort_order',
        'created_at',
        'updated_at',
    ]))->toBeTrue()
        ->and(Schema::hasColumns('messages', [
            'id',
            'area_id',
            'msgno',
            'source_type',
            'source_uid',
            'source_locator',
            'source_offset',
            'external_id',
            'subject',
            'from_name',
            'from_address',
            'to_name',
            'to_address',
            'body_text',
            'body_raw',
            'body_encoding',
            'reply_to_msgno',
            'reply_to_external_id',
            'reply1st_msgno',
            'replynext_msgno',
            'attributes_raw',
            'posted_at',
            'arrived_at',
            'control_lines_json',
            'provenance_json',
            'created_at',
            'updated_at',
        ]))->toBeTrue();
});

it('casts archive values on the base models', function (): void {
    $area = Area::create(areaRecord(['source_sort_order' => 7]));
    $message = Message::create(messageRecord($area, [
        'msgno' => 42,
        'source_offset' => 256,
        'attributes_raw' => 128,
        'posted_at' => '1999-03-18 12:34:56',
        'control_lines_json' => ['msgid' => '2:230/150 12345678'],
        'provenance_json' => ['sourceType' => 'jam', 'sourceOffset' => 256],
    ]))->refresh();

    expect($area->source_sort_order)->toBe(7)
        ->and($message->msgno)->toBe(42)
        ->and($message->source_offset)->toBe(256)
        ->and($message->attributes_raw)->toBe(128)
        ->and($message->posted_at?->year)->toBe(1999)
        ->and($message->control_lines_json)->toBe(['msgid' => '2:230/150 12345678'])
        ->and($message->provenance_json)->toBe(['sourceType' => 'jam', 'sourceOffset' => 256]);
});

it('resolves relationships through configured model classes', function (): void {
    config()->set('ftn-database.models.area', ConfiguredArea::class);
    config()->set('ftn-database.models.message', ConfiguredMessage::class);

    $area = ConfiguredArea::create(areaRecord());
    $message = ConfiguredMessage::create(messageRecord($area));

    expect($area->messages()->first())->toBeInstanceOf(ConfiguredMessage::class)
        ->and($message->area)->toBeInstanceOf(ConfiguredArea::class);
});

it('enforces source identity inside an area', function (): void {
    $area = Area::create(areaRecord());

    Message::create(messageRecord($area, [
        'source_type' => 'jam',
        'source_uid' => 'jam:offset:256',
    ]));

    expect(fn () => Message::create(messageRecord($area, [
        'source_type' => 'jam',
        'source_uid' => 'jam:offset:256',
    ])))->toThrow(QueryException::class);
});

it('scopes source identity by area', function (): void {
    $firstArea = Area::create(areaRecord(['code' => 'FIRST']));
    $secondArea = Area::create(areaRecord(['code' => 'SECOND']));

    Message::create(messageRecord($firstArea, [
        'source_type' => 'jam',
        'source_uid' => 'jam:offset:256',
    ]));

    Message::create(messageRecord($secondArea, [
        'source_type' => 'jam',
        'source_uid' => 'jam:offset:256',
    ]));

    expect(Message::count())->toBe(2);
});

it('scopes external IDs by area', function (): void {
    $firstArea = Area::create(areaRecord(['code' => 'FIRST']));
    $secondArea = Area::create(areaRecord(['code' => 'SECOND']));

    Message::create(messageRecord($firstArea, ['external_id' => '2:230/150 abc']));
    Message::create(messageRecord($secondArea, ['external_id' => '2:230/150 abc']));

    expect(Message::count())->toBe(2)
        ->and(fn () => Message::create(messageRecord($firstArea, [
            'source_uid' => 'jam:offset:512',
            'external_id' => '2:230/150 abc',
        ])))->toThrow(QueryException::class);
});

it('allows multiple null external IDs', function (): void {
    $area = Area::create(areaRecord());

    Message::create(messageRecord($area, [
        'source_uid' => 'jam:offset:256',
        'external_id' => null,
    ]));

    Message::create(messageRecord($area, [
        'source_uid' => 'jam:offset:512',
        'external_id' => null,
    ]));

    expect(Message::whereNull('external_id')->count())->toBe(2);
});
