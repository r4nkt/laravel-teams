# An opinionated solution for competitive team structures within a game-like atmosphere.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/r4nkt/laravel-teams.svg?style=flat-square)](https://packagist.org/packages/r4nkt/laravel-teams)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/r4nkt/laravel-teams/run-tests?label=tests)](https://github.com/r4nkt/laravel-teams/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/r4nkt/laravel-teams/Check%20&%20fix%20styling?label=code%20style)](https://github.com/r4nkt/laravel-teams/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/r4nkt/laravel-teams.svg?style=flat-square)](https://packagist.org/packages/r4nkt/laravel-teams)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require r4nkt/laravel-teams
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="R4nkt\Teams\TeamsServiceProvider" --tag="laravel-teams-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="R4nkt\Teams\TeamsServiceProvider" --tag="laravel-teams-config"
```

This is the contents of the published config file:

```php
return [

    // ...

];
```

## Usage

### Create Team

Using the Teams facade:

```php
$team = Teams::createTeam('Team Name', $owner);
```

Or, if you use a model that uses `HasTeams`:

```php
$team = $person->createTeam('Team Name'); // $person will be the owner
```

### Delete Team

Using the Teams facade:

```php
$team = Teams::deleteTeam($team);
```

Or, if you use a model that uses `HasTeams`:

```php
$person->deleteTeam($team);
```

Or, using the team itself:

```php
$team->purge();
```

### Transfer Team

Using the Teams facade:

```php
Teams::transferTeam($team, $newOwner);
```

Or, if you use a model that uses `HasTeams`:

```php
$person->transferTeam($team, $newOwner);
```

Or, using the team itself:

```php
$team->transfer($newOwner);
```

### Add Team Member

Using the Teams facade:

```php
Teams::addTeamMember($team, $person);
```

Or, if you use a model that uses `HasTeams`:

```php
$person->joinTeam($team);
```

Or, using the team itself:

```php
$team->addMember($person);
```

### Remove Team Member

Using the Teams facade:

```php
Teams::removeTeamMember($team, $member);
```

Or, if you use a model that uses `HasTeams`:

```php
$person->leaveTeam($team);
```

Or, using the team itself:

```php
$team->removeMember($member);
```

### Invite Team Member

Using the Teams facade:

```php
$invitation = Teams::inviteToTeam($team, $inviter, $person); @todo Reconsider argument order...?
```

Or, if you use a model that uses `HasTeams`:

```php
$invitation = $person->inviteToTeam($team, $person);
```

Or, using the team itself:

```php
$invitation = $team->invite($person);
```

### Accept Invitation

Using the Teams facade:

```php
Teams::acceptTeamInvitation($invitation);
```

Or, if you use a model that uses `HasTeams`:

```php
$person->acceptTeamInvitation($invitation);
```

Or, using the invitation itself:

```php
$invitation->accept();
```

### Reject Invitation

Using the Teams facade:

```php
Teams::rejectTeamInvitation($invitation);
```

Or, if you use a model that uses `HasTeams`:

```php
$person->rejectTeamInvitation($invitation);
```

Or, using the invitation itself:

```php
$invitation->reject();
```

### Delete Invitation

Using the Teams facade:

```php
Teams::deleteTeamInvitation($invitation);
```

Or, if you use a model that uses `HasTeams`:

```php
$person->deleteTeamInvitation($invitation);
```

Or, using the invitation itself:

```php
$invitation->delete();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Travis Elkins](https://github.com/telkins)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
