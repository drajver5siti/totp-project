<?php

declare(strict_types=1);

namespace App;

use Twig\Environment;

class View
{
    public function __construct(private Environment $twig)
    {
    }

    public function make(string $path, array $variables = [])
    {
        if (!str_ends_with($path, '.html.twig')) {
            $path = $path . '.html.twig';
        }

        return $this->twig->render($path, $variables);
    }
}
