<?php

namespace Aweram\TraitsHelpers\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class BuilderActionsManager
{
    public function extendLike(mixed $query, string $value, string $field): void
    {
        /**
         * @var Builder|QueryBuilder $query
         */
        if (! empty($value)) {
            $value = trim($value);
            $query->where($field, "like", "%$value%");
        }
    }

    public function extendPublished(mixed $query, string $value, string $yes = "yes", string $no = "no"): void
    {
        /**
         * @var Builder|QueryBuilder $query
         */
        if (trim($value) === $yes) $query->whereNotNull("published_at");
        if (trim($value) === $no) $query->whereNull("published_at");
    }

    public function extendDate(mixed $query, string $from, string $to, string $field = "created_at"): void
    {
        /**
         * @var Builder|QueryBuilder $query
         */
        if (! empty($from)) {
            $from = date_helper()->forFilter($from);
            $query->where($field, ">=", $from);
        }

        if (! empty($to)) {
            $to = date_helper()->forFilter($to, true);
            $query->where($field, "<=", $to);
        }
    }

    public function extendEquals(mixed $query, string $value, string $field): void
    {
        /**
         * @var Builder|QueryBuilder $query
         */
        if (! empty($value)) {
            $value = trim($value);
            $query->where($field, "=", "$value");
        }
    }
}
