<?php

declare(strict_types=1);

namespace Golded\Ftn\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $echoid
 * @property string|null $source_type
 * @property string|null $area_type
 * @property string|null $source_group_code
 * @property int $source_sort_order
 */
class Area extends Model
{
    protected $fillable = [
        'code',
        'name',
        'echoid',
        'source_type',
        'area_type',
        'source_group_code',
        'source_sort_order',
    ];

    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany($this->messageModel(), 'area_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source_sort_order' => 'integer',
        ];
    }

    /**
     * @return class-string<Message>
     */
    private function messageModel(): string
    {
        $model = config('ftn-database.models.message', Message::class);

        if (is_string($model) && is_a($model, Message::class, true)) {
            /** @var class-string<Message> $model */
            return $model;
        }

        return Message::class;
    }
}
