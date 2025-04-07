<?php

namespace DsTransformer\wrappers;

use DsTransformer\enums\ArraySortEnum;
use InvalidArgumentException;

Class ArrayWrapper {
    private array $data;

    public function __construct(array $data) {
        $this->setData($data);
    }

    private function setData(array $data): void {
        $this->data = $data;
    }

    public function get(): array {
        return $this->data;
    } 

    public function push(mixed $item): self {
        $this->data[] = $item;
        return $this;
    }

    public function merge(array $array): self {
        $this->setData(array_merge($this->data, $array));
        return $this;
    }

    public function flatMerge(array $array): self {
        return $this->merge(array_merge(...$array));
    }

    public function pop(): self {
        array_pop($this->data);
        return $this;
    }

    public function shift(): self {
        array_shift($this->data);
        return $this;
    }

    public function unshift(mixed ...$values): self {
        array_unshift($this->data, ...$values);
        return $this;
    }

    public function count(): int {
        return count($this->data);
    }

    public function slice(int $offset, ?int $length): self {
        $this->setData(array_slice($this->data, $offset, $length, true));
        return $this;
    }

    public function replaceFromIndex(int $offset, array $replacement): self {
        array_splice($this->data, $offset, count($this->data) - $offset, $replacement);
        return $this;
    }


    public function replaceToIndex(int $index, array $replacement): self {
        array_splice($this->data, 0, $index, $replacement);
        return $this;
    }

    public function splice(int $offset, int $length, ?array $replacement = null): self {
        array_splice($this->data, $offset,$length, $replacement);
        return $this;
    }

    public function filter(callable $callback): self {
        $this->setData(array_filter($this->data, $callback));
        return $this;
    }

    public function map(callable $callback): self {
        $this->setData(array_map($callback, $this->data));
        return $this;
    }

    public function reduce(callable $callback, mixed $initial = null): mixed {
        return array_reduce($this->data, $callback, $initial);
    }

    public function every(callable $callback): bool {
        return array_all($this->data, $callback);
    }

    public function any(callable $callback): bool {
        return array_any($this->data, $callback);
    }

    public function findOne(callable $callback): mixed {
        return array_find($this->data, $callback);
    }

    public function findByIndex(int $index): mixed {
        return $this->data[$index];
    }

    private function handleFlatRecursively(?array $array): array {
        $flatArray = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $flatArray = array_merge($flatArray, $this->handleFlatRecursively($item));
            }
            else $flatArray[] = $item;
        }
        return $flatArray;
    }

    public function flat(): self {
        $this->setData($this->handleFlatRecursively($this->data));
        return $this;
    }

    public function chunk(int $cLength, bool $preserve_keys = false): self {
        $this->setData(array_chunk($this->data, $cLength, $preserve_keys));
        return $this;
    }

    public function diff(array ...$arrays): self {
        $this->setData(array_diff($this->data, ...$arrays));
        return $this;
    }

    public function diffWithIndexCheck(array ...$arrays): self {
        $this->setData(array_diff_assoc($this->data, ...$arrays));
        return $this;
    }

    public function intersect(array ...$arrays): self {
        $this->setData(array_intersect($this->data, ...$arrays));
        return $this;
    }

    public function intersectWithIndexCheck(array ...$arrays): self {
        $this->setData(array_intersect_assoc($this->data, ...$arrays));
        return $this;
    }

    public function pad(int $length, mixed $value): self {
        $this->setData(array_pad($this->data, $length, $value));
        return $this;
    }

    public function randomValue(int $numberOfItems = 1): array | int | string {
        $indexes = array_rand($this->data, $numberOfItems);
        return is_array($indexes) ? array_map(fn ($index) => $this->data[$index], $indexes) : $this->data[$indexes];
    }

    public function random(int $numberOfItems = 1): array | int {
        return array_rand($this->data, $numberOfItems);
    }

    public function shuffle(): self {
        shuffle($this->data);
        return $this;
    }

    public function reverse(): self {
        $this->setData(array_reverse($this->data));
        return $this;
    }

    public function implode(string $separator): string {
        return implode($separator, $this->data);
    }

    public function join(string $separator): string {
        return $this->implode($separator);
    }

    public function reverseJoin(string $separator): string {
        return $this->reverse()->join($separator);
    }

    public function sort(ArraySortEnum | int | null $order = null): self {
        if (is_int($order) && $order !== 1 && $order !== -1) 
            throw new InvalidArgumentException('Order must be 1 (asc) or -1 (desc)');

        match ($order) {
            ArraySortEnum::ASC, 1, null  => sort($this->data),
            ArraySortEnum::DESC, -1      => rsort($this->data),
        };
        return $this;
    }

    public function sortByCallback(callable $callback): self {
        usort($this->data, $callback);
        return $this;
    }

    public function walk(callable $callback): void {
        array_walk($this->data, $callback);
    }

    public function contains(mixed $value): bool {
        return in_array($value, $this->data, true);
    }

    public function indexOf(mixed $value): int | bool {
        return array_search($value, $this->data, true);
    }

    public function keys(): array {
        return array_keys($this->data);
    }

    public function indexesOf(mixed $value): array {
        return $this->filter(fn (int $item): int => $item === $value)->keys(); 
    }

    public function unique(): self {
        $this->setData(array_unique($this->data));
        return $this;
    }

    public function hasDuplicate(): bool {
        return $this->count() > $this->unique()->count();
    }
}