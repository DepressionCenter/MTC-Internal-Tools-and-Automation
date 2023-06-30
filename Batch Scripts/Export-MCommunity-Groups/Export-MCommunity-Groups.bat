
setlocal enabledelayedexpansion

set "substring=member: uid=*,ou=People"
set searchstring1=member: uid
set replacestring1=


for /F "delims=*" %%g in (Export-MCommunity-Groups-Config.txt) do (
    ldapsearch -x -LLL -h ldap.umich.edu -b "ou=Groups,dc=umich,dc=edu" "cn=%%g" >> temp.txt

	REM Deleting existing group membership output file
	if exist %%g.txt del /F %%g.txt
	
	for /f "delims=," %%L in ('findstr "member: uid=*," "temp.txt"') do (
		for %%S in (%%L) do (
			for /f "tokens=*" %%u in ("%%~L") do (
				set newline=%%u
				call set newline=%%newline:!searchstring1!=!replacestring1!%%
				call :replaceEqualSign in newline with _
				
				REM The variable !newline! should now contain a uniquename
				call echo !newline! >> %%g.txt
			)
	   )
	)
	
	del temp.txt
)



:replaceEqualSign in <variable> with <newString>
    setlocal enableDelayedExpansion

        set "_s=!%~2!#"
        set "_r="

        :_replaceEqualSign
            for /F "tokens=1* delims==" %%A in ("%_s%") do (
                if not defined _r ( set "_r=%%A" ) else ( set "_r=%_r%%~4%%A" )
                set "_s=%%B"
            )
        if defined _s goto _replaceEqualSign

    endlocal&set "%~2=%_r:~0,-1%"
exit /B
