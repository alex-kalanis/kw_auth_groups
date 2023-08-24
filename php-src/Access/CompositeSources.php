<?php

namespace kalanis\kw_auth_groups\Access;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_auth_sources\Access as source_access;
use kalanis\kw_auth_groups\Sources\KwAuth;
use kalanis\kw_groups\GroupsException;
use kalanis\kw_groups\Interfaces\IProcessor;
use kalanis\kw_groups\Processor\Basic;


/**
 * Class CompositeSources
 * @package kalanis\kw_auth_groups\Access
 *
 * todo: next step: separate user rights themselves and system consistency checks
 */
class CompositeSources extends source_access\CompositeSources
{
    /** @var IProcessor */
    protected $libGroup = null;
    /** @var Interfaces\IUser|null */
    protected $currentUser = null;

    public function __construct(source_access\SourcesAdapters\AAdapter $adapter, ?IProcessor $libGroup = null)
    {
        parent::__construct($adapter);
        $this->libGroup = $libGroup ?: new Basic(new KwAuth($adapter->getGroups()));
    }

    /**
     * @param string $userName
     * @throws AccountsException
     * @throws GroupsException
     * @return Interfaces\IUser|null
     */
    public function getDataOnly(string $userName): ?Interfaces\IUser
    {
        $data = parent::getDataOnly($userName);
        return $data && $this->canAccessUser($data)
            ? $data
            : null
        ;
    }

    /**
     * @param string $userName
     * @throws AccountsException
     * @throws GroupsException
     * @return Interfaces\IUserCert|null
     */
    public function getCertData(string $userName): ?Interfaces\IUserCert
    {
        $data = parent::getCertData($userName);
        return $data && $this->canAccessUser($data)
            ? $data
            : null
        ;
    }

