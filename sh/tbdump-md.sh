#!/bin/bash

minProdID=1
maxProdID=300

if [ -n "$1" -a -z "$2" ]; then echo "Usage: $0 [first_id] [last_id]"; exit; fi

if [ -n "$1" -a -n "$2" ]; then
	let minProdID=$1+0
	let maxProdID=$2+0
	
	if [ $minProdID -le 0 ]; then echo "First Product ID needs to be larger than 0"; exit 1; fi
	if [ $maxProdID -le 0 ]; then echo "Last Product ID needs to be larger than 0"; exit 1; fi
	if [ $maxProdID -lt $minProdID ]; then echo "Last Product ID needs to be larger or equal to First Product ID"; exit 1; fi
fi

tbdumpVersion="9alpha1"

infoHead="[INFO]"
warnHead="[WARNING]"
errorHead="[ERROR]"

noProductErr="The product key you provided is for a product not currently supported by this site or may be invalid"

#URLs to all needed things
getLangUrl="https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=a8f8f489-4c7f-463a-9ca6-5cff94d8d041&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=getskuinformationbyproductedition"
getDownUrl="https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=GetProductDownloadLinksBySku"
getDownUrlShort="http://mdl-tb.ct8.pl/get.php"
refererUrl="https://www.microsoft.com/en-us/software-download/windows10ISO"

#Fix redirection on Windows and warn user, that Control-C is broken
if [ "$WIN_WRAPPED" == "1" ]; then
	nullRedirect="NUL"
	echo -e "$warnHead Control-C does not work when using this script on Windows!\n"
else
	nullRedirect="/dev/null"
fi

if ! type curl > $nullRedirect; then
	echo "$errorHead This scripts needs cUrl to be installed! Exiting" >&2
	exit
fi

function getLangs {
	local result=$(curl -s "$getLangUrl&productEditionId=$1" -H "Referer: $refererUrl")

	if echo "$result" | grep "$noProductErr" > $nullRedirect; then
		return 1
	fi

	echo "$result" | grep 'option value=.{&quot;id' > "/dev/null"
	if [ $? -ne 0 ]; then
		return 2
	fi

	local result=$(echo "$result" | grep 'option value=.{&quot;id')
	langList=$(echo "$result" | sed 's/.*<option value=.{//g;s/}.>.*<\/option>//g;s/&quot;//g;s/id:/skuId=/g;s/,language:/\&language=/g')
	return 0
}

function getNameAndWrite {
	local appendVer=""
	
	#Windows 10 identification
	if [ $productID -ge 75 -a $productID -le 82 ]; then local appendVer=" (Threshold 1)"; fi
	if [ $productID -ge 99 -a $productID -le 106 ]; then local appendVer=" (Threshold 2)"; fi
	if [ $productID -ge 109 -a $productID -le 116 ]; then local appendVer=" (Threshold 2, February 2016 Update)"; fi
	if [ $productID -ge 178 -a $productID -le 185 ]; then local appendVer=" (Threshold 2, April 2016 Update)"; fi
	if [ $productID -ge 242 -a $productID -le 247 ]; then local appendVer=" (Redstone 1)"; fi
	
	local appendVer="$appendVer [ID: $productID]"
	
	local tempLine=$(printf "$langList" | tail -n1 | tr -d '\r')
	local tempLink=$(printf "$tempLine" | sed s/.language=.*//g)
	local tempLang=$(printf "$tempLine" | awk -F'[&= ]' '{print $4}')

	local result=$(curl -s "$getDownUrl&$(echo -n $tempLink)" -H "Referer: $refererUrl")

	echo "$result" | grep "Choose a link below to begin the download" > $nullRedirect
	if [ $? -ne 0 ]; then
		return 1
	fi

	echo "$result" | grep -o '<h2>.*<\/h2>' | sed "s/.*<h2>/### /g;s/ $tempLang.*<\/h2>/$appendVer/g" >> "Techbench dump.md"
	if [ $? -ne 0 ]; then
		return 1
	fi

	echo "$infoHead Writing..."
	echo "" >> "Techbench dump.md"
	echo "$langList" | tr -d '\r' | awk -v url="$getDownUrlShort?skuId=" -F'[&=]' '{print "* ["$4"]("url $2")"}' >> "Techbench dump.md"
	echo "" >> "Techbench dump.md"
}

echo "$infoHead TechBench dump script (tbdump-$tbdumpVersion)"
echo "$infoHead Using Product ID range from $minProdID to $maxProdID"

echo "# TechBench dump" > "Techbench dump.md"
echo "Generated on $(date "+%Y-%m-%dT%H:%M:%S%z") using:<br>" >> "Techbench dump.md"
echo "TechBench dump script (tbdump-$tbdumpVersion)<br>" >> "Techbench dump.md"
echo "$(uname -mrsio)<br>" >> "Techbench dump.md"
echo "$(curl -V | head -n1)<br>" >> "Techbench dump.md"
echo "" >> "Techbench dump.md"
echo "Number of products: !!productsNumberPlaceholder!!<br>" >> "Techbench dump.md"
echo "" >> "Techbench dump.md"

echo -e "\n$infoHead Checking for languages using Product ID..."

productsFound=0

for productID in $(seq $minProdID $maxProdID); do
	echo "$infoHead Checking product ID: $productID"
	getLangErr=2
	while [ $getLangErr -gt 1 ]; do
		getLangs $productID
		getLangErr=$?
		if [ $getLangErr -eq 0 ]; then
			echo "$infoHead Got language list!"
			writeErr=1
			while [ $writeErr -ne 0 ]; do
				getNameAndWrite
				writeErr=$?
			done;
			let productsFound=productsFound+1
			echo "$infoHead OK!"
		elif [ $getLangErr -eq 1 ]; then
			echo "$errorHead Product does not exist!"
		fi
	done;
	echo ""
done;

sed s/!!productsNumberPlaceholder!!/$productsFound/g "Techbench dump.md" > "Techbench dump.tmp"
mv -f "Techbench dump.tmp" "Techbench dump.md"

echo "$infoHead Number of products: $productsFound"
echo "$infoHead Done"
