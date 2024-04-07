<?php

namespace AccessTests;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_auth_groups\Access;
use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_groups\GroupsException;


class CompositeGroupsTest extends ACompositeTest
{
    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupsSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $known = $lib->readGroup();
        $this->assertNotEmpty($known);
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupsAdmin(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $known = $lib->readGroup();
        $this->assertNotEmpty($known);
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupsPlebs(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertEmpty($lib->readGroup());
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupsForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertEmpty($lib->readGroup());
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupDataMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $known = $lib->getGroupDataOnly('2');
        $this->assertNotEmpty($known);
        $this->assertEquals('2', $known->getGroupId());
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupDataSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $known = $lib->getGroupDataOnly('0');
        $this->assertNotEmpty($known);
        $this->assertEquals('0', $known->getGroupId());

        $known = $lib->getGroupDataOnly('1');
        $this->assertNotEmpty($known);
        $this->assertEquals('1', $known->getGroupId());

        $known = $lib->getGroupDataOnly('2');
        $this->assertNotEmpty($known);
        $this->assertEquals('2', $known->getGroupId());
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupDataChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $known = $lib->getGroupDataOnly('0');
        $this->assertEmpty($known);

        $known = $lib->getGroupDataOnly('4');
        $this->assertNotEmpty($known);
        $this->assertEquals('4', $known->getGroupId());
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupDataParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertEmpty($lib->getGroupDataOnly('5'));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupDataForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertEmpty($lib->getGroupDataOnly('5'));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testGetGroupDataUnknown(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertEmpty($lib->getGroupDataOnly('this is not exists'));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testCreateGroupSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertFalse($lib->createGroup(new ExistingGroup())); // exists
        $this->assertTrue($lib->createGroup(new NewGroup())); // ok
        $this->assertFalse($lib->createGroup(new ParentedGroup())); // cyclic
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testCreateGroupAdmin(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertFalse($lib->createGroup(new ExistingGroup()));
        $this->assertTrue($lib->createGroup(new NewGroup()));
        $this->assertFalse($lib->createGroup(new ParentedGroup()));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testCreateGroupAnyone(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->createGroup(new ExistingGroup()));
        $this->assertFalse($lib->createGroup(new NewGroup()));
        $this->assertFalse($lib->createGroup(new ParentedGroup()));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testCreateGroupForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->createGroup(new ExistingGroup()));
        $this->assertFalse($lib->createGroup(new NewGroup()));
        $this->assertFalse($lib->createGroup(new ParentedGroup()));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testUpdateGroupSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->updateGroup(new ExistingGroup())); // exists
        $this->assertFalse($lib->updateGroup(new NewGroup())); // not found
        $this->assertTrue($lib->createGroup(new NewGroup()));
        $this->assertTrue($lib->updateGroup(new NewGroup()));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testUpdateGroupAdmin(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateGroup(new ExistingGroup())); // mine
        $this->assertFalse($lib->updateGroup(new NewGroup())); // not found
        $this->assertTrue($lib->createGroup(new NewGroup()));
        $this->assertTrue($lib->updateGroup(new NewGroup()));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testUpdateGroupAnyone(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->updateGroup(new ExistingGroup()));
        $this->assertFalse($lib->updateGroup(new NewGroup()));
        $this->assertFalse($lib->updateGroup(new ParentedGroup()));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testUpdateGroupForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->updateGroup(new ExistingGroup()));
        $this->assertFalse($lib->updateGroup(new NewGroup()));
        $this->assertFalse($lib->updateGroup(new ParentedGroup()));
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testDeleteGroupSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->deleteGroup('5')); // exists
        $this->assertFalse($lib->deleteGroup('not exists'));
        $this->assertFalse($lib->deleteGroup('2')); // has children, other user
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testDeleteGroupAdmin(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->deleteGroup('5')); // exists, mine
        $this->assertFalse($lib->deleteGroup('not exists'));
        $this->assertFalse($lib->deleteGroup('2')); // has children, other user
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testDeleteGroupAnyone(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->deleteGroup('5')); // exists, other user
        $this->assertFalse($lib->deleteGroup('not exists'));
        $this->assertFalse($lib->deleteGroup('2')); // has children
    }

    /**
     * @throws AccountsException
     * @throws AuthSourcesException
     * @throws GroupsException
     */
    public function testDeleteGroupForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->deleteGroup('5')); // exists, other user
        $this->assertFalse($lib->deleteGroup('not exists'));
        $this->assertFalse($lib->deleteGroup('2')); // has children
    }
}
