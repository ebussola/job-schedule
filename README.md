job-schedule
============

A daemon job schedule

Status Codes
============

- 0 - Everything is OK
- 1 - It's running
- 2 - Can't run because of some error on dependencies
- 3 - Some error when executing command (exit code not 0)
- 4 - Waiting another process finish to start execution

Test
====

To run the ExternalControllerCommandTest, you need to start the test daemon, just run test/start-daemon.php

It will open a daemon with stubs classes simulating some functionalities.