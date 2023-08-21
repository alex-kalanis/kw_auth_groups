<?php

use kalanis\kw_auth_sources\Interfaces as sources_interfaces;
use PHPUnit\Framework\TestCase;


/**
 * Class CommonTestClass
 * The structure for mocking and configuration seems so complicated, but it's necessary to let it be totally idiot-proof
 */
class CommonTestClass extends TestCase
{
}


class MockUser implements sources_interfaces\IUser
{
    public function setUserData(?string $authId, ?string $authName, ?string $authGroup, ?int $authClass, ?int $authStatus, ?string $displayName, ?string $dir, ?array $extra = []): void
    {
    }

    public function getAuthId(): string
    {
        return '654';
    }

    public function getAuthName(): string
    {
        return 'fool';
    }

    public function getGroup(): string
    {
        return '4';
    }

    public function getClass(): int
    {
        return 999;
    }

    public function getStatus(): int
    {
        return static::USER_STATUS_ENABLED;
    }

    public function getDisplayName(): string
    {
        return 'FooL';
    }

    public function getDir(): string
    {
        return 'not_available\\:///';
    }

    public function getExtra(): array
    {
        return [];
    }
}


class MockGroup implements sources_interfaces\IGroup
{
    public function setGroupData(?string $id, ?string $name, ?string $desc, ?string $authorId, ?int $status, ?array $parents = [], ?array $extra = []): void
    {
    }

    public function getGroupId(): string
    {
        return 'bazbazbaz';
    }

    public function getGroupName(): string
    {
        return 'FOO';
    }

    public function getGroupDesc(): string
    {
        return 'bar';
    }

    public function getGroupAuthorId(): string
    {
        return '1000';
    }

    public function getGroupStatus(): int
    {
        return 999;
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