    /**
     * @param Interfaces\IUser $user
     * @param string $password
     * @throws AccountsException
     * @throws GroupsException
     * @return bool
     */
    public function createAccount(Interfaces\IUser $user, string $password): bool
    {
//print_r(['data', intval($this->isMe($user)), intval($this->accessUserByClass($user)), intval($this->currentUserIsRepresented($user)), intval($this->currentUserRepresents($user)), ]);
        return $this->canAccessUserByClassId($user->getClass()) && !$this->currentUserRepresents($user)
            ? parent::createAccount($user, $password)
            : false
        ;
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     * @return Interfaces\IUser[]
     */
    public function readAccounts(): array
    {
        $availableAccounts = [];
        /** @var Interfaces\IUser[] $allAccounts */
        $allAccounts = parent::readAccounts();
        foreach ($allAccounts as $account) {
            if ($this->canAccessUserByClassId($account->getClass()) && $this->isCurrentUserRepresented($account)) {
                $availableAccounts[] = $account;
            }
        }
        return $availableAccounts;
    }

    /**
     * @param Interfaces\IUser $user
     * @throws AccountsException
     * @throws GroupsException
     * @return bool
     */
    public function updateAccount(Interfaces\IUser $user): bool
    {
        return $this->canAccessUser($user)
            ? parent::updateAccount($user)
            : false
        ;
    }

    /**
     * @param string $userName
     * @param string $passWord
     * @throws AccountsException
     * @throws GroupsException
     * @return bool
     */
    public function updatePassword(string $userName, string $passWord): bool
    {
        $user = parent::getDataOnly($userName);
        return $user && $this->canAccessUser($user)
            ? parent::updatePassword($userName, $passWord)
            : false
        ;
    }

    /**
     * @param string $userName
     * @param string|null $certKey
     * @param string|null $certSalt
     * @throws AccountsException
     * @throws GroupsException
     * @return bool
     */
    public function updateCertKeys(string $userName, ?string $certKey, ?string $certSalt): bool
    {
        $user = $this->getDataOnly($userName);
        return $user && $this->canAccessUser($user)
            ? parent::updateCertKeys($userName, $certKey, $certSalt)
            : false
        ;
    }

    /**
     * @param string $userName
     * @throws AccountsException
     * @throws GroupsException
     * @return bool
     */
    public function deleteAccount(string $userName): bool
    {
        $user = parent::getDataOnly($userName);
        return $user && $this->canAccessUserByClassId($user->getClass()) && !$this->currentUserRepresents($user)
            ? parent::deleteAccount($userName)
            : false
        ;
    }

    /**
     * All lower classes than user has
     * @throws GroupsException
     * @return array<int, string>
     */
    public function readClasses(): array
    {
        $availableClasses = [];
        foreach (parent::readClasses() as $classId => $className) {
            if ($this->canAccessUserByClassId($classId)) {
                $availableClasses[$classId] = $className;
            }
        }
        return $availableClasses;
    }

    /**
     * @param Interfaces\IGroup $group
     * @throws AccountsException
     * @throws GroupsException
     * @return bool
     */
    public function createGroup(Interfaces\IGroup $group): bool
    {
        $reGroup = clone $group;
        $reGroup->setGroupData(
            $group->getGroupId(),
            $group->getGroupName(),
            $group->getGroupDesc(),
            $this->getCurrentUser()->getAuthId(),
            $group->getGroupStatus(),
            $group->getGroupParents(),
            $group->getGroupExtra()
        );

        return ($this->canAccessGroup($reGroup) && !$this->isAlreadyInParents($reGroup))
            ? parent::createGroup($reGroup)
            : false
        ;
    }

    /**
     * @param string $groupId
     * @throws AccountsException
     * @throws GroupsException
     * @return Interfaces\IGroup|null
     */
    public function getGroupDataOnly(string $groupId): ?Interfaces\IGroup
    {
        $thisGroup = null;
        foreach (parent::readGroup() as $group) {
            /** @var Interfaces\IGroup $group */
            if ($group->getGroupId() == $groupId) {
                $thisGroup = $group;
                break;
            }
        }
        if (!$thisGroup) {
            return null;
        }

        return ($this->canAccessGroup($thisGroup))
            ? $thisGroup
            : null
        ;
    }

    /**
     * @throws AccountsException
     * @throws GroupsException
     * @return Interfaces\IGroup[]
     */
    public function readGroup(): array
    {
        $availableGroups = [];
        foreach (parent::readGroup() as $group) {
            /** @var Interfaces\IGroup $group */
            if ($this->canAccessGroup($group)) {
                $availableGroups[] = $group;
            }
        }
        return $availableGroups;
    }

    /**
     * @param Interfaces\IGroup $group
     * @throws AccountsException
     * @throws GroupsException
     * @return bool
     */
    public function updateGroup(Interfaces\IGroup $group): bool
    {
        return $this->canAccessGroup($group) && !$this->isAlreadyInParents($group)
            ? parent::updateGroup($group)
            : false
        ;
    }

    /**
     * @param string $groupId
     * @throws AccountsException
     * @throws GroupsException
     * @return bool
     */
    public function deleteGroup(string $groupId): bool
    {
        $group = $this->getGroupDataOnly($groupId);
        return $group && !$this->hasChildren($group)
            ? parent::deleteGroup($groupId)
            : false
        ;
    }

    public function getAuth(): Interfaces\IAuth
    {
        return $this;
    }

    public function getAccounts(): Interfaces\IProcessAccounts
    {
        return $this;
    }

    public function getGroups(): Interfaces\IProcessGroups
    {
        return $this;
    }

    public function getClasses(): Interfaces\IProcessClasses
    {
        return $this;
    }

    public function setCurrentUser(?Interfaces\IUser $user): self
    {
        $this->currentUser = $user;
        return $this;
    }

    /**
     * @throws GroupsException
     * @return Interfaces\IUser
     */
    public function getCurrentUser(): Interfaces\IUser
    {
        if (empty($this->currentUser)) {
            throw new GroupsException('Set Current User first!');
        }
        return $this->currentUser;
    }

    /**
     * @param Interfaces\IUser $user
     * @throws GroupsException
     * @return bool
     */
    protected function canAccessUser(Interfaces\IUser $user): bool
    {
        return $this->isMe($user) || $this->canAccessUserByClassId($user->getClass()) || $this->isCurrentUserRepresented($user);
    }

    /**
     * Aktualni Zastupuje dodavaneho
     * Aktualni je potomek dodavaneho
     * @param Interfaces\IUser $data
     * @throws GroupsException
     * @return bool
     */
    protected function currentUserRepresents(Interfaces\IUser $data): bool
    {
//print_r(['grp child', $this->getCurrentUser()->getGroup(), $data->getGroup(), intval($this->libGroup->canAccess($this->getCurrentUser()->getGroup(), $data->getGroup()))]);
        return $this->libGroup->canAccess($this->getCurrentUser()->getGroup(), $data->getGroup());
    }

    /**
     * Akntualni Je zastupovan dodanym
     * Aktualni je rodic dodavaneho
     * @param Interfaces\IUser $data
     * @throws GroupsException
     * @return bool
     */
    protected function isCurrentUserRepresented(Interfaces\IUser $data): bool
    {
//print_r(['grp parent', $this->getCurrentUser()->getGroup(), $data->getGroup(), intval($this->libGroup->canAccess($data->getGroup(), $this->getCurrentUser()->getGroup()))]);
        return $this->libGroup->canAccess($data->getGroup(), $this->getCurrentUser()->getGroup());
    }

    /**
     * @param int $class
     * @throws GroupsException
     * @return bool
     */
    protected function canAccessUserByClassId(int $class): bool
    {
        return $this->getCurrentUser()->getClass() < $class;
    }

    /**
     * @param Interfaces\IUser $data
     * @throws GroupsException
     * @return bool
     */
    protected function isMe(Interfaces\IUser $data): bool
    {
        return $this->getCurrentUser()->getAuthId() == $data->getAuthId();
    }

    /**
     * @param Interfaces\IGroup $data
     * @throws GroupsException
     * @return bool
     */
    protected function canAccessGroup(Interfaces\IGroup $data): bool
    {
        return $this->isMaintainer() || $this->canProcess($data);
    }

    /**
     * @throws GroupsException
     * @return bool
     */
    protected function isMaintainer(): bool
    {
        return Interfaces\IProcessClasses::CLASS_MAINTAINER == $this->getCurrentUser()->getClass();
    }

    /**
     * @param Interfaces\IGroup $group
     * @throws GroupsException
     * @return bool
     */
    protected function canProcess(Interfaces\IGroup $group): bool
    {
        $current = $this->getCurrentUser();
        return
            Interfaces\IProcessClasses::CLASS_ADMIN == $current->getClass()
            && (
                $group->getGroupAuthorId() == $current->getAuthId()
                || (
                    $group->getGroupId()
                    && $this->libGroup->canAccess($group->getGroupId(), $current->getGroup())
                )
            )
        ;
    }

    /**
     * @param Interfaces\IGroup $group
     * @throws GroupsException
     * @return bool
     */
    protected function isAlreadyInParents(Interfaces\IGroup $group): bool
    {
        foreach ($group->getGroupParents() as $groupParent) {
            if ($this->libGroup->canAccess($groupParent, $group->getGroupId())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Interfaces\IGroup $group
     * @throws AccountsException
     * @return bool
     */
    protected function hasChildren(Interfaces\IGroup $group): bool
    {
        $allGroups = $this->adapter->getGroups()->readGroup();
        foreach ($allGroups as $sourceGroup) {
            if (in_array($group->getGroupId(), $sourceGroup->getGroupParents())) {
                return true;
            }
        }
        return false;
    }
}
