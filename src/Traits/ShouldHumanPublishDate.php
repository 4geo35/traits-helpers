<?php

namespace GIS\TraitsHelpers\Traits;

trait ShouldHumanPublishDate
{
    public function getPublishedMoscowAttribute()
    {
        $value = $this->published_at;
        if (empty($value)) return $value;
        return date_helper()->changeTz($value);
    }

    public function getPublishedHumanAttribute()
    {
        $value = $this->published_moscow;
        if (empty($value)) return $value;
        return date_helper()->format($value);
    }

    public function getPublishedDateAttribute(): string
    {
        $value = $this->published_moscow;
        if (empty($value)) return $value;
        return date_helper()->format($value, "d.m.Y");
    }
}
