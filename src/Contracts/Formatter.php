<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager\Contracts;

/**
 * @package NobiDev\EnvManager\Contracts
 */
interface Formatter
{
    public function formatKey(string $key): string;

    public function formatValue(string $value, bool $force_quotes = false): string;

    public function formatComment(string $comment): string;

    public function formatSetterLine(string $key, string $value = null, string $comment = null, bool $export = false): string;

    public function normaliseKey(string $key): string;

    public function normaliseValue(string $value, string $quote = ''): string;

    public function normaliseComment(string $comment): string;

    public function parseLine(string $line): array;
}
