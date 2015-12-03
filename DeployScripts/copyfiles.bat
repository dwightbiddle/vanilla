REM /Y: Overwrites existing files without prompting you.
REM /R: Overwrites read-only files.
REM /D: Only copies files in the source that are newer than the destination.
REM /E: Copies any subfolder, even if it is empty (Recursive).

xcopy C:\GIT\VanillaCollabatron\%1 \\usb-tgcollabap2\c$\wamp\www\%1 /Y /R /D /E