SET DIR=%~dp0
SET PARAMS=
SHIFT
:LOOP1
IF "%1"=="" GOTO AFTER_LOOP
SET PARAMS=%PARAMS% %1
SHIFT
GOTO LOOP1

:AFTER_LOOP
php "%DIR%.\..\garoevans\composer-sym\bin\cubex" --cubex-env=cli ComposerSym:%METHOD% %PARAMS%
