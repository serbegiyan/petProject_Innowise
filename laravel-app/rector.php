<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/app', __DIR__.'/routes', __DIR__.'/tests'])
    ->withPhpSets(php84: true)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
    ])
    ->withRules([TypedPropertyFromAssignsRector::class])
    ->withComposerBased(laravel: true);
