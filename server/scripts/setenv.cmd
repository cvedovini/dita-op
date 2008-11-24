@echo off
if not "%DOP_HOME%"=="" goto exit

set DOP_HOST=localhost
set DOP_PORT=8181
set DOP_HOME=%~dp0
set DOP_TOOLS=%DOP_HOME%tools\
set DOP_CONF=%DOP_HOME%conf\
set APACHE_HOME=%DOP_TOOLS%apache2\
set ANT_HOME=%DOP_TOOLS%ant\
set DITA_HOME=%DOP_TOOLS%dita-ot\
set PYTHONHOME=%DOP_TOOLS%python\
set SVN_HOME=%DOP_TOOLS%svn\
set PATH=%APACHE_HOME%bin;%PYTHONHOME%;%PYTHONHOME%Scripts;%SVN_HOME%bin;%ANT_HOME%bin;%PATH%

:exit