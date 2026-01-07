<?php

declare(strict_types=1);

namespace Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use Codeat3\BladePhosphorIcons\BladePhosphorIconsServiceProvider;
use Livewire\LivewireServiceProvider;
use Mckenziearts\LivewireMarkdownEditor\LivewireMarkdownEditorServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BladeIconsServiceProvider::class,
            BladePhosphorIconsServiceProvider::class,
            LivewireServiceProvider::class,
            LivewireMarkdownEditorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }
}
