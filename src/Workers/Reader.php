<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager\Workers;

use NobiDev\EnvManager\Contracts\Formatter as FormatterContract;
use NobiDev\EnvManager\Contracts\Reader as ReaderContract;
use NobiDev\EnvManager\Exceptions\UnableReadFileException;

/**
 * Class Reader
 * @package NobiDev\EnvManager\Workers
 */
class Reader implements ReaderContract
{
    protected ?string $filePath;
    protected FormatterContract $formatter;

    public function __construct(FormatterContract $formatter)
    {
        $this->formatter = $formatter;
    }

    public function load(?string $filePath): Reader
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return string
     * @throws UnableReadFileException
     */
    public function content(): string
    {
        $this->ensureFileIsReadable();
        return file_get_contents($this->filePath);
    }

    /**
     * @throws UnableReadFileException
     * @noinspection PhpMethodNamingConventionInspection
     */
    protected function ensureFileIsReadable(): void
    {
        if (!is_readable($this->filePath) || !is_file($this->filePath)) {
            throw new UnableReadFileException(sprintf('Unable to read the file at %s.', $this->filePath));
        }
    }

    /**
     * @throws UnableReadFileException
     */
    public function lines(): array
    {
        $content = [];
        $lines = $this->readLinesFromFile();

        foreach ($lines as $row_index => $line_data) {
            $content[] = [
                'line' => $row_index + 1,
                'raw_data' => $line_data,
                'parsed_data' => $this->formatter->parseLine($line_data)
            ];
        }

        return $content;
    }

    /**
     * @throws UnableReadFileException
     * @noinspection PhpMethodNamingConventionInspection
     */
    protected function readLinesFromFile(): array
    {
        $this->ensureFileIsReadable();

        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        return $lines;
    }

    /**
     * @throws UnableReadFileException
     */
    public function keys(): array
    {
        $content = [];
        $lines = $this->readLinesFromFile();

        foreach ($lines as $row_index => $line_data) {
            $data_parsed = $this->formatter->parseLine($line_data);

            if ($data_parsed['type'] === 'setter') {
                $content[$data_parsed['key']] = [
                    'line' => $row_index + 1,
                    'export' => $data_parsed['export'],
                    'value' => $data_parsed['value'],
                    'comment' => $data_parsed['comment']
                ];
            }
        }

        return $content;
    }
}
