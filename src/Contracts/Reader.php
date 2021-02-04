<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager\Contracts;

/**
 * @package NobiDev\EnvManager\Contracts
 */
interface Reader
{
    public function load(string $filePath): Reader;

    public function content(): string;

    public function lines(): array;

    public function keys(): array;
}
