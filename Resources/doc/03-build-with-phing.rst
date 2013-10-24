Build with Phing
================
Installation instructions
-------------------------

This guide assumes that you execute all CLI commands from the project root directory. You should perform these actions
on all development environments and on the CI server.

Ensure that you have PHP XSLT extension installed and Graphviz_ application. They are required by phpDocumentor 2.

Install required PEAR libraries
::

    sudo pear upgrade PEAR\
    && sudo pear config-set auto_discover 1\
    && sudo pear install pear.phpqatools.org/phpqatools pear.phing.info/phing pear.phpunit.de/hphpa pear.phpdoc.org/phpDocumentor

Symlink custom PHP_CodeSniffer standards to PEAR directory
::

    sudo ln -s `pwd`/vendor/epam/ci-bundle/Epam/CiBundle/Resources/configs/phpcs/Standards/Symfony2EpamCi `pear config-get php_dir`/PHP/CodeSniffer/Standards/
    sudo ln -s `pwd`/vendor/epam/ci-bundle/Epam/CiBundle/Resources/configs/phpcs/Standards/Symfony2EpamCiJs `pear config-get php_dir`/PHP/CodeSniffer/Standards/

We recommend to commit ``composer.phar`` and ``php-cs-fixer.phar`` to the repository and store them in the root of the
project because it helps to use exactly the same tools across all environments. This is the default location used in the
build file. You can change location of each tool in ``build.properties.dist``.

If you agree with the default tools location then install both tools:
::

    wget http://getcomposer.org/composer.phar\
    && wget http://cs.sensiolabs.org/get/php-cs-fixer.phar

Usage instructions
------------------
Existing targets could be overridden in ``build.xml`` file in the project root. Additional configuration could be
performed in ``build.properties.dist`` (project-specific, i.e. shared between different environments) and
``build.properties`` (environment-specific).

Currently the following configuration could be applied in the ``properties`` files:

============================================== ==================================================================================
Property                                       Description
============================================== ==================================================================================
``project.symfony-twig-bundles``               Comma-separated list of bundles that should pass Twig lint verification
``project.bin-composer``                       Path to Composer
``project.bin-php-cs-fixer``                   Path to PHP CS Fixer
``project.bin-php``                            Path to PHP CLI
``project.dir-app``                            App directory of the application
``project.phpcs-php-standard``                 Name of PHP_CodeSniffer standard applied to PHP files
``project.phpcs-js-standard``                  Name of PHP_CodeSniffer standard applied to JavaScript files
``project.symfony-doctrine-migrations-enable`` Enable Doctrine Migrations during build
``project.symfony-doctrine-fixtures-enable``   Enable Doctrine Fixtures during build
``project.symfony-doctrine-fixtures-append``   Append Doctrine Fixtures instead of truncating database
``project.deploy-backup-db``                   Backup database before the deployment
``project.deploy-clear-doctrine-cache``        Clear Doctrine ORM cache during deployment (Should be disabled for APC-based cache)
============================================== ==================================================================================

``build`` is the default target. It should be used by every developer before the commit:
::

    phing

Core Phing targets
------------------
============================= =============================================================================
Target name                   Description
============================= =============================================================================
``build``                     Builds the project. Intended for usage on the command line before committing.
``build-ci``                  Builds the project for the continuous integration server
``package``                   Prepares archived package containing ready for deployment application
``deploy``                    Deploys the application archive to a remote host using Capifony
``build-tools-self-update``   Updates tools used for build: Composer and PHP Coding Standards Fixer
``dependencies-list-updates`` Lists available updates on dependencies via Composer
``deploy-backup-db``          Performs database backup on a remote host
============================= =============================================================================

Complete list of tasks could be found using the following command:
::

    phing -l

.. _Graphviz: http://graphviz.org/

