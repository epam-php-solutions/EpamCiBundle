Continuous Integration with Jenkins CI
======================================
Installation instructions
-------------------------
Download and install Jenkins CI according to `instructions on the official website`_

Add required Jenkins Plugins
::

    wget http://localhost:8080/jnlpJars/jenkins-cli.jar\
    && curl -L http://updates.jenkins-ci.org/update-center.json | sed '1d;$d' | curl -X POST -H 'Accept: application/json' -d @- http://localhost:8080/updateCenter/byId/default/postBack\
    && java -jar jenkins-cli.jar -s http://localhost:8080 install-plugin checkstyle cloverphp dry htmlpublisher jdepend plot pmd violations xunit phing git greenballs parameterized-trigger copyartifact email-ext\
    && java -jar jenkins-cli.jar -s http://localhost:8080 safe-restart


Second line is a temporary workaround for Jenkins issue with plugin installation (No update center data is retrieved yet
from: http://updates.jenkins-ci.org/update-center.json checkstyle looks like a short plugin name. Did you mean 'null'?)


Proceed with the build and deployment tools installation. You will need to checkout your projectfrom VCS once into a
temporary location for this purpose. This guide assumes that you execute all CLI commands from the project root
directory. You should perform these actions on all development environments and on the CI server. It's recommended to
use the UNIX account that is used by Jenkins CI (it would help with an access to Git repository). Keep in mind that
you should add public SSH key of the Jenkins account to your Git repository. Deployment key (i.e. read-only access) is
recommended.

Job configuration instructions
------------------------------
Execute the following commands to create all generated jobs replacing ``<project-name>`` with your project name
::

    PROJECT_NAME=<project-name>\
    && cat app/Resources/configs/jenkins/config-build-master.xml | java -jar jenkins-cli.jar -s http://localhost:8080/ create-job $PROJECT_NAME-build-master\
    && cat app/Resources/configs/jenkins/config-build-package-tag.xml | java -jar jenkins-cli.jar -s http://localhost:8080/ create-job $PROJECT_NAME-build-package-tag\
    && cat app/Resources/configs/jenkins/config-deploy-package-tag.xml | java -jar jenkins-cli.jar -s http://localhost:8080/ create-job $PROJECT_NAME-deploy-package-tag\
    && cat app/Resources/configs/jenkins/config-deploy-qa-master.xml | java -jar jenkins-cli.jar -s http://localhost:8080/ create-job $PROJECT_NAME-deploy-qa-master

Apply additional configuration to the jobs in Jenkins CI UI:

#. Repository configuration (at least ``Repository URL`` for Git)
#. ``Build Triggers`` for ``build-master`` job (Poll SCM is recommended unless you had configured appropriate hook on
   your VCS server)
#. ``Discard Old Builds`` (if necessary)
#. ``Copy artifacts from another project - Project name`` should be changed to ``<project-name>-build-package-tag`` in
   ``deploy-package-tag`` job

.. _instructions on the official website: http://jenkins-ci.org/

Security
--------
It's a good practice to enable authentication for Jenkins CI and configure roles for accounts. Also you should configure
email settings in order to receive email notifications.

Available jobs
--------------
build-master
~~~~~~~~~~~~
Job executes ``build-ci`` phing target using latest commit from ``master`` branch. You can switch this job to
``develop`` or whatever else branch if you need. It's recommended to have multiple jobs created from this template if
active development is performed in multiple branches.

Job does not produce any artifacts.

build-package-tag
~~~~~~~~~~~~~~~~~
Job executes ``build-ci`` and ``package`` phing targets. Build is parameterized, but you have provide Git tag name
manually due to the bug in git-parameter_ plugin.

Job produces ``tar.gz`` archive with packaged version of the application. This package is used by deploy-package-tag job.

deploy-qa-master
~~~~~~~~~~~~~~~~
Job executes ``build-ci``, ``package`` and ``deploy`` phing targets.

Job produces the following artifacts:

#. ``tar.gz`` archive with packaged version of the application
#. ``previous-doctrine-migrations-info.txt`` file with output of
   ``app/console doctrine:migrations:status --show-versions``
#. ``previous-version.txt`` file with the previous deployed version info:
    - ``<tag_name>.<build_number>`` if previous build was from ``deploy-package-tag``, e.g. ``v1.0.2.42``
    - ``<branch_name>-<build_number>-<commit_hash>-SNAPSHOT`` if previous build was from ``deploy-qa-master``, e.g.
      ``master-3-7f0cebe8f859bc15be2fb7e6fd0da4dd0cebcbe5-SNAPSHOT``
#. ``<application_name>.remote_dump.latest.sql.gz``

deploy-package-tag
~~~~~~~~~~~~~~~~~~
Job executes ``deploy`` phing target. Build is parameterized. It accepts the following parameters:

======================== =============================================== ====================================================================================
Name                     Default value                                   Description
======================== =============================================== ====================================================================================
``PACKAGE_TAG_BUILD_ID`` ``Latest successful build``                     Id of the ``build-package-tag`` build which will be used as a source for the package
``ENVIRONMENT_NAME``     ``qa``                                          Target environment
``MAINTENANCE_ENABLE``   ``Yes``                                         Yes/No
``MAINTENANCE_MESSAGE``  ``We are sorry. Project is under maintenance.`` Maintenance mode message
``MAINTENANCE_UNTIL``    ``HH:MM GMT``                                   Planned end time of the deployment
======================== =============================================== ====================================================================================

Job produces the following artifacts:

#. ``tar.gz`` archive with packaged version of the application
#. ``previous-doctrine-migrations-info.txt`` file with output of
   ``app/console doctrine:migrations:status --show-versions``
#. ``previous-version.txt`` file with the previous deployed version info:
    - ``<tag_name>.<build_number>`` if previous build was from ``deploy-package-tag``, e.g. ``v1.0.2.42``
    - ``<branch_name>-<build_number>-<commit_hash>-SNAPSHOT`` if previous build was from ``deploy-qa-master``, e.g.
      ``master-3-7f0cebe8f859bc15be2fb7e6fd0da4dd0cebcbe5-SNAPSHOT``
#. ``<application_name>.remote_dump.latest.sql.gz``

.. _git-parameter: https://github.com/lukanus/git-parameter/issues/2
