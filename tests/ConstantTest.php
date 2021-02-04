<?php

declare(strict_types=1);

use NobiDev\LibraryStarter\Constant;
use PHPUnit\Framework\TestCase;

/**
 * Class ConstantTest
 */
class ConstantTest extends TestCase
{
    public function testNameNotEmpty(): void
    {
        self::assertNotEmpty(Constant::getName());
    }
}
