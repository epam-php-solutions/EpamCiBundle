EpamCiBundle
============
Overview
--------
This bundle is intended to help with Continuous Integration & Automated Deployments for Symfony-based projects.

Currently it supports Phing_ build tool, `Jenkins CI`_ server and `Capifony`_ deployment tool.

Todo
----
#. build
    - add build tools to ``require-dev``, don't use tools from pear
    - ``build`` target: add assetic:dump --env=prod
    - symfony security validator: display output from webservice if vulnerability was found
    - add hphpa analysis
#. deploy
    - rollback docs
    - custom CLI tasks during deployment via jenkins
    - handling of running CLI/cron scripts
    - execute ``composer install --dry-run`` and ``php app/check.php``during deployment to verify that environment is
      sane
#. general
    - put a link to presentation

Detailed information
--------------------
#. `Installation instructions`_
#. `Usage`_
#. `Build with Phing`_
#. `Continuous Integration with Jenkins CI`_
#. `Automated Deployments with Capifony`_

.. _Installation instructions: 01-install.rst
.. _Usage: 02-usage.rst
.. _Build with Phing: 03-build-with-phing.rst
.. _Continuous Integration with Jenkins CI: 04-ci-with-jenkins.rst
.. _Automated Deployments with Capifony: 05-deploy-with-capifony.rst
.. _Phing: http://www.phing.info/
.. _Jenkins CI: http://jenkins-ci.org/
.. _Capifony: http://capifony.org/
