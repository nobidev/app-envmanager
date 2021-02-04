<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\LibraryStarter\Contracts;

use Illuminate\Contracts\Container\Container;

/**
 * Interface LibraryStarter
 * @package NobiDev\LibraryStarter\Contracts
 */
interface LibraryStarter
{
    public function __construct(Container $app);

    public function load(): LibraryStarter;
}
