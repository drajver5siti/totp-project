<?php

declare(strict_types=1);

namespace App\Services\Implementations;

use App\Services\EmailServiceInterface;

class SendgridEmailService implements EmailServiceInterface
{
    public function process(): string
    {
        return "<h1>Processing email via Sendgrid</h1>";
    }
}
