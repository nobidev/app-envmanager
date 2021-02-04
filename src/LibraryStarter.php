<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\LibraryStarter;

use Illuminate\Contracts\Container\Container;
use NobiDev\LibraryStarter\Contracts\LibraryStarter as LibraryStarterContract;
use NobiDev\LibraryStarter\Workers\Example;

/**
 * Class LibraryStarter
 * @package NobiDev\LibraryStarter
 */
class LibraryStarter implements LibraryStarterContract
{
    protected Container $application;
    protected Example $worker;

    public function __construct(Container $app)
    {
        $this->application = $app;

        // $this->worker = null;

        $this->load();
    }

    public function load(): LibraryStarter
    {
        $this->resetContent();

        return $this;
    }

    protected function resetContent(): void
    {
        $this->worker = new Example();
    }
}
