Deployment with Capifony
========================
Installation instructions for application servers on target environments
------------------------------------------------------------------------
Create separate user ``www-sh`` with authentication by public key and add public key of CI server. User ``www-sh``
should use the same group as PHP executed for web server (e.g. ``www-data``).

Ensure that your environment is ready for Symfony application. Don't forget to set proper ``env`` on production, e.g.
using ``SYMFONY_ENV=prod`` and ``SYMFONY_DEBUG=0`` Environment variables.

Create base directory that will be used to deploy all the projects (``/var/www`` by default). Selected user should have
write privileges on this directory, e.g. ``sudo chown www-sh:www-data /var/www-data/``

Add ability to restart service with ``sudo`` for ``www-sh`` without password, e.g.:
::

    sudo visudo
    #User_Alias DEPLOYERS = www-sh
    #Cmnd_Alias SERVICE_CMDS = /usr/sbin/service
    #DEPLOYERS ALL=NOPASSWD: SERVICE_CMDS


Proceed with web server configuration.

Installation instructions for CI server
---------------------------------------
Install RubyGems_

Install required gems:
::

    sudo gem update\
    && sudo gem install capifony\
    && sudo gem install capistrano-deploy-strategy-archive


Add public key of ``jenkins`` user to all application servers of target environments and verify that publickey auth
works:
::

    ssh-copy-id www-sh@<hostname>

Configuration in repository
---------------------------
Project configuration
~~~~~~~~~~~~~~~~~~~~~
Project configuration could be applied to ``app/Resources/configs/capifony/deploy.rb``. Details on configuration could
be found at `Capifony website`_ and `Capistrano wiki`_.

New environment configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Bundle creates configuration for ``qa`` environment. Configuration file is placed in
``app/Resources/configs/capifony/stages/qa.rb``.

You have to perform the following steps in order to create new environment:

#. Copy ``app/Resources/configs/capifony/stages/qa.rb`` to
   ``app/Resources/configs/capifony/stages/<environment_name>.rb`` or create it from the scratch. Change configuration
   options. Provide at least ``server`` name.
#. Add environment to ``stages`` config in ``app/Resources/configs/capifony/deploy.rb``, e.g.
   ``set :stages, %w(qa <environment_name>)``.
#. Create configuration file ``/app/config/parameters_<environment_name>.yml``. It would become
   ``/app/config/parameters.yml`` on the target environment after deployment. Change configuration options if necessary.
#. Add new environment name to ``ENVIRONMENT_NAME`` parameter of ``deploy-package-tag`` in Jenkins CI

First deployment to the new node
--------------------------------

#. Commit config changes to the repository
#. Update working copy on CI server manually using ``jenkins`` account (you can clone repo in some temporary location for
   this purpose). All following steps assume that you execute all CLI commands from this project root directory.
#. Execute the following command on the CI server using ``jenkins`` account: ``cap <environment_name> deploy:setup``
#. Perform steps on the target nodes using ``www-sh`` account (``<deployment_path>`` is equal to ``deploy_to`` value
   in ``app/Resources/configs/capifony/deploy.rb`` file):
   ::

    cd <deployment_path>/shared
    mkdir -p app/logs app/config
#. Copy all project-specific shared files (if any)
#. Execute first deployment manually using ``jenkins`` account: ``phing clean package deploy -Dproject.deploy-env=<environment_name> -Dproject.deploy-archive=build/output/<application_name>-0.0.1-SNAPSHOT.tar.gz -Dproject.deploy-maintenance-enable=0 -Dproject.deploy-backup-db=0 -Dproject.deploy-clear-doctrine-cache=0 -Dproject.deploy-dump-version=0``
#. All subsequent deployments should be performed via Jenkins CI UI

Maintenance mode
----------------
Maintenance mode template is located in ``app/Resources/configs/capifony/maintenance.html.erb`` file. Feel free to
modify it for your purposes.

Apache 2
~~~~~~~~
Add the following code to ``web/.htaccess`` right after ``RewriteEngine On`` to enable proper maintenance mode handling:
::

    # Maintenance mode verification
    ErrorDocument 503 /maintenance.html
    RewriteCond %{REQUEST_URI} !\.(css|js|gif|jpg|png)$
    RewriteCond %{DOCUMENT_ROOT}/maintenance.html -f
    RewriteCond %{SCRIPT_FILENAME} !maintenance.html
    RewriteRule ^.*$ - [redirect=503,last]

Nginx
~~~~~
Add the following code to the nginx config to enable proper maintenance mode handling:
::

    # Maintenance mode verification
    error_page 503 @maintenance;
    if (-f $document_root/maintenance.html) {
        return 503;
    }

.. _RubyGems: http://rubygems.org/
.. _Capifony website: http://capifony.org/
.. _Capistrano wiki: https://github.com/capistrano/capistrano/wiki
