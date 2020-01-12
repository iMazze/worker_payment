# EmptyDescriptionCheckerBundle

A plugin for Kimai 2 which shows you all entries without a description + email notification for users to ask them to add a description

## Requirement

Requires Kamai 2, V1.4 or higher

## Installation

First clone it to your Kimai installation `plugins` directory:

```bash
cd /kimai/var/plugins/
git clone https://gitlab.com/hmr-it/kimai2plugins/EmptyDescriptionCheckerBundle.git
```

And then rebuild the cache: 

```bash
cd /kimai/
bin/console cache:clear
bin/console cache:warmup
```

The plugin should appear now.

## Exclude users, projects, or customers from this plugin

You might want to exclude users, projects or customers from appearing in this plugin. It can be done by providing their id in a config file.
After the first run of the plugin you'll find a dedicated config file in var/data/EmptyDescriptionCheckerBundle/

open the file *_to_exclude.conf in the above mentioned path. Write the id(s) of the user(s), project(s) or customer(s),  in the SECOND line of the config file.

Example of how your users_to_exclude.conf could look like:

```
### DO NOT REMOVE THIS LINE! ### If you want to exclude users from notifying about missing descriptions please put their usersIds separated by , (it is a comma - NOT a semicolon!) in the SECOND line (the line after this line) in this file. You may want to include this file in your backup!
1,2,3
```

with this config the users with id 1, 2, and 3 will not appear in this plugin + they won't be notified about missing descriptions.

## Add a cronjob for email generation

If you want to notify your users on a regular basis you can call this cli command to notify them

```bash
bin/console emptydescriptionchecker:sendmails
```

The above command can be used in a cron job.

## Api Methods

- [GET] /api/empty-description-checker/counter

## Permissions

This bundle ships a new administration screen, which will be available for the following users:

- `ROLE_SUPER_ADMIN` - every super administrator
- `empty_description_checker` - every user that owns this permission
