<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\LibraryStarter\Tests;

use NobiDev\LibraryStarter\Constant;
use PHPUnit\Framework\TestCase;

/**
 * @package NobiDev\LibraryStarter\Tests
 */
class ConstantTest extends TestCase
{
    public function testNameNotEmpty(): void
    {
        self::assertNotEmpty(Constant::getName());
    }
}
