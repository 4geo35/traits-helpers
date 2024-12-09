<?php

namespace Aweram\TraitsHelpers\Traits;
trait ShouldHumanDate
{
    public function getCreatedMoscowAttribute()
    {
        $value = $this->created_at;
        if (empty($value)) return $value;
        return date_helper()->changeTz($value);
    }
    public function getCreatedHumanAttribute()
    {
        $value = $this->created_moscow;
        if (empty($value)) return $value;
        return date_helper()->format($value);
    }

    public function getUpdatedMoscowAttribute()
    {
        $value = $this->updated_at;
        if (empty($value)) return $value;
        return date_helper()->changeTz($value);
    }

    public function getUpdatedHumanAttribute()
    {
        $value = $this->updated_moscow;
        if (empty($value)) return $value;
        return date_helper()->format($value);
    }
}
