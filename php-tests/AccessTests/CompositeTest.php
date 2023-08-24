<?php

namespace AccessTests;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_auth_groups\Access;
use kalanis\kw_auth_sources\Access as sources_access;
use kalanis\kw_auth_sources\Sources;
use kalanis\kw_groups\GroupsException;


class CompositeTest extends ACompositeTest
{
    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testBasic(): void
    {
        $acc = new Sources\Dummy\Accounts();
        $lib = new Access\CompositeSources(new sources_access\SourcesAdapters\Direct($acc, $acc, new Sources\Dummy\Groups(), new Sources\Classes()));
        $this->assertInstanceOf(Access\CompositeSources::class, $lib->getAuth());
        $this->assertInstanceOf(Access\CompositeSources::class, $lib->getAccounts());
        $this->assertInstanceOf(Access\CompositeSources::class, $lib->getGroups());
        $this->assertInstanceOf(Access\CompositeSources::class, $lib->getClasses());

        $lib->setCurrentUser(new \MockUser());

        $this->assertNull($lib->getDataOnly('whatever'));
        $this->assertFalse($lib->updateCertKeys('whatever', null, null));
        $this->assertNull($lib->getCertData('whatever'));

        $this->assertFalse($lib->createAccount(new \MockUser(), 'not important'));
        $this->assertEmpty($lib->readAccounts());
        $this->assertFalse($lib->updateAccount(new \MockUser()));
        $this->assertFalse($lib->updatePassword('whatever', 'not important'));
        $this->assertFalse($lib->deleteAccount('whatever'));

        $this->assertEmpty($lib->readClasses());

        $this->assertFalse($lib->createGroup(new \MockGroup()));
        $this->assertNull($lib->getGroupDataOnly('whatever'));
        $this->assertEmpty($lib->readGroup());
        $this->assertFalse($lib->updateGroup(new \MockGroup()));
        $this->assertFalse($lib->deleteGroup('whatever'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUserSet(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());

        $lib->setCurrentUser(new \MockUser());
        $this->assertInstanceOf(\MockUser::class, $lib->getCurrentUser());

        $lib->setCurrentUser(null);
        $this->expectException(GroupsException::class);
        $lib->getCurrentUser();
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testCerts(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $this->assertInstanceOf(Access\CompositeSources::class, $lib->getAuth());
        $this->assertInstanceOf(Access\CompositeSources::class, $lib->getAccounts());

        $this->assertNull($lib->authenticate('whatever', ['password' => 'any']));
        $this->assertNull($lib->getDataOnly('whatever'));
        $this->assertFalse($lib->updateCertKeys('whatever', null, null));
        $this->assertNull($lib->getCertData('whatever'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testSysUser(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertNull($lib->getDataOnly('whatever'));
        $this->assertFalse($lib->updateCertKeys('whatever', null, null));
        $this->assertNull($lib->getCertData('whatever'));

        $this->assertTrue($lib->createAccount(new \MockUser(), 'not important'));
        $this->assertNotEmpty($lib->readAccounts());
        $this->assertTrue($lib->updateAccount(new \MockUser()));
        $this->assertFalse($lib->updatePassword('whatever', 'not important'));
        $this->assertFalse($lib->deleteAccount('whatever'));
        $this->assertTrue($lib->updatePassword('fool', 'not important'));
        $this->assertTrue($lib->deleteAccount('fool'));

        $this->assertNotEmpty($lib->readClasses());
    }
}
