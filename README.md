<p align="center">
    <a href="https://sylius.com" target="_blank">
        <picture>
          <source media="(prefers-color-scheme: dark)" srcset="https://media.sylius.com/sylius-logo-800-dark.png">
          <source media="(prefers-color-scheme: light)" srcset="https://media.sylius.com/sylius-logo-800.png">
          <img alt="Sylius Logo." src="https://media.sylius.com/sylius-logo-800.png">
        </picture>
    </a>
</p>

GmvBundle
=========

Lightweight local command to calculate the Sylius instance GMV within a specific period

## Features

- Calculate GMV for a specified period. Default period is the last whole year.
- Exclude taxes and shipping costs from GMV calculation.
- Provide GMV in multiple currencies.

## Installation

Add the bundle to your Sylius project:

 ```bash
 composer require sylius/gmv-bundle
 ```

## Usage

### Command Line

You can calculate the GMV for a specified period using the Symfony console command:

```bash
bin/console sylius:gmv:calculate [periodStart] [periodEnd]
```

The `periodStart` and `periodEnd` arguments are optional and should be in the `mm/YYYY` format. If not provided, the command will calculate the GMV for the last whole year.

### Example

The following command will calculate the GMV for the period from 1 January 2024 to 31 May 2024:

```bash
bin/console sylius:gmv:calculate 01/2024 5/2024
```

Example command output:

```
GMV Calculation
Period Start: 2024-01-01
Period End: 2024-05-31
GMV in USD: $3,247.28
```

Contributing
------------

[This page](https://docs.sylius.com/en/latest/contributing/index.html) contains all the information about contributing to Sylius.

Follow Sylius' Development
--------------------------

If you want to keep up with the updates and latest features, follow us on the following channels:

* [Official Blog](https://sylius.com/blog)
* [Sylius on Twitter](https://twitter.com/Sylius)
* [Sylius on Facebook](https://facebook.com/SyliusEcommerce)

Bug tracking
------------

Sylius uses [GitHub issues](https://github.com/Sylius/Sylius/issues).
If you have found bug, please create an issue.

MIT License
-----------

License can be found [here](https://github.com/Sylius/Sylius/blob/master/LICENSE).

Authors
-------

See the list of [contributors](https://github.com/Sylius/Sylius/contributors).
