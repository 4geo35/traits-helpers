<?php

namespace Aweram\TraitsHelpers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
trait ShouldSlug
{
    protected static function bootShouldSlug(): void
    {
        static::creating(function (Model $model) {
            $model->fixSlug();
        });

        static::updating(function (Model $model) {
            $model->fixSlug(true);
        });
    }

    public function getRouteKeyName(): string
    {
        return "slug";
    }

    protected function getSlugKey()
    {
        if (! empty($this->slugKey)) return $this->slugKey;
        else return "title";
    }

    public function fixSlug($updating = false): void
    {
        if ($updating && ($this->original["slug"] == $this->slug)) return;

        $slug = empty($this->slug) ? $this->{$this->getSlugKey()} : $this->slug;
        $slug = Str::slug($slug);
        $buf = $slug;
        $i = 1;
        $id = $updating ? $this->id : 0;
        while (self::query()
                ->select("id")
                ->where("slug", $buf)
                ->where("id", "!=", $id)
                ->count()) {
            $buf = $slug . "-" . $i++;
        }
        $this->slug = $buf;
    }
}
