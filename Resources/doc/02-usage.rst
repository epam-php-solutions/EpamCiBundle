Usage
=====

Ensure that your project is under version control system because this bundle will overwrite files without notifications.
Note that bundle does not overwrite any file from Symfony distribution, but it could overwrite its own file.

Execute ``app/console`` command to create configuration skeleton for your project:
::

    app/console epam-ci:init --env=dev <project name>

All available options could be found in command help:
::

    app/console epam-ci:init --env=dev --help

