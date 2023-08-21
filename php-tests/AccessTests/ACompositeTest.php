<?php

namespace AccessTests;


use CommonTestClass;
use kalanis\kw_auth_sources\Data;
use kalanis\kw_auth_sources\Access as sources_access;
use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_auth_sources\Interfaces as sources_interfaces;
use kalanis\kw_auth_sources\Sources;
use kalanis\kw_auth_sources\Hashes;
use MockGroup;
use MockUser;


// proces ja; koho reprezentuji; nekdo mimo
// nektere veci muze delat autor, jine ne
// nektere veci muze delat jen nekdo nezavisly dost vysoko v hearchii

abstract class ACompositeTest extends CommonTestClass
{
    /**
     * @throws AuthSourcesException
     * @return sources_access\SourcesAdapters\AAdapter
     */
    protected function getFilledSources(): sources_access\SourcesAdapters\AAdapter
    {
        return new sources_access\SourcesAdapters\LastInstance(
            new Sources\Memory\AccountsCerts(new Hashes\CoreLib(), $this->filledAccounts()),
            new Sources\Memory\Groups($this->filledGroups()),
            new Sources\Classes()
        );
    }

    /**
     * @return array<sources_interfaces\IUser>
     */
    protected function filledAccounts(): array
    {
        return array_map([$this, 'fillAccount'], [
            ['1000', 'owner', '$2y$10$6-bucFamnK5BTGbojaWw3!HzzHOlUNnN6PF3Y9qHQIdE8FmQKv/eq', 0, 1, 1, 'Owner', '/data/'],
            ['1001', 'manager', '$2y$10$6.bucFamnK5BTGbojaWw3.HpzHOlQUnN6PF3Y9qHQIdE8FmQKv/eq', 1, 2, 1, 'Manage', '/data/'],
            ['1002', 'worker', '$2y$10$6.bucFamnK5BTGbojaWw3.HpzHOlQUnN6PF3Y9qHQIdE8FmQKv/eq', 2, 3, 1, 'Worker', '/data/'],
            ['1003', 'fooz', '$2y$10$6.bucFamnK5BTGbojaWw3.HpzHOlQUnN6PF3Y9qHQIdE8FmQKv/eq', 1, 3, 1, 'Fooz', '/data/'],
            ['1004', 'barz', '$2y$10$6.bucFamnK5BTGbojaWw3.HpzHOlQUnN6PF3Y9qHQIdE8FmQKv/eq', 4, 3, 1, 'Barz', '/data/'],
        ]);
    }

    /**
     * @param array<string|int> $data
     * @return sources_interfaces\IUser
     */
    public function fillAccount(array $data): sources_interfaces\IUser
    {
        $user = new Data\FileCertUser();
        $user->setUserData(
            strval($data[0]),
            strval($data[1]),
            strval($data[3]),
            intval($data[4]),
            intval($data[5]),
            strval($data[6]),
            strval($data[7]),
            ['pw' => strval($data[2])] # to get it later (maybe)
        );
        return $user;
    }

    /**
     * @return array<sources_interfaces\IGroup>
     */
    protected function filledGroups(): array
    {
        return array_map([$this, 'fillGroup'], [
            ['0', 'root', '1000', 'Maintainers', 1, ''],
            ['1', 'admin', '1000', 'Administrators', 1, ''],
            ['2', 'user', '1000', 'All users', 1, ''],
            ['4', 'extra', '1002', 'Extra', 1, '2'],
            ['5', 'test', '1002', 'Test', 1, '2:7'],
        ]);
    }

    /**
     * @param array<string|int> $data
     * @return sources_interfaces\IGroup
     */
    public function fillGroup(array $data): sources_interfaces\IGroup
    {
        $user = new Data\FileGroup();
        $user->setGroupData(
            strval($data[0]),
            strval($data[1]),
            strval($data[3]),
            strval($data[2]),
            intval($data[4]),
            array_filter(explode(':', strval($data[5])))
        );
        return $user;
    }
}


class SysUser extends MockUser
{
    public function getAuthId(): string
    {
        return '123';
    }

    public function getAuthName(): string
    {
        return 'god';
    }

    public function getGroup(): string
    {
        return '1';
    }

    public function getClass(): int
    {
        return 1;
    }

    public function getStatus(): int
    {
        return static::USER_STATUS_ENABLED;
    }
}


class KnownUser extends MockUser
{
    public function getAuthId(): string
    {
        return '1002';
    }

    public function getAuthName(): string
    {
        return 'worker';
    }

    public function getGroup(): string
    {
        return '2';
    }

    public function getClass(): int
    {
        return 2;
    }
}


class KnownUser2 extends MockUser
{
    public function getAuthId(): string
    {
        return '1004';
    }

    public function getAuthName(): string
    {
        return 'barz';
    }

    public function getClass(): int
    {
        return 3;
    }
}


class ExistingGroup extends MockGroup
{
    public function getGroupId(): string
    {
        return '5';
    }

    public function getGroupName(): string
    {
        return 'updated test';
    }

    public function getGroupDesc(): string
    {
        return 'Updated test';
    }

    public function getGroupAuthorId(): string
    {
        return '1002';
    }

    public function getGroupStatus(): int
    {
        return 1;
    }

    public function getGroupParents(): array
    {
        return ['2'];
    }

    public function getGroupExtra(): array
    {
        return [];
    }
}


class NewGroup extends MockGroup
{
    public function getGroupId(): string
    {
        return '6';
    }

    public function getGroupName(): string
    {
        return 'new test';
    }

    public function getGroupDesc(): string
    {
        return 'New test';
    }

    public function getGroupAuthorId(): string
    {
        return '1002';
    }

    public function getGroupStatus(): int
    {
        return 1;
    }

    public function getGroupParents(): array
    {
        return [];
    }

    public function getGroupExtra(): array
    {
        return [];
    }
}


class ParentedGroup extends MockGroup
{
    public function getGroupId(): string
    {
        return '7';
    }

    public function getGroupName(): string
    {
        return 'parented test';
    }

    public function getGroupDesc(): string
    {
        return 'Parented test';
    }

    public function getGroupAuthorId(): string
    {
        return '1002';
    }

    public function getGroupStatus(): int
    {
        return 1;
    }

    public function getGroupParents(): array
    {
        return ['5'];
    }

    public function getGroupExtra(): array
    {
        return [];
    }
}
