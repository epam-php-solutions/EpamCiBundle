Installation instructions
=========================

Add dependencies to composer.json
---------------------------------

If you wish to use DoctrineMigrationsBundle_ or DoctrineFixturesBundle_ then you have to add them into your
``composer.json`` manually.

In order to use these bundles add the following lines to ``require`` section of your ``composer.json`` file:
::

        "doctrine/doctrine-migrations-bundle": ">1.0.0-beta1@dev",
        "doctrine/migrations": ">1.0-ALPHA1@dev",
        "doctrine/doctrine-fixtures-bundle": ">2.1.0-ALPHA@dev",
        "doctrine/data-fixtures": ">1.0.0-ALPHA3@dev"

Download bundle using Composer
------------------------------
::

    php composer.phar require "epam/ci-bundle=@dev"

Enable bundle in the AppKernel
------------------------------

Edit your ``app/AppKernel.php`` file and add the following lines
::

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            //...
            $bundles[] = new Epam\CiBundle\EpamCiBundle();
        }

If you installed DoctrineMigrationsBundle_ and DoctrineFixturesBundle_ then add also the following lines
::

        new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
        new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),

Update .gitignore
-----------------

Add ``/build.properties`` to ``.gitignore``

.. _DoctrineMigrationsBundle: http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html
.. _DoctrineFixturesBundle: http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
