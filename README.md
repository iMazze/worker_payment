# WorkerPayment

A plugin for Kimai 2 which xxx
## Requirement

Requires Kamai 2, V1.4 or higher

## Installation

First clone it to your Kimai installation `plugins` directory:

```bash
cd /kimai/var/plugins/
git clone https://github.com/iMazze/WorkerPayment.git
```

And then rebuild the cache: 

```bash
cd /kimai/
bin/console cache:clear
bin/console cache:warmup
```

The plugin should appear now.


## Permissions

This bundle ships a new administration screen, which will be available for the following users:

- `ROLE_SUPER_ADMIN` - every super administrator
- `WorkerPayment` - every user that owns this permission
