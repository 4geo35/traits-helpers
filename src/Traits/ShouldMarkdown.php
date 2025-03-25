<?php

namespace GIS\TraitsHelpers\Traits;

use Illuminate\Support\Str;

trait ShouldMarkdown
{
    public function getMarkdownAttribute(): ?string
    {
        $value = $this->description;
        if (! $value) return $value;
        return Str::markdown($value);
    }
}
