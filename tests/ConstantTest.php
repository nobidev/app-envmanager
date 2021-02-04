<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

use NobiDev\EnvManager\Constant;
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
