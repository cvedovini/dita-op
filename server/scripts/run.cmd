@echo off
setlocal
call setenv.cmd
cd apache2
httpd
endlocal