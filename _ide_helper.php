<?php
/*
 * Copyright (c) 2021 NobiDev
 */

/**
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpMissingDocCommentInspection
 * @noinspection EmptyClassInspection
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUnhandledExceptionInspection
 * @noinspection PhpMethodNamingConventionInspection
 * @noinspection PhpClassNamingConventionInspection
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace NobiDev\EnvManager {
    class FacadeImplemented
    {
        protected static EnvManager $instance;

        public static function load(string $filePath = null, bool $restoreIfNotFound = false, string $restorePath = null): EnvManager
        {
            return self::$instance->load($filePath, $restoreIfNotFound, $restorePath);
        }

        public static function getContent(): string
        {
            return self::$instance->getContent();
        }

        public static function getLines(): array
        {
            return self::$instance->getLines();
        }

        public static function getValue(string $key): string
        {
            return self::$instance->getValue($key);
        }

        public static function getKeys(array $list_keys = []): array
        {
            return self::$instance->getKeys($list_keys);
        }

        public static function getBuffer(): string
        {
            return self::$instance->getBuffer();
        }

        public static function addEmpty(): EnvManager
        {
            return self::$instance->addEmpty();
        }

        public static function addComment(string $comment): EnvManager
        {
            return self::$instance->addComment($comment);
        }

        public static function setKey(string $key, string $value = null, string $comment = null, bool $export = false): EnvManager
        {
            return self::$instance->setKey($key, $value, $comment, $export);
        }

        public static function setKeys(array $data): EnvManager
        {
            return self::$instance->setKeys($data);
        }

        public static function keyExists(string $key): bool
        {
            return self::$instance->keyExists($key);
        }

        public static function deleteKey(string $key): EnvManager
        {
            return self::$instance->deleteKey($key);
        }

        public static function deleteKeys(array $keys = []): EnvManager
        {
            return self::$instance->deleteKeys($keys);
        }

        public static function save(): EnvManager
        {
            return self::$instance->save();
        }
    }
}

namespace {

    use NobiDev\EnvManager\FacadeImplemented;

    class EnvManager extends FacadeImplemented
    {
    }
}
