<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\LibraryStarter\Workers;

use NobiDev\LibraryStarter\Contracts\Example as ExampleContract;
use NobiDev\LibraryStarter\Exceptions\ExampleException;

/**
 * @package NobiDev\LibraryStarter\Workers
 */
class Example implements ExampleContract
{
    /**
     * @throws ExampleException
     */
    public function raiseException(): void
    {
        throw new ExampleException('Example Exception');
    }
}
