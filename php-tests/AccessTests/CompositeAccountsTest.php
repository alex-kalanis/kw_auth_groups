<?php

namespace AccessTests;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_auth_groups\Access;
use kalanis\kw_groups\GroupsException;


class CompositeAccountsTest extends ACompositeTest
{
    /**
     * @throws AccountsException
     * @throws GroupsException
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
     * @throws AccountsException
     * @throws GroupsException
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
     * @throws AccountsException
     * @throws GroupsException
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
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testGetDataParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertEmpty($lib->getDataOnly('worker'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testGetDataForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertEmpty($lib->getDataOnly('fooz'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
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
     * @throws AccountsException
     * @throws GroupsException
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
     * @throws AccountsException
     * @throws GroupsException
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
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testGetCertDataParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertEmpty($lib->getCertData('worker'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testGetCertDataForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertEmpty($lib->getCertData('fooz'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testCreateUserSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->createAccount(new \MockUser(), 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testCreateUserAnyoneElse(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->createAccount(new \MockUser(), 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
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
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testReadAccountsAnyoneElse(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $known = $lib->readAccounts();
        $this->assertEmpty($known);
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateAccountMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateAccount(new KnownUser()));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateAccountSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->updateAccount(new KnownUser()));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateAccountChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateAccount(new KnownUser2()));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateAccountParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->updateAccount(new KnownUser()));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateAccountForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->updateAccount(new SysUser()));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdatePasswordMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updatePassword('worker', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdatePasswordSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->updatePassword('worker', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdatePasswordChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updatePassword('barz', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdatePasswordParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->updatePassword('worker', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdatePasswordForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->updatePassword('worker', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateCertsMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateCertKeys('worker', 'not important', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateCertsSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->updateCertKeys('fooz', 'not important', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateCertsChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->updateCertKeys('barz', 'not important', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateCertsParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->updateCertKeys('worker', 'not important', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testUpdateCertsForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->updateCertKeys('fooz', 'not important', 'not important'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testDeleteAccountMine(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertFalse($lib->deleteAccount('worker'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testDeleteAccountSystem(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new SysUser());

        $this->assertTrue($lib->deleteAccount('worker'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testDeleteAccountChildren(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser());

        $this->assertTrue($lib->deleteAccount('barz'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testDeleteAccountParent(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new KnownUser2());

        $this->assertFalse($lib->deleteAccount('worker'));
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     */
    public function testDeleteAccountForeigner(): void
    {
        $lib = new Access\CompositeSources($this->getFilledSources());
        $lib->setCurrentUser(new \MockUser());

        $this->assertFalse($lib->deleteAccount('worker'));
    }
}
