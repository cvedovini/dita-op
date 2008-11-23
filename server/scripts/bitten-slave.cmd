@echo off
setlocal
call setenv.cmd
bitten-slave.exe http://%DOP_HOST%:%DOP_PORT%/projects/%1/builds -d builds/%1 -u admin -p admin -v
endlocal
