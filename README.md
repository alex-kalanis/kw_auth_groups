# kw_auth_groups

[![Build Status](https://app.travis-ci.com/alex-kalanis/kw_auth_groups.svg?branch=master)](https://app.travis-ci.com/github/alex-kalanis/kw_auth_groups)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/kw_auth_groups/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/kw_auth_groups/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/kw_auth_groups/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_auth_groups)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/kw_auth_groups.svg?v1)](https://packagist.org/packages/alex-kalanis/kw_auth_groups)
[![License](https://poser.pugx.org/alex-kalanis/kw_auth_groups/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_auth_groups)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/kw_auth_groups/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/kw_auth_groups/?branch=master)

Groups using kw_* authentication sources inside the kw_* project.

## PHP Installation

```
{
    "require": {
        "alex-kalanis/kw_auth_groups": ">=3.0"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


## PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Add some external packages with connection to the local or remote services.

3.) Connect the "kalanis\kw_auth_groups\Access\Factory" into your app. Extends it for setting your case.

4.) Extend your libraries by interfaces inside the package.

5.) Just call Factory::getSources and then work over CompositeSources.

## Basic Rules

- Get
  - Group ID equals current one.
  - Group ID is somewhere in the tree of children.

- Add
  - Fails when the currently added group already exists.
  - Fails when the currently added group is already defined within parents of this group.

- Update
  - Fails when the currently updated group is already defined within parents of this group.

- Delete
  - Can delete only when there is no group with processed one as parent.

The group ID is usually string, although it can be integer converted to string before method call.
