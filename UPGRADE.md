# UPGRADE FROM 1.0 TO 1.1

1. Support for Symfony 8 has been added. It requires Sylius 2.3, the first Sylius release supporting Symfony 8.

1. Support for Symfony 5.4, PHP 8.1 and Sylius 1.14 has been dropped. The minimum requirements are now PHP 8.2
   and Sylius 2.0.

1. The `config/services.xml` file has been replaced with `config/services.php`. If you imported it directly
   (instead of relying on `SyliusGmvExtension`), update the path accordingly.

# UPGRADE FROM 0.2 TO 1.0

1. Support for Sylius 2.0 has been added.

1. Support for Sylius 1.12 and 1.13 have been dropped.

1. The `GmvCommand` console command has been moved from `Command` to `Console\Command` and changed its namespace
   accordingly from `Sylius\GmvBundle\Command\GmvCommand` to `Sylius\GmvBundle\Console\Command\GmvCommand`. 
   The service ID has been also changed from `sylius_gmv.command.gmv` to `sylius_gmv.console.command.gmv`. 

# UPGRADE FROM 0.1 TO 0.2

1. Support for Sylius 1.14 has been added, it is now the recommended Sylius version to use with GmvPlugin.

1. Support for Sylius 1.12 has been dropped, upgrade your application to [Sylius 1.13](https://github.com/Sylius/Sylius/blob/1.13/UPGRADE-1.13.md).
   or [Sylius 1.14](https://github.com/Sylius/Sylius/blob/1.14/UPGRADE-1.14.md).
