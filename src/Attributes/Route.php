<?php

declare(strict_types=1);

namespace App\Attributes;

use App\Enums\RequestType;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public readonly string $path,
        public readonly RequestType $method = RequestType::GET
    ) {
    }
}
