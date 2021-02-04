<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager;

use Illuminate\Contracts\Container\Container;
use NobiDev\EnvManager\Contracts\EnvManager as EnvManagerContract;
use NobiDev\EnvManager\Exceptions\KeyNotFoundException;
use NobiDev\EnvManager\Workers\Formatter;
use NobiDev\EnvManager\Workers\Reader;
use NobiDev\EnvManager\Workers\Writer;
use function array_key_exists;
use function in_array;
use function is_array;
use function is_string;

/**
 * Class EnvManager
 * @package NobiDev\EnvManager
 */
class EnvManager implements EnvManagerContract
{
    protected Container $application;
    protected Formatter $formatter;
    protected Reader $reader;
    protected Writer $writer;
    protected ?string $filePath;

    /**
     * EnvManager constructor.
     * @param Container $app
     * @throws Exceptions\UnableReadFileException
     */
    public function __construct(Container $app)
    {
        $this->application = $app;
        $this->formatter = new Formatter();
        $this->reader = new Reader($this->formatter);
        $this->writer = new Writer($this->formatter);
        $this->load();
    }

    /**
     * @param string|null $filePath
     * @param bool $restoreIfNotFound
     * @param string|null $restorePath
     * @return $this
     * @throws Exceptions\UnableReadFileException
     */
    public function load(string $filePath = null, bool $restoreIfNotFound = false, string $restorePath = null): EnvManager
    {
        $this->resetContent();

        if ($filePath !== null) {
            $this->filePath = $filePath;
        } elseif (method_exists($this->application, 'environmentPath') && method_exists($this->application, 'environmentFile')) {
            $this->filePath = $this->application->environmentPath() . '/' . $this->application->environmentFile();
        } else {
            $this->filePath = __DIR__ . '/../../../../../../.env';
        }

        $this->reader->load($this->filePath);

        if (file_exists($this->filePath)) {
            $this->writer->setBuffer($this->getContent());

            return $this;
        }
        return $this;
    }

    protected function resetContent(): void
    {
        $this->filePath = null;
        $this->reader->load(null);
        $this->writer->setBuffer(null);
    }

    /**
     * @return string
     * @throws Exceptions\UnableReadFileException
     */
    public function getContent(): string
    {
        return $this->reader->content();
    }

    /**
     * @return array
     * @throws Exceptions\UnableReadFileException
     */
    public function getLines(): array
    {
        return $this->reader->lines();
    }

    /**
     * @param string $key
     * @return string
     * @throws KeyNotFoundException|Exceptions\UnableReadFileException
     */
    public function getValue(string $key): string
    {
        $all_keys = $this->getKeys([$key]);

        if (array_key_exists($key, $all_keys)) {
            return $all_keys[$key]['value'];
        }

        throw new KeyNotFoundException('Requested key not found in your file.');
    }

    /**
     * @param array $list_keys
     * @return array
     * @throws Exceptions\UnableReadFileException
     */
    public function getKeys(array $list_keys = []): array
    {
        $all_keys = $this->reader->keys();

        return array_filter($all_keys, static function ($key) use ($list_keys) {
            if (!empty($list_keys)) {
                return in_array($key, $list_keys, true);
            }

            return true;
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getBuffer(): string
    {
        return $this->writer->getBuffer();
    }

    public function addEmpty(): EnvManager
    {
        $this->writer->appendEmptyLine();
        return $this;
    }

    public function addComment(string $comment): EnvManager
    {
        $this->writer->appendCommentLine($comment);
        return $this;
    }

    /**
     * @param string $key
     * @param string|null $value
     * @param string|null $comment
     * @param bool $export
     * @return $this
     * @throws Exceptions\UnableReadFileException
     */
    public function setKey(string $key, string $value = null, string $comment = null, bool $export = false): EnvManager
    {
        $data_compacted = [compact('key', 'value', 'comment', 'export')];
        return $this->setKeys($data_compacted);
    }

    /**
     * @param array $data
     * @return $this
     * @throws Exceptions\UnableReadFileException
     */
    public function setKeys(array $data): EnvManager
    {
        foreach ($data as $data_key => $setter) {
            if (!is_array($setter)) {
                if (!is_string($data_key)) {
                    continue;
                }
                $setter = [
                    'key' => $data_key,
                    'value' => $setter,
                ];
            }
            if (array_key_exists('key', $setter)) {
                $data_key = $this->formatter->formatKey($setter['key']);
                $value = $setter['value'] ?? null;
                $comment = $setter['comment'] ?? null;
                $export = array_key_exists('export', $setter) ? $setter['export'] : false;

                if (!is_file($this->filePath) || !$this->keyExists($data_key)) {
                    $this->writer->appendSetter($data_key, $value, $comment, $export);
                } else {
                    $old_info = $this->getKeys([$data_key]);
                    $comment = $comment ?? $old_info[$data_key]['comment'];

                    $this->writer->updateSetter($data_key, $value, $comment, $export);
                }
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     * @throws Exceptions\UnableReadFileException
     */
    public function keyExists(string $key): bool
    {
        $all_keys = $this->getKeys();

        return array_key_exists($key, $all_keys);
    }

    public function deleteKey(string $key): EnvManager
    {
        $all_keys = [$key];
        return $this->deleteKeys($all_keys);
    }

    public function deleteKeys(array $keys = []): EnvManager
    {
        foreach ($keys as $delete_key) {
            $this->writer->deleteSetter($delete_key);
        }
        return $this;
    }

    /**
     * @return $this
     * @throws Exceptions\UnableWriteToFileException
     */
    public function save(): EnvManager
    {
        $this->writer->save($this->filePath);
        return $this;
    }
}
