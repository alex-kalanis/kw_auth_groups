<?php

namespace AccessTests;


use kalanis\kw_auth_groups\Access;
use kalanis\kw_auth_sources\Interfaces;
use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_groups\GroupsException;
use kalanis\kw_locks\LockException;


class CompositeAccountsTest extends ACompositeTest
{
    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetDataMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $known = $lib->getDataOnly('worker');
        $this->assertNotEmpty($known);
        $this->assertEquals('1002', $known->getAuthId());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetDataSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $known = $lib->getDataOnly('fooz');
        $this->assertNotEmpty($known);
        $this->assertEquals('1003', $known->getAuthId());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetDataChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $known = $lib->getDataOnly('barz');
        $this->assertNotEmpty($known);
        $this->assertEquals('1004', $known->getAuthId());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetDataParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertEmpty($lib->getDataOnly('worker'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetDataForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertEmpty($lib->getDataOnly('fooz'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetCertDataMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $known = $lib->getCertData('worker');
        $this->assertNotEmpty($known);
        $this->assertEquals('1002', $known->getAuthId());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetCertDataSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $known = $lib->getCertData('fooz');
        $this->assertNotEmpty($known);
        $this->assertEquals('1003', $known->getAuthId());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetCertDataChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $known = $lib->getCertData('barz');
        $this->assertNotEmpty($known);
        $this->assertEquals('1004', $known->getAuthId());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetCertDataParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertEmpty($lib->getCertData('worker'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testGetCertDataForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertEmpty($lib->getCertData('fooz'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testCreateUserSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->createAccount(new \MockUser(), 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testCreateUserAnyoneElse(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->createAccount(new \MockUser(), 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testReadAccountsSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $known = $lib->readAccounts();
        $this->assertNotEmpty($known);

        usort($known, [$this, 'sortAccountsById']);

        $entry = reset($known);
        /** @var Interfaces\IUser $entry */
        $this->assertNotEmpty($entry);
        $this->assertEquals('1001', $entry->getAuthId());

        $entry = next($known);
        $this->assertNotEmpty($entry);
        $this->assertEquals('1003', $entry->getAuthId());

        $entry = next($known);
        $this->assertEmpty($entry);
    }

    public function sortAccountsById(Interfaces\IUser $acc1, Interfaces\IUser $acc2): int
    {
        return intval($acc1->getAuthId()) <=> intval($acc2->getAuthId());
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testReadAccountsAnyoneElse(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $known = $lib->readAccounts();
        $this->assertEmpty($known);
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateAccountMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateAccount(new KnownUser()));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateAccountSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->updateAccount(new KnownUser()));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateAccountChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateAccount(new KnownUser2()));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateAccountParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->updateAccount(new KnownUser()));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateAccountForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->updateAccount(new SysUser()));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdatePasswordMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updatePassword('worker', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdatePasswordSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->updatePassword('worker', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdatePasswordChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updatePassword('barz', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdatePasswordParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->updatePassword('worker', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdatePasswordForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->updatePassword('worker', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateCertsMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateCertKeys('worker', 'not important', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateCertsSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->updateCertKeys('fooz', 'not important', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateCertsChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateCertKeys('barz', 'not important', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateCertsParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->updateCertKeys('worker', 'not important', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testUpdateCertsForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->updateCertKeys('fooz', 'not important', 'not important'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testDeleteAccountMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertFalse($lib->deleteAccount('worker'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testDeleteAccountSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->deleteAccount('worker'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testDeleteAccountChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->deleteAccount('barz'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testDeleteAccountParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->deleteAccount('worker'));
    }

    /**
     * @throws AuthSourcesException
     * @throws GroupsException
     * @throws LockException
     */
    public function testDeleteAccountForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->deleteAccount('worker'));
    }
}
