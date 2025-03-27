<?php

namespace GIS\TraitsHelpers\Traits;

use GIS\TraitsHelpers\Interfaces\ShouldTreeInterface;
use Illuminate\Support\Arr;

trait ManagerTreeTrait
{
    public string $modelClass;
    public bool $hasImage;

    public function getCategoryTree(array $newOrder = null): array
    {
        list($tree, $roots) = $this->makeRawCategoryData();
        if ($newOrder) {
            return $this->setTmpOrder($newOrder, $tree);
        }
        $this->addChildren($tree);
        $this->clearTree($tree, $roots);
        return $this->sortByPriority($tree);
    }

    public function rebuildTree(array $newOrder): bool
    {
        try {
            $this->buildCategoryTree($newOrder);
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    protected function buildCategoryTree(array $newOrder, int $parent = null): void
    {
        foreach ($newOrder as $priority => $item) {
            $id = $item["id"];
            if (! empty($item["children"])) { $this->buildCategoryTree($item["children"], $id); }
            $parentId = ! empty($parent) ? $parent : null;
            $category = $this->modelClass::find($id);
            if (! $category) { continue; }
            if (! class_implements($category, ShouldTreeInterface::class)) { continue; }
            /**
             * @var ShouldTreeInterface $category
             */
            $category->priority = $priority;
            $category->parent_id = $parentId;
            $category->save();
        }
    }

    protected function setTmpOrder(array $newOrder, array $tree): array
    {
        if (empty($newOrder)) { return []; }
        $array = [];
        foreach ($newOrder as $key => $item) {
            if (empty($tree[$item["id"]])) { continue; }
            $tmp = $tree[$item["id"]];
            $tmp["ts"] = now()->timestamp;
            $tmp["priority"] = $key;
            $tmp["children"] = $this->setTmpOrder($item["children"], $tree);
            $array[] = $tmp;
        }
        return $array;
    }

    protected function makeRawCategoryData(): array
    {
        $query = $this->modelClass::query();
        if ($this->hasImage) { $query->with("image"); }
        $categories = $query->orderBy("parent_id")
            ->get();

        $tree = [];
        $roots = [];
        foreach ($categories as $category) {
            /**
             * @var ShouldTreeInterface $category
             */
            $data = [
                "model" => $category,
                "title" => $category->title,
                "slug" => $category->slug,
                "parent" => $category->parent_id,
                "priority" => $category->priority,
                "id" => $category->id,
                "ts" => now()->timestamp,
                "children" => [],
            ];
            if ($this->hasImage) { $data["imageUrl"] = $category->image_id ? $category->image->storage : null; }
            $this->expandItemData($data, $category);
            $tree[$category->id] = $data;
            if (empty($category->parent_id)) { $roots[] = $category->id; }
        }
        return [$tree, $roots];
    }

    protected function expandItemData(&$data, ShouldTreeInterface $category): void
    {}

    protected function addChildren(array &$tree): void
    {
        foreach ($tree as $id => $item) {
            if (empty($item["parent"]))
                continue;
            $this->addChild($tree, $item, $id);
        }
    }

    protected function addChild(&$tree, $item, $id, $children = false): void
    {
        // Добавление к дочерним
        if (! $children) $tree[$item["parent"]]["children"][$id] = $item;
        // Обновление дочерних
        else $tree[$item["parent"]]["children"][$id]["children"] = $children;

        $parent = $tree[$item["parent"]];
        if (! empty($parent["parent"])) {
            $items = $parent["children"];
            $this->addChild($tree, $parent, $parent["id"], $items);
        }
    }

    protected function clearTree(&$tree, $roots): void
    {
        foreach ($roots as $id) {
            $this->removeChildren($tree, $id);
        }
    }

    protected function removeChildren(&$tree, $id): void
    {
        if (empty($tree[$id])) return;
        $item = $tree[$id];
        foreach ($item["children"] as $key => $child) {
            $this->removeChildren($tree, $key);
            if (! empty($tree[$key])) unset($tree[$key]);
        }
    }

    protected function sortByPriority($tree): array
    {
        $sorted = array_values(Arr::sort($tree, function ($value) {
            return $value["priority"];
        }));
        foreach ($sorted as &$item) {
            if (! empty($item["children"])) {
                $item["children"] = $this->sortByPriority($item["children"]);
            }
        }
        return $sorted;
    }
}
