<?php

declare(strict_types=1);

use Golded\Ftn\Database\Models\Area;
use Golded\Ftn\Database\Models\Message;

return [
    'load_migrations' => true,

    'models' => [
        'area' => Area::class,
        'message' => Message::class,
    ],
];
