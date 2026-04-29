<?php

declare(strict_types=1);

use Golded\Ftn\Database\Tests\TestCase;
use Golded\Ftn\Database\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function areaRecord(array $overrides = []): array
{
    return [
        'code' => 'TEST',
        'name' => 'Test Area',
        'echoid' => 'TEST',
        'source_type' => 'jam',
        'area_type' => 'Echo',
        'source_group_code' => 'A',
        'source_sort_order' => 0,
        ...$overrides,
    ];
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function messageRecord(Area $area, array $overrides = []): array
{
    return [
        'area_id' => $area->id,
        'msgno' => 1,
        'source_type' => 'jam',
        'source_uid' => 'jam:offset:128',
        'source_locator' => '/archives/JAM/TEST.jhr',
        'source_offset' => 128,
        'external_id' => '2:230/150 12345678',
        'subject' => 'Hello',
        'from_name' => 'Alice',
        'from_address' => '2:230/150',
        'to_name' => 'Bob',
        'to_address' => '2:230/151',
        'body_text' => 'Message body',
        'attributes_raw' => 0,
        ...$overrides,
    ];
}
