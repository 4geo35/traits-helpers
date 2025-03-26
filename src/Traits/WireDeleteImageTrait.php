<?php

namespace GIS\TraitsHelpers\Traits;

use GIS\Fileable\Interfaces\ShouldImageInterface;

trait WireDeleteImageTrait
{
    public bool $displayDeleteImage = false;

    public function showClearImage(): void
    {
        if (! method_exists($this, 'findModel')) { return; }
        $item = $this->findModel();
        if (! $item) return;
        if (
            method_exists($this, "checkAuth") &&
            ! $this->checkAuth("update", $item)
        ) { return; }
        $this->displayDeleteImage = true;
    }

    public function closeClearImage(): void
    {
        $this->displayDeleteImage = false;
    }

    public function clearImage(): void
    {
        if (! method_exists($this, 'findModel')) { return; }
        $item = $this->findModel();
        if (! $item) return;
        if (
            method_exists($this, "checkAuth") &&
            ! $this->checkAuth("update", $item)
        ) { return; }
        /**
         * @var ShouldImageInterface $item
         */
        $item->clearImage();
        $this->displayDeleteImage = false;
        if (isset($this->imageUrl)) { $this->imageUrl = null; }
        if (isset($this->coverUrl)) { $this->coverUrl = null; }
        $this->closeClearImage();
    }
}
