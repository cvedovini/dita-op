@echo off
setlocal
call setenv.cmd

rem svnadmin create %DOP_HOME%repository\%1
trac-admin %DOP_HOME%projects\%1 initenv --inherit=%DOP_HOME%common\conf\trac.ini %1 sqlite:db/trac.db svn %DOP_HOME%repository\%1

endlocal