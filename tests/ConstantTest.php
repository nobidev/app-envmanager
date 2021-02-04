<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager\Tests;

use NobiDev\EnvManager\Constant;
use PHPUnit\Framework\TestCase;

/**
 * @package NobiDev\EnvManager\Tests
 */
class ConstantTest extends TestCase
{
    public function testNameNotEmpty(): void
    {
        self::assertNotEmpty(Constant::getName());
    }
}
