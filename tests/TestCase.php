<?php

namespace Cboxdk\StatamicSubtitles\Tests;

use Cboxdk\StatamicSubtitles\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
