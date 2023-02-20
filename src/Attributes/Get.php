<?php

declare(strict_types=1);

namespace App\Attributes;

use App\Enums\RequestType;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Get extends Route
{
    public function __construct(string $path)
    {
        parent::__construct($path, RequestType::GET);
    }
}
