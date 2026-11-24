<?php

it('parses comma-separated ids into integers', function () {
    expect(parseIdList('1, 2,3'))->toBe([1, 2, 3]);
});

it('accepts arrays', function () {
    expect(parseIdList(['4', 5]))->toBe([4, 5]);
});

it('drops anything that is not numeric', function (mixed $input, array $expected) {
    expect(parseIdList($input))->toBe($expected);
})->with([
    'sql injection' => ['1) OR 1=1 -- ', []],
    'injection among valid ids' => ['1,2) UNION SELECT password FROM users -- ,3', [1, 3]],
    'empty string' => ['', []],
    'nested array' => [[['1'], '2'], [2]],
]);
