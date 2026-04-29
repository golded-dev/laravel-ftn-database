<?php

declare(strict_types=1);

namespace Golded\Ftn\Database\Tests\Fixtures\Models;

use Golded\Ftn\Database\Models\Message;

class ConfiguredMessage extends Message
{
    protected $table = 'messages';
}
