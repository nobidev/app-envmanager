<?php

declare(strict_types=1);

namespace NobiDev\LibraryStarter;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class Facade
 * @package NobiDev\LibraryStarter
 */
class Facade extends BaseFacade
{
    protected static function getFacadeAccessor(): string
    {
        return Constant::getName();
    }
}
