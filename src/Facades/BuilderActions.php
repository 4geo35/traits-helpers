<?php

namespace GIS\TraitsHelpers\Facades;

use GIS\TraitsHelpers\Helpers\BuilderActionsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void extendLike(mixed $query, string $value, string $field)
 * @method static void extendRelationLike(mixed $query, string $value, string $field, string $relation)
 * @method static void extendPublished(mixed $query, string $value, string $yes = "yes", string $no = "no", string $field = "published_at")
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
