@echo off
if not "%DOP_HOME%"=="" goto exit

set DOP_HOST=localhost
set DOP_PORT=8181
set DOP_HOME=%~dp0
set APACHE_HOME=%DOP_HOME%apache2\
set ANT_HOME=%DOP_HOME%ant\
set DITA_HOME=%DOP_HOME%dita-ot\
set PYTHONHOME=%DOP_HOME%python\
set SVN_HOME=%DOP_HOME%svn\
set PATH=%APACHE_HOME%bin;%PYTHONHOME%;%PYTHONHOME%Scripts;%SVN_HOME%bin;%ANT_HOME%bin;%PATH%

:exit