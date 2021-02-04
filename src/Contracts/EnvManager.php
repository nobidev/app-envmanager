<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager\Contracts;

use Illuminate\Contracts\Container\Container;

/**
 * @package NobiDev\EnvManager\Contracts
 */
interface EnvManager
{
    public function __construct(Container $app);

    public function load(string $filePath = null, bool $restoreIfNotFound = false, string $restorePath = null): EnvManager;

    public function getContent(): string;

    public function getLines(): array;

    public function getValue(string $key): string;

    public function getKeys(array $list_keys = []): array;

    public function getBuffer(): string;

    public function addEmpty(): EnvManager;

    public function addComment(string $comment): EnvManager;

    public function setKey(string $key, string $value = null, string $comment = null, bool $export = false): EnvManager;

    public function setKeys(array $data): EnvManager;

    public function keyExists(string $key): bool;

    public function deleteKey(string $key): EnvManager;

    public function deleteKeys(array $keys = []): EnvManager;

    public function save(): EnvManager;
}
