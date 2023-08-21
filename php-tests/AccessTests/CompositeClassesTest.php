<?php

namespace AccessTests;


use kalanis\kw_auth_groups\Access;
use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_groups\GroupsException;


class CompositeClassesTest extends ACompositeTest
{
    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetClassesSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $known = $lib->readClasses();
        $this->assertNotEmpty($known);
//        $this->assertEquals('1003', $known->readClasses());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetClassesAdmin(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $known = $lib->readClasses();
        $this->assertNotEmpty($known);
//        $this->assertEquals('1004', $known->readClasses());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetClassesPlebs(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertEmpty($lib->readClasses());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetClassesForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertEmpty($lib->readClasses());
    }
}
