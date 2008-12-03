@echo off
setlocal
call setenv.cmd
httpd -f %DOP_CONF%httpd.conf
endlocal