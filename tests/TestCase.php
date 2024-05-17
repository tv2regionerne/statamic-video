<?php

namespace Tv2regionerne\StatamicVideo\Tests;

use Statamic\Testing\AddonTestCase;
use Tv2regionerne\StatamicVideo\ServiceProvider;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
