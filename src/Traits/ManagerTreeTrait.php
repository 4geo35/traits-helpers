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
        list($items, $roots) = $this->makeRawCategoryData();
        if ($newOrder) { return $this->setTmpOrder($newOrder, $items); }

        $grouped = $this->splitByParents($items);
        $tree = [];
        foreach ($roots as $id) {
            $item = $items[$id];
            $this->addChildren($item, $grouped);
            $tree[$id] = $item;
        }
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

        $items = [];
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
            $items[$category->id] = $data;
            if (empty($category->parent_id)) { $roots[] = $category->id; }
        }
        return [$items, $roots];
    }

    protected function splitByParents(array $items): array
    {
        $groups = [];
        foreach ($items as $item) {
            $parentId = $item["parent"];
            if (empty($parentId)) { continue; }
            if (!isset($groups[$parentId])) { $groups[$parentId] = []; }
            $groups[$parentId][] = $item;
        }
        return $groups;
    }

    protected function expandItemData(&$data, ShouldTreeInterface $category): void
    {}

    protected function addChildren(array &$parent, array $grouped): void
    {
        $parentId = $parent["id"];
        if (empty($grouped[$parentId])) { return; }
        $children = $grouped[$parentId];
        foreach ($children as $child) {
            $this->addChildren($child, $grouped);
            $parent["children"][$child["id"]] = $child;
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
