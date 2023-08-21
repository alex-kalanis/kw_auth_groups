<?php

namespace AccessTests;


use CommonTestClass;
use kalanis\kw_auth_groups\Access;
use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_files\FilesException;
use kalanis\kw_locks\LockException;
use kalanis\kw_paths\PathsException;


class FactoryTest extends CommonTestClass
{
    /**
     * @param $param
     * @throws AuthSourcesException
     * @throws LockException
     * @dataProvider passProvider
     */
    public function testPass($param): void
    {
        $lib = new Access\Factory();
        $this->assertInstanceOf(Access\CompositeSources::class, $lib->getSources($param));
    }

    /**
     * @throws AuthSourcesException
     * @throws FilesException
     * @throws LockException
     * @throws PathsException
     * @return array
     */
    public function passProvider(): array
    {
        return [
            [__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data'],
            [['path' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data']],
        ];
    }
}
