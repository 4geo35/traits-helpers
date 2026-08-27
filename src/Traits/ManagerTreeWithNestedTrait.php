<?php

namespace GIS\TraitsHelpers\Traits;

use GIS\TraitsHelpers\Interfaces\ShouldTreeInterface;
use Illuminate\Database\Eloquent\Builder;

trait ManagerTreeWithNestedTrait
{
    public string $elementModelClass;
    public bool $isMorphed = false;
    public string $elementRelationCol;

    public function buildNestedTree(ShouldTreeInterface $item = null): array
    {
        list($items, $roots) = $this->makeNestedItemDataWithElements($item);
        $grouped = $this->splitByParents($items);
        $tree = [];
        foreach ($roots as $id) {
            $item = $items[$id];
            $this->addChildren($item, $grouped);
            $tree[$id] = $item;
        }
        return $this->sortByPriority($tree);
    }

    public function findNestedChild(ShouldTreeInterface $item): ShouldTreeInterface
    {
        $firstChild = $item->children()->whereNotNull("published_at")->orderBy("priority")->first();
        if (! $firstChild) { return $item; }
        if ($firstChild->show_nested) { return $firstChild; }
        return $this->findNestedChild($firstChild);
    }

    public function findRootNested(ShouldTreeInterface $item): ?ShouldTreeInterface
    {
        $parent = $item->parent;
        if (! $parent) { return null; }
        $check = $this->findRootNested($parent);
        if (! $parent->show_nested && ! $check) { return null; }
        if (! $check) { return $parent; }
        return $check;
    }

    public function getElementIds(ShouldTreeInterface $item, bool $includeSubs = false): array
    {
        $query = $this->elementModelClass::query()
            ->select("id");
        if ($this->isMorphed) {
            $relationCol = $this->elementRelationCol . "_id";
            $query->where("{$relationCol}_type", $item::class);
        } else { $relationCol = $this->elementRelationCol;}

        if ($includeSubs) { $query->whereIn($relationCol, $this->getChildrenIds($item, true)); }
        else { $query->where($relationCol, $item->id); }
        $elements = $query->get();
        $eIds = [];
        foreach ($elements as $element) {
            $eIds[] = $element->id;
        }
        return $eIds;
    }

    protected function makeNestedItemDataWithElements(ShouldTreeInterface $item = null): array
    {
        $query = $this->modelClass::query();
        $this->expandRawDataQueryWith($query, $item);
        if ($item) {
            $ids = $this->getChildrenIds($item, true);
            $query->whereIn("id", $ids);
        }
        $categories = $query->orderBy("parent_id")->get();

        $items = [];
        $roots = [];
        if ($item && $item->parent_id) { $roots[] = $item->id; }
        foreach ($categories as $category) {
            $data = [
                "model" => $category,
                "parent" => $category->parent_id,
                "priority" => $category->priority,
                "id" => $category->id,
                "children" => [],
            ];
            $this->expandNestedData($data, $category);
            $items[$category->id] = $data;
            if (empty($category->parent_id)) { $roots[] = $category->id; }
        }
        return [$items, $roots];
    }

    protected function expandRawDataQueryWith(Builder $query, ShouldTreeInterface $item = null): void
    {}

    protected function expandNestedData(array &$data, ShouldTreeInterface $item): void
    {}
}
