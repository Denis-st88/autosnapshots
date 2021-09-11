<?php

declare(strict_types=1);

namespace App\Frontend\Test\Unit;

use PHPUnit\Framework\TestCase;
use App\Frontend\FrontendUrlGenerator;

/**
 * @covers \App\Frontend\FrontendUrlGenerator
 *
 * @internal
 */
class FrontendUrlGeneratorTest extends TestCase
{
    public function testEmpty(): void
    {
        $generate = new FrontendUrlGenerator('http://test');

        self::assertEquals('http://test', $generate->generate(''));
    }

    public function testPath(): void
    {
        $generate = new FrontendUrlGenerator('http://test');

        self::assertEquals('http://test/path', $generate->generate('path'));
    }

    public function testWithParams(): void
    {
        $generate = new FrontendUrlGenerator('http://test');

        self::assertEquals('http://test/path?a=1&b=2', $generate->generate('path', [
            'a' => 1,
            'b' => 2
        ]));
    }
}
