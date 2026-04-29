<?php

declare(strict_types=1);

namespace Golded\Ftn\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $area_id
 * @property int|null $msgno
 * @property string $source_type
 * @property string $source_uid
 * @property string|null $source_locator
 * @property int|null $source_offset
 * @property string|null $external_id
 * @property string|null $subject
 * @property string|null $from_name
 * @property string|null $from_address
 * @property string|null $to_name
 * @property string|null $to_address
 * @property string $body_text
 * @property string|null $body_raw
 * @property string|null $body_encoding
 * @property int|null $reply_to_msgno
 * @property string|null $reply_to_external_id
 * @property int|null $reply1st_msgno
 * @property int|null $replynext_msgno
 * @property int $attributes_raw
 * @property Carbon|null $posted_at
 * @property Carbon|null $arrived_at
 * @property array<string, mixed>|null $control_lines_json
 * @property array<string, mixed>|null $provenance_json
 */
class Message extends Model
{
    protected $fillable = [
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
    ];

    /**
     * @return BelongsTo<Area, $this>
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo($this->areaModel(), 'area_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'msgno' => 'integer',
            'source_offset' => 'integer',
            'reply_to_msgno' => 'integer',
            'reply1st_msgno' => 'integer',
            'replynext_msgno' => 'integer',
            'attributes_raw' => 'integer',
            'posted_at' => 'datetime',
            'arrived_at' => 'datetime',
            'control_lines_json' => 'array',
            'provenance_json' => 'array',
        ];
    }

    /**
     * @return class-string<Area>
     */
    private function areaModel(): string
    {
        $model = config('ftn-database.models.area', Area::class);

        if (is_string($model) && is_a($model, Area::class, true)) {
            /** @var class-string<Area> $model */
            return $model;
        }

        return Area::class;
    }
}
