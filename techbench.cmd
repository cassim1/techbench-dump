@echo off
set productID=1
set maxProdID=300
set appendVer=
set "getLangUrl=https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=a8f8f489-4c7f-463a-9ca6-5cff94d8d041&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=getskuinformationbyproductedition"
set "getDownUrl=https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=GetProductDownloadLinksBySku"

set rnd=%random%
set foundProducts=0

if [%PROCESSOR_ARCHITECTURE%] == [AMD64] (
set binDir=bin\x64
) ELSE (
set binDir=bin
)

cd /d %~dp0
mkdir tmp%rnd%

title Techbench dump script
cls
color 07

%binDir%\busybox.exe echo -e "\033[36;1mTechbench dump script (HTML version)\033[0m"

echo ^<html^> > "Techbench dump.html"
echo ^<head^> >> "Techbench dump.html"
echo ^<title^>Techbench dump^</title^> >> "Techbench dump.html"
echo ^</head^> >> "Techbench dump.html"
echo ^<body^> >> "Techbench dump.html"
echo ^<font face="Consolas, Monospace" size="2"^> >> "Techbench dump.html"
echo ^<h1^>TechBench dump^</h1^> >> "Techbench dump.html"
for /f "delims=" %%a IN ('%binDir%\busybox.exe date -Iseconds') do echo Generated on %%a using:^<br/^> >> "Techbench dump.html"
for /f "delims=" %%a IN ('%binDir%\curl.exe -V ^| %binDir%\busybox.exe head -n1') do echo %%a^<br/^> >> "Techbench dump.html"
for /f "delims=" %%a IN ('%binDir%\busybox.exe ^| %binDir%\busybox.exe head -n1') do echo %%a^<br/^> >> "Techbench dump.html"
echo ^<br/^>Number of products: !!productsNumberPlaceholder!!^<br/^> >> "Techbench dump.html"

echo Checking for languages using Product ID...
echo.

:getLangs
%binDir%\busybox.exe echo -n "Checking product: %productID%..."

:retryGetLangs
%binDir%\curl.exe -s -o"tmp%rnd%\temp.txt" "%getLangUrl%&productEditionId=%productID%"

%binDir%\busybox.exe grep "The product key you provided is for a product not currently supported by this site or may be invalid" "tmp%rnd%\temp.txt" > NUL
if %errorlevel% EQU 0 %binDir%\busybox.exe echo -e " \033[31;1mFail\033[0m" & goto nextProduct

%binDir%\busybox.exe grep "option value=.{&quot;id" "tmp%rnd%\temp.txt" > "tmp%rnd%\prod.txt"
if %errorlevel% NEQ 0 goto retryGetLangs

%binDir%\busybox.exe echo -ne " \033[37;1mGetting name of product...\033[0m"

%binDir%\busybox.exe sed -i "s/.*<option value=.{//g";"s/}.>.*<\/option>//g";"s/&quot;//g";"s/id:/skuId=/g";"s/,language:/\&language=/g" "tmp%rnd%\prod.txt"

REM ## Windows 10 identification ##
if %productID% GEQ 75 if %productID% LEQ 82 set "appendVer= (Threshold 1)"
if %productID% GEQ 99 if %productID% LEQ 106 set "appendVer= (Threshold 2)"
if %productID% GEQ 109 if %productID% LEQ 116 set "appendVer= (Threshold 2, February 2016 Update)"
if %productID% GEQ 178 if %productID% LEQ 185 set "appendVer= (Threshold 2, April 2016 Update)"

set "appendVer=%appendVer% [ID: %productID%]"

:retryGetName
%binDir%\busybox.exe tail -n1 "tmp%rnd%\prod.txt" > "tmp%rnd%\temp.txt"
for /f %%a IN ('%binDir%\busybox.exe tail -n1 "tmp%rnd%\prod.txt"') do set "tempLink=%%a"
for /f %%a IN ('%binDir%\busybox.exe sed s/.*language^=//g "tmp%rnd%\temp.txt"') do set "tempLang=%%a"

%binDir%\curl.exe -s -o"tmp%rnd%\temp.txt" "%getDownUrl%&%tempLink%"

%binDir%\busybox.exe grep "Choose a link below to begin the download" "tmp%rnd%\temp.txt" > NUL
if %errorlevel% NEQ 0 goto retryGetName

%binDir%\busybox.exe grep -o "<h2>.*<\/h2>" "tmp%rnd%\temp.txt" | %binDir%\busybox.exe sed "s/.*<h2>/<h3>/g";"s/ %tempLang%.*<\/h2>/%appendVer%<\/h3>/g" >> "Techbench dump.html"
if %errorlevel% NEQ 0 goto retryGetName

%binDir%\busybox.exe echo -ne "\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\033[37;1mWriting...\033[0m                \b\b\b\b\b\b\b\b\b\b\b\b\b\b\b\b"
for /f "delims=" %%a IN (tmp%rnd%\prod.txt) do echo ^<a href="%getDownUrl%&%%a"^>!!%%a^</a^>^<br/^> >> "Techbench dump.html"

set /a foundProducts=foundProducts+1
%binDir%\busybox.exe echo -e "\b\b\b\b\b\b\b\b\b\b\033[32;1mOK\033[0m        "

:nextProduct
set appendVer=
set /a productID=productID+1
if %productID% LEQ %maxProdID% goto getLangs

echo.
echo Number of products: %foundProducts%
%binDir%\busybox.exe sed -r -i "s/!!productsNumberPlaceholder!!/%foundProducts%/g" "Techbench dump.html"

echo.
echo Formatting HTML...
%binDir%\busybox.exe sed -i s/!!.*language^=//g "Techbench dump.html"
echo ^</font^> >> "Techbench dump.html"
echo ^</body^> >> "Techbench dump.html"
echo ^</html^> >> "Techbench dump.html"

echo Cleaning temp files...
rmdir /q /s tmp%rnd%

echo.
echo Done.
pause
