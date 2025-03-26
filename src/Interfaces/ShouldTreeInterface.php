<?php

namespace GIS\TraitsHelpers\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface ShouldTreeInterface
{
    public function parent(): BelongsTo;
    public function children(): HasMany;
}
