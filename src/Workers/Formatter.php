<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager\Workers;

use NobiDev\EnvManager\Exceptions\InvalidValueException;

/**
 * @package NobiDev\EnvManager\Workers
 */
class Formatter implements \NobiDev\EnvManager\Contracts\Formatter
{
    public function formatSetterLine(string $key, string $value = null, string $comment = null, bool $export = false): string
    {
        $force_quotes = ($comment !== '' && trim((string)$value) === '');
        $value = $this->formatValue($value, $force_quotes);
        $key_formatted = $this->formatKey($key);
        $comment = $this->formatComment($comment);
        $label = $export ? 'export ' : '';
        return "{$label}{$key_formatted}={$value}{$comment}";
    }

    public function formatValue(string $value, bool $force_quotes = false): string
    {
        if (empty($value)) {
            $value = '';
        }

        if (!$force_quotes && !preg_match('/[#\s"\'\\\\]|\\\\n/', $value)) {
            return $value;
        }

        $value = str_replace(['\\', '"'], ['\\\\', '\"'], $value);
        $value = "\"{$value}\"";

        return $value;
    }

    public function formatKey(string $key): string
    {
        return trim((string)str_replace(['export ', '\'', '"', ' '], '', $key));
    }

    public function formatComment(string $comment): string
    {
        $comment = trim($comment, '# ');
        return ($comment !== '') ? " # {$comment}" : '';
    }

    /**
     * @param string $line
     * @return string[]
     * @throws InvalidValueException
     */
    public function parseLine(string $line): array
    {
        $output = [
            'type' => null,
            'export' => null,
            'key' => null,
            'value' => null,
            'comment' => null,
        ];

        if ($this->isEmpty($line)) {
            $output['type'] = 'empty';
        } elseif ($this->isComment($line)) {
            $output['type'] = 'comment';
            $output['comment'] = $this->normaliseComment($line);
        } elseif ($this->looksLikeSetter($line)) {
            [$key_raw, $data_raw] = array_map('trim', explode('=', $line, 2));
            $export = $this->isExportKey($key_raw);
            $key_normalised = $this->normaliseKey($key_raw);
            $data_trimmed = trim((string)$data_raw);

            if (!$data_trimmed && $data_trimmed !== '0') {
                $value = '';
                $comment = '';
            } elseif ($this->beginsWithAQuote($data_trimmed)) { // data starts with a quote
                $quote = $data_trimmed[0];
                $regex_pattern = sprintf(
                    '/^
                    %1$s          # match a quote at the start of the data
                    (             # capturing sub-pattern used
                     (?:          # we do not need to capture this
                      [^%1$s\\\\] # any character other than a quote or backslash
                      |\\\\\\\\   # or two backslashes together
                      |\\\\%1$s   # or an escaped quote e.g \"
                     )*           # as many characters that match the previous rules
                    )             # end of the capturing sub-pattern
                    %1$s          # and the closing quote
                    (.*)$         # and discard any string after the closing quote
                    /mx',
                    $quote
                );

                $value = preg_replace($regex_pattern, '$1', $data_trimmed);
                $extant = preg_replace($regex_pattern, '$2', $data_trimmed);

                $value = $this->normaliseValue($value, $quote);
                $comment = ($this->isComment($extant)) ? $this->normaliseComment($extant) : '';
            } else {
                [$value_raw, $comment_raw] = explode(' #', $data_trimmed, 2);
                $value = $this->normaliseValue($value_raw);
                $comment = (isset($comment_raw)) ? $this->normaliseComment($comment_raw) : '';

                // Unquoted values cannot contain whitespace
                if (preg_match('/\s+/', $value) > 0) {
                    throw new InvalidValueException('Dotenv values containing spaces must be surrounded by quotes.');
                }
            }

            $output['type'] = 'setter';
            $output['export'] = $export;
            $output['key'] = $key_normalised;
            $output['value'] = $value;
            $output['comment'] = $comment;
        } else {
            $output['type'] = 'unknown';
        }

        return $output;
    }

    protected function isEmpty(string $line): bool
    {
        return trim($line) === '';
    }

    protected function isComment(string $line): bool
    {
        return strncmp(ltrim($line), '#', 1) === 0;
    }

    public function normaliseComment(string $comment): string
    {
        return trim($comment, '# ');
    }

    protected function looksLikeSetter(string $line): bool
    {
        return strpos($line, '=') !== false && strncmp($line, '=', 1) !== 0;
    }

    protected function isExportKey(string $key): bool
    {
        $pattern = '/^export\h.*$/';

        if (preg_match($pattern, trim($key))) {
            return true;
        }

        return false;
    }

    public function normaliseKey(string $key): string
    {
        return $this->formatKey($key);
    }

    protected function beginsWithAQuote(string $data): bool
    {
        return strpbrk($data[0], '"\'') !== false;
    }

    public function normaliseValue(string $value, string $quote = ''): string
    {
        if ($quote === '') {
            return trim($value);
        }

        $value = str_replace(["\\$quote", '\\\\'], [$quote, '\\'], $value);

        return $value;
    }
}
