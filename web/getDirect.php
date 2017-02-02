<?php
$fileName = isset($_GET['fileName']) ? $_GET['fileName'] : 'Win7_Pro_SP1_English_x64.iso';

$req = curl_init("http://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=160bb813-f54e-4e9f-bffc-38c6eb56e061&host=www.microsoft.com&segments=software-download%2cwindows10&query=&action=GetProductDownloadLinkForFriendlyFileName&friendlyFileName=" . urlencode($fileName));

curl_setopt($req, CURLOPT_HEADER, 0);
curl_setopt($req, CURLOPT_REFERER, "https://www.microsoft.com/en-us/software-download/windows10ISO");
curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

$out = curl_exec($req);
curl_close($req);

if (strpos($out, 'We encountered a problem processing your request') !== false) {
	echo 'There was an error processing your request.';
	die();
}

$out = preg_replace('/\n|\r|\t/', '', $out);

$out = preg_replace('/.*http/', 'http', $out);
$out = preg_replace('/",downloadType:".*<\/div>/', '', $out);

echo '<h1>Moved to <a href="'. $out .'">here</a>';

header('Location: '. $out);
die();
?>
