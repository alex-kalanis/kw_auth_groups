<?php

namespace kalanis\kw_auth_groups\Access;


use kalanis\kw_auth_sources\Access as sources_access;


/**
 * Class Factory
 * @package kalanis\kw_auth_groups\Access
 */
class Factory extends sources_access\Factory
{
    protected function getCompositeSourceInstance(sources_access\SourcesAdapters\AAdapter $adapter): sources_access\CompositeSources
    {
        return new CompositeSources($adapter);
    }
}
