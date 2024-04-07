<?php

namespace kalanis\kw_auth_groups\Access;


use kalanis\kw_auth_sources\Access as sources_access;
use kalanis\kw_auth_sources\AuthSourcesException;


/**
 * Class Factory
 * @package kalanis\kw_auth_groups\Access
 */
class Factory extends sources_access\Factory
{
    /**
     * @param sources_access\SourcesAdapters\AAdapter $adapter
     * @throws AuthSourcesException
     * @return sources_access\CompositeSources
     */
    protected function getCompositeSourceInstance(sources_access\SourcesAdapters\AAdapter $adapter): sources_access\CompositeSources
    {
        return new CompositeSources($adapter);
    }
}
