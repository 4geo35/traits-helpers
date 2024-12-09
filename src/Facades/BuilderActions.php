<?php

namespace Aweram\TraitsHelpers\Facades;

use Aweram\TraitsHelpers\Helpers\BuilderActionsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void extendLike(mixed $query, string $value, string $field)
 * @method static void extendPublished(mixed $query, string $value, string $yes = "yes", string $no = "no")
 * @method static void extendDate(mixed $query, string $from, string $to, string $field = "created_at")
 * @method static void extendEquals(mixed $query, string $value, string $field)
 *
 * @see BuilderActionsManager
 */
class BuilderActions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "builder-actions";
    }
}
