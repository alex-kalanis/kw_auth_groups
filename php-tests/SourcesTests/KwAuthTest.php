<?php

namespace SourcesTests;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Data\FileGroup;
use kalanis\kw_accounts\Interfaces\IProcessGroups;
use kalanis\kw_accounts\Interfaces\IGroup;
use kalanis\kw_auth_groups\Sources\KwAuth;
use kalanis\kw_groups\GroupsException;


class KwAuthTest extends \CommonTestClass
{
    /**
     * @throws GroupsException
     */
    public function testSimple(): void
    {
        $lib = new KwAuth(new XAccessGroups());
        $this->assertEquals([
            '1' => [],
            '2' => [],
            '3' => ['1'],
            '4' => ['1'],
            '5' => ['2', '4'],
        ], $lib->get());
    }

    /**
     * @throws GroupsException
     */
    public function testSimpleFail(): void
    {
        $lib = new KwAuth(new XFailedGroups());
        $this->expectException(GroupsException::class);
        $lib->get();
    }
}


/**
 * Class XAccessGroups
 * @package SourcesTests
 *
 * Basic group tree:
 *  |
 *  o--o----->  root
 *  |  o----->  base
 *  |  o----->  sys
 *  o--+----->  admin
 *     o--o-->  extra
 *
 * -> extra is in both admin and root group
 */
class XAccessGroups implements IProcessGroups
{
    /** @var IGroup[] */
    protected $internal = [];

    public function __construct()
    {
        $grp1 = new FileGroup();
        $grp1->setGroupData('1', 'root', 'root', '1', 1, []);
        $this->internal[] = $grp1;

        $grp2 = new FileGroup();
        $grp2->setGroupData('2', 'admin', 'admin', '1', 1, []);
        $this->internal[] = $grp2;

        $grp3 = new FileGroup();
        $grp3->setGroupData('3', 'base', 'under', '1', 1, ['1']);
        $this->internal[] = $grp3;

        $grp4 = new FileGroup();
        $grp4->setGroupData('4', 'sys', 'sys', '1', 1, ['1']);
        $this->internal[] = $grp4;

        $grp5 = new FileGroup();
        $grp5->setGroupData('5', 'extra', 'extra', '1', 1, ['2', '4']);
        $this->internal[] = $grp5;
    }

    public function createGroup(IGroup $group): bool
    {
        foreach ($this->internal as $item) {
            if ($group->getGroupId() == $item->getGroupId()) {
                return false;
            }
        }
        $this->internal[] = $group;
        return true;
    }

    public function getGroupDataOnly(string $groupId): ?IGroup
    {
        foreach ($this->internal as $item) {
            if ($groupId == $item->getGroupId()) {
                return $item;
            }
        }
        return null;
    }

    public function readGroup(): array
    {
        return $this->internal;
    }

    public function updateGroup(IGroup $group): bool
    {
        foreach ($this->internal as $key => $item) {
            if ($group->getGroupId() == $item->getGroupId()) {
                $this->internal[$key] = $group;
                return true;
            }
        }
        return false;
    }

    public function deleteGroup(string $groupId): bool
    {
        foreach ($this->internal as $key => $item) {
            if ($groupId == $item->getGroupId()) {
                unset($this->internal[$key]);
                return true;
            }
        }
        return false;
    }
}


class XFailedGroups implements IProcessGroups
{
    public function createGroup(IGroup $group): bool
    {
        throw new AccountsException('mock');
    }

    public function getGroupDataOnly(string $groupId): ?IGroup
    {
        throw new AccountsException('mock');
    }

    public function readGroup(): array
    {
        throw new AccountsException('mock');
    }

    public function updateGroup(IGroup $group): bool
    {
        throw new AccountsException('mock');
    }

    public function deleteGroup(string $groupId): bool
    {
        throw new AccountsException('mock');
    }
}
