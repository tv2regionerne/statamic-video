<?php

namespace Cboxdk\StatamicSubtitles;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        Fieldtypes\Subtitles::class,
    ];
    protected $vite = [
        'input' => [
            'resources/js/addon.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];
    public function bootAddon()
    {
        //
    }
}
