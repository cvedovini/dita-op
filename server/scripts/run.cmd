@echo off
setlocal
call setenv.cmd
cd %APACHE_HOME%
httpd
endlocal