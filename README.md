# Laravel Token Blacklist

Enables API tokens to be marked as invalid until natural expiry within Laravel.

This package is designed not to depend on any particular auth token package (with the exception that tokens are strings). However, by default this package will rely on the [antarctica/laravel-auth-token](https://github.com/antarctica/laravel-auth-token) package to provide a working implementation out of the box.

This package is designed not to rely on any particular storage mechanism to record details of blacklisted tokens. However, by default this package will reply on an [Eloquent](http://laravel.com/docs/4.2/eloquent) model with an underlying `blacklisted_token` database. Again this is to provide a working implementation out of the box.

It is possible to provide your own implementation for token handling and for storing details of blacklisted tokens.

## Installing

Require this package in your `composer.json` file:

```json
{
	"require": {
		"antarctica/laravel-token-blacklist": "0.*"
	}
}
```

Run `composer update`.

Register the service provider in the `providers` array of your `app/config/app.php` file:

```php
'providers' => array(

	'Antarctica\LaravelTokenBlacklist\LaravelTokenBlacklistServiceProvider',
	
)
```

### Package dependency note

This package depends on the [Indatus/dispatcher](https://github.com/Indatus/dispatcher) package - which replies on OS level support (enabling a cron job).

As per [BASWEB-114](https://jira.ceh.ac.uk/browse/BASWEB-114) if using the [antarctica/laravel](https://github.com/antarctica/ansible-laravel) Ansible role to provision the underlying infrastructure on which the app using this package is required, it is necessary to require this package in the app `composer.json` file.

i.e.

```json
{
	"require": {
		"indatus/dispatcher": "1.4.*@dev"
	}
}
```

Composer will resolve the package requirements in exactly the same way, this change is only needed so Ansible is aware this package is used and that OS support should be provided for its use.

### Usage

#### Basic usage

These steps assume the use of the default token implementation (provided by the `antarctica/laravel-token-auth` package) and storage implementation (using the bundled Eloquent model). To use alternatives see the *custom usage* section.

Create the required database table using the package migration:

```shell
php artisan migrate --package="antarctica/laravel-token-blacklist"
```

Finished.

##### Ongoing maintenance 

The package will create a scheduled task ran, by default, every day at midnight. This task will automatically clear out any blacklisted tokens that have naturally expired and would be rejected anyway.

To run this maintenance command manually:

```shell
php artisan auth-tokens:delete-expired-blacklisted-tokens
```

#### Custom implementation

You can replace the default token and/or storage implementation as required.

##### Custom *token* implementation

Note: This currently isn't supported, but will be in future versions of the package [See BASWEB-118 for details].

##### Custom *storage* implementation

Custom storage implementations must implement the `TokenBlacklistRepositoryInterface` interface, which is commented and hopefully self descriptive. The `TokenBlacklistRepositoryEloquent` implementation can act as a working example.

Use a custom implementation first publish this package's configuration:

```shell
php artisan config:publish antarctica/laravel-token-blacklist
```

Then set the `repository` key to the custom implementation.

E.g.

```php
'repository' => 'Antarctica\LaravelTokenBlacklist\Repository\TokenBlacklistRepositoryEloquent'
```

## Contributing

This project welcomes contributions, see `CONTRIBUTING` for our general policy.

## Developing

To aid development and keep your local computer clean, a VM (managed by Vagrant) is used to create an isolated environment with all necessary tools/libraries available.

### Requirements

* Mac OS X
* Ansible `brew install ansible`
* [VMware Fusion](http://vmware.com/fusion)
* [Vagrant](http://vagrantup.com) `brew cask install vmware-fusion vagrant`
* [Host manager](https://github.com/smdahlen/vagrant-hostmanager) and [Vagrant VMware](http://www.vagrantup.com/vmware) plugins `vagrant plugin install vagrant-hostmanager && vagrant plugin install vagrant-vmware-fusion`
* You have a private key `id_rsa` and public key `id_rsa.pub` in `~/.ssh/`
* You have an entry like [1] in your `~/.ssh/config`

[1] SSH config entry

```shell
Host bslweb-*
    ForwardAgent yes
    User app
    IdentityFile ~/.ssh/id_rsa
    Port 22
```

### Provisioning development VM

VMs are managed using Vagrant and configured by Ansible.

```shell
$ git clone ssh://git@stash.ceh.ac.uk:7999/basweb/laravel-token-blacklist.git
$ cp ~/.ssh/id_rsa.pub laravel-token-blacklist/provisioning/public_keys/
$ cd laravel-token-blacklist
$ ./armadillo_standin.sh

$ vagrant up

$ ssh bslweb-laravel-token-blacklist-dev-node1
$ cd /app

$ composer install

$ logout
```

### Committing changes

The [Git flow](laravel-token-blacklist) workflow is used to manage development of this package.

Discrete changes should be made within *feature* branches, created from and merged back into *develop* (where small one-line changes may be made directly).

When ready to release a set of features/changes create a *release* branch from *develop*, update documentation as required and merge into *master* with a tagged, [semantic version](http://semver.org/) (e.g. `v1.2.3`).

After releases the *master* branch should be merged with *develop* to restart the process. High impact bugs can be addressed in *hotfix* branches, created from and merged into *master* directly (and then into *develop*).

### Issue tracking

Issues, bugs, improvements, questions, suggestions and other tasks related to this package are managed through the BAS Web & Applications Team Jira project ([BASWEB](https://jira.ceh.ac.uk/browse/BASWEB)).

### Clean up

To remove the development VM:

```shell
vagrant halt
vagrant destroy
```

The `laravel-token-blacklist` directory can then be safely deleted as normal.

## License

Copyright 2015 NERC BAS. Licensed under the MIT license, see `LICENSE` for details.
