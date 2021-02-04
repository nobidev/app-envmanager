<?php
/*
 * Copyright (c) 2021 NobiDev
 */

declare(strict_types=1);

namespace NobiDev\EnvManager;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider
 * @package NobiDev\EnvManager
 */
class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        parent::register();
        $this->app->bind(Constant::getName(), EnvManager::class);
    }

    public function provides(): array
    {
        return parent::provides() + [
                Constant::getName(),
            ];
    }
}
