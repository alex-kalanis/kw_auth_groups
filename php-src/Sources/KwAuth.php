<?php

namespace kalanis\kw_auth_groups\Sources;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_groups\GroupsException;
use kalanis\kw_groups\Interfaces\ISource;


/**
 * Class KwAuth
 * @package kalanis\kw_auth_groups\Sources
 * Process the menu against the file tree
 * Load more already unloaded entries and remove non-existing ones
 */
class KwAuth implements ISource
{
    protected Interfaces\IProcessGroups $lib;

    public function __construct(Interfaces\IProcessGroups $lib)
    {
        $this->lib = $lib;
    }

    public function get(): array
    {
        try {
            $groups = $this->lib->readGroup();
            /** @var array<string, array<int, string>> $result */
            $result = [];
            foreach ($groups as $group) {
                $result[$group->getGroupId()] = $group->getGroupParents();
            }
            return $result;
        } catch (AccountsException $ex) {
            throw new GroupsException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
