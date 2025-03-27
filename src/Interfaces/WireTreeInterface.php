<?php

namespace GIS\TraitsHelpers\Interfaces;

interface WireTreeInterface
{
    public function showCreate(int $parentId = null): void;
    public function showEdit(int $id): void;
    public function showDelete(int $id): void;
}
