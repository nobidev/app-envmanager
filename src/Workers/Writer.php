<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager\Workers;

use NobiDev\EnvManager\Contracts\Formatter as FormatterContract;
use NobiDev\EnvManager\Contracts\Writer as WriterContract;
use NobiDev\EnvManager\Exceptions\UnableWriteToFileException;
use function dirname;

/**
 * @package NobiDev\EnvManager\Workers
 */
class Writer implements WriterContract
{
    protected ?string $buffer;
    protected FormatterContract $formatter;

    public function __construct(FormatterContract $formatter)
    {
        $this->formatter = $formatter;
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    public function setBuffer(?string $content): Writer
    {
        if (!empty($content)) {
            $content = rtrim($content) . PHP_EOL;
        }
        $this->buffer = $content;
        return $this;
    }

    public function appendEmptyLine(): Writer
    {
        return $this->appendLine();
    }

    protected function appendLine(string $text = null): Writer
    {
        $this->buffer .= $text . PHP_EOL;
        return $this;
    }

    public function appendCommentLine(string $comment): Writer
    {
        return $this->appendLine('# ' . $comment);
    }

    public function appendSetter(string $key, string $value = null, string $comment = null, bool $export = false): Writer
    {
        $line_data = $this->formatter->formatSetterLine($key, $value, $comment, $export);

        return $this->appendLine($line_data);
    }

    public function updateSetter(string $key, string $value = null, string $comment = null, bool $export = false): Writer
    {
        $pattern = "/^(export\h)?\h*{$key}=.*/m";
        $line_data = $this->formatter->formatSetterLine($key, $value, $comment, $export);
        $this->buffer = (string)preg_replace_callback($pattern, static function () use ($line_data) {
            return $line_data;
        }, $this->buffer);

        return $this;
    }

    public function deleteSetter(string $key): Writer
    {
        $pattern = "/^(export\h)?\h*{$key}=.*\n/m";
        $this->buffer = (string)preg_replace($pattern, null, $this->buffer);

        return $this;
    }

    /**
     * @param string $filePath
     * @return $this
     * @throws UnableWriteToFileException
     */
    public function save(string $filePath): Writer
    {
        $this->ensureFileIsWritable($filePath);
        file_put_contents($filePath, $this->buffer);
        return $this;
    }

    /**
     * @param string $filePath
     * @throws UnableWriteToFileException
     */
    protected function ensureFileIsWritable(string $filePath): void
    {
        if ((is_file($filePath) && !is_writable($filePath)) || (!is_file($filePath) && !is_writable(dirname($filePath)))) {
            throw new UnableWriteToFileException(sprintf('Unable to write to the file at %s.', $filePath));
        }
    }
}
