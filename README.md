job-schedule
============

A daemon job schedule

Status Codes
============

0 - Everything is OK
1 - It's running
2 - Can't run because of some error on dependencies
3 - Some error when executing command (exit code not 0)
4 - Waiting another process finish to start execution