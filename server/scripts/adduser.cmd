@echo off
setlocal
call setenv.cmd
htpasswd %DOP_CONF%.htpasswd %1
echo %1 = rw >> %DOP_CONF%.svnaccess
endlocal