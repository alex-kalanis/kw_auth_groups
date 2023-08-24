<?php

namespace AccessTests;


use CommonTestClass;
use kalanis\kw_auth_groups\Access;
use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_locks\LockException;


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
