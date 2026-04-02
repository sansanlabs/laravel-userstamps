<?php

use SanSanLabs\Userstamps\Tests\Models\Product;

it('does not throw circular boot exception when model is booted during observer registration', function () {
    expect(fn () => new Product)->not->toThrow(LogicException::class);
});
