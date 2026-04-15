<?php

namespace App\Aplication\Category\Services;

use Illuminate\Support\Str;

class SlugService
{
    public function generate(string $name): string
    {
        return Str::slug($name);
    }
}
