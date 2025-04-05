<?php
namespace Tests;

use DsTransformer\enums\ArraySortEnum;
use DsTransformer\wrappers\ArrayWrapper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ArrayWrapperTest extends TestCase
{
    private ArrayWrapper $wrapper;
    private array $data = [1, 2, 3];
    protected function setUp(): void {
        $this->wrapper = new ArrayWrapper($this->data);
    }

    public function testget(): void
    {
        $this->assertEquals($this->data, $this->wrapper->get());
    }

    public function testPush(): void 
    {
        $this->wrapper->push(5);
        $this->assertEquals([...$this->data, 5], $this->wrapper->get());
    }

    public function testMerge(): void
    {
        $this->wrapper->merge([10, 12]);
        $this->assertEquals([...$this->data, 10, 12], $this->wrapper->get());
    }

    public function testFlatMerge(): void
    {
        $this->wrapper->flatMerge([[5, 6], [10, 12]]);
        $this->assertEquals([...$this->data, 5, 6, 10, 12], $this->wrapper->get());
    }

    public function testPop(): void
    {
        $this->wrapper->pop();
        $this->assertEquals([1, 2], $this->wrapper->get());
    }

    public function testShift(): void
    {
        $this->wrapper->shift();
        $this->assertEquals([2, 3], $this->wrapper->get());
    }

    public function testUnshift(): void
    {
        $this->wrapper->unshift(0, 3);
        $this->assertEquals([0, 3, 1, 2, 3], $this->wrapper->get());
    }

    public function testCount(): void
    {
        $this->assertEquals(3, $this->wrapper->count());
    }

    public function testSlice(): void
    {
        $this->wrapper->slice(1, 1);
        $this->assertEquals([1 => 2], $this->wrapper->get());
    }

    public function testReplaceFromIndex(): void
    {
        $this->wrapper->replaceFromIndex(1, [5, 6]);
        $this->assertEquals([1, 5, 6], $this->wrapper->get());
    }

    public function testReplaceToIndex(): void
    {
        $this->wrapper->replaceToIndex(2, [5, 6]);
        $this->assertEquals([5, 6, 3], $this->wrapper->get());
    }

    public function testSplice(): void
    {
        $this->wrapper->splice(0, 1);
        $this->assertEquals([2, 3], $this->wrapper->get());
    }

    public function testFilter(): void
    {
        $this->wrapper->filter(fn (int $item): bool => $item > 2);
        $this->assertEquals([2 => 3], $this->wrapper->get());
    }

    public function testMap(): void
    {
        $this->wrapper->map(fn (int $item): string => "MAP$item");
        $this->assertEquals(['MAP1', 'MAP2', 'MAP3'], $this->wrapper->get());
    }

    public function testReduce(): void
    {
        $this->assertEquals(
            6, $this->wrapper->reduce(fn (int $carry, int $item): string => $carry * $item, 1)
        );
    }

    public function testEvery(): void
    {
        $this->assertEquals(
            false, $this->wrapper->every(fn (int $item): bool => $item%2 === 0)
        );
    }

    public function testAny(): void
    {
        $this->assertEquals(
            true, $this->wrapper->any(fn (int $item): bool => $item%2 === 0)
        );
    }

    public function testFindOne(): void
    {
        $this->assertEquals(
            1, $this->wrapper->findOne(fn (int $item): bool => $item%2 !== 0)
        );
    }

    public function testFlatten(): void
    {
        $this->wrapper->merge([[4,5,6], [7,8,9]]);
        $this->wrapper->flat();
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $this->wrapper->get());
    }

    public function testChunk(): void
    {
        $this->wrapper->chunk(2);
        $this->assertEquals([[1, 2], [3]], $this->wrapper->get());
    }

    public function testDiff(): void 
    {
        $this->wrapper->diff([2, 3]);
        $this->assertEquals([1], $this->wrapper->get());
    }

    public function testDiffWithIndexCheck(): void 
    {
        $this->wrapper->diffWithIndexCheck([3, 2]);
        $this->assertEquals([0 => 1, 2=> 3], $this->wrapper->get());
    }

    public function testIntersect(): void
    {
        $this->wrapper->intersect([4, 5]);
        $this->assertEquals([], $this->wrapper->get());
    }

    public function testIntersectWithIndexCheck(): void 
    {
        $this->wrapper->intersectWithIndexCheck([3, 2]);
        $this->assertEquals([1 => 2], $this->wrapper->get());
    }

    public function testPadWithPositiveLength(): void
    {
        $this->wrapper->pad(5, 0);
        $this->assertEquals([1, 2, 3, 0, 0], $this->wrapper->get());
    }

    public function testPadWithNegativeLength(): void
    {
        $this->wrapper->pad(-5, 0);
        $this->assertEquals([0, 0, 1, 2, 3], $this->wrapper->get());
    }

    public function testPadWithLessLength(): void
    {
        $this->wrapper->pad(2, 0);
        $this->assertEquals([1, 2, 3], $this->wrapper->get());
    }

    public function testRandomValue(): void
    {
        $item = $this->wrapper->randomValue();
        $this->assertTrue(in_array($item, $this->wrapper->get()));
    }

    public function testRandom(): void
    {
        $item = $this->wrapper->random();
        $this->assertTrue(in_array($item, array_keys($this->wrapper->get())));
    }

    public function testShuffle(): void
    {
        $this->wrapper->shuffle();
        $this->assertCount(0, array_diff([1, 2, 3], $this->wrapper->get()));
    }

    public function testReverse(): void
    {
        $this->wrapper->reverse();
        $this->assertEquals([3, 2, 1], $this->wrapper->get());
    }

    public function testImplode(): void
    {
        $this->assertEquals('1,2,3', $this->wrapper->implode(','));
    }

    public function testJoin(): void
    {
        $this->assertEquals('1-2-3', $this->wrapper->join('-'));
    }

    public function testReverseJoin(): void
    {
        $this->assertEquals('3,2,1', $this->wrapper->reverseJoin(','));
    }

    public function testDefaultSort(): void
    {
        $this->wrapper->sort();
        $this->assertEquals([1,2,3], $this->wrapper->get());
    }
    public function testSortWithIntOrder(): void
    {
        $this->wrapper->sort(-1);
        $this->assertEquals([3,2,1], $this->wrapper->get());
    }

    public function testSortThrowInvalidArgumentError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->wrapper->sort(2);
    }

    public function testSortWithEnumOrder(): void
    {
        $this->wrapper->sort(ArraySortEnum::DESC);
        $this->assertEquals([3,2,1], $this->wrapper->get());
    }

    public function testSortByCallback(): void
    {
        $this->wrapper->sortByCallback(fn (int $a, int $b): int =>  $a <=> $b);
        $this->assertEquals([1,2,3], $this->wrapper->get());
    }
}