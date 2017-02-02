<?php
$skuId = isset($_GET['skuId']) ? $_GET['skuId'] : '6PC-00020';

$req = curl_init("http://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=GetProductDownloadLinksBySku&skuId=" . urlencode($skuId));

curl_setopt($req, CURLOPT_HEADER, 0);
curl_setopt($req, CURLOPT_REFERER, "https://www.microsoft.com/en-us/software-download/windows10ISO");
curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

$expire = time() + 86400;
$out = curl_exec($req);
curl_close($req);

$out = preg_replace('/\n|\r|\t/', '', $out);

$out = preg_replace('/<div.*?>|<span.*?>|<h.>Downloads.*FAQ<\/a>\.|<i>Links valid.*UTC<\/i>.*/', '', $out);
$out = preg_replace('/<input.*?>/', '&nbsp;', $out);
$out = preg_replace('/<\/div>|<\/span>/', '', $out);
$out = preg_replace('/button button-long button-flat button-purple/', 'btn btn-primary', $out, 1);
$out = str_replace('button button-long button-flat button-purple', 'btn btn-default', $out);
$out = str_replace('<button class="button-flat button-purple modal-dismiss">Close</button>', '', $out);
$out = str_replace('Unknown Download', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download', $out);
$out = str_replace('IsoX64 Download', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> 64-bit', $out);
$out = str_replace('IsoX86 Download', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> 32-bit', $out);

preg_match_all('/<a class="btn.*?href="http.*?">/', $out, $isoName);
$isoNameTmp = $isoName[0];
$isoName = preg_replace('/<a class="btn.*com\/pr\/|\?t=.*/', '', $isoNameTmp);

$out = preg_replace('/.*<h2><\/h2>/', '<h3><span class="glyphicon glyphicon-file" aria-hidden="true"></span> ' . $isoName[0] . ' [?]</h3>', $out);
$out = preg_replace('/.*<h2>/', '<h3><span class="glyphicon glyphicon-file" aria-hidden="true"></span> ', $out);
$out = str_replace('</h2>', "</h3>", $out);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TechBench downloads</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <style>body{font-family: "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif; padding-top: 50px;} .content {padding: 30px 15px;} .modal-content {padding: 20px;}</style>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">TechBench dump</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">TechBench dump</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href=".">Home</a></li>
                        <li><a href="https://gist.github.com/mkuba50/27c909501cbc2a4f169be4b4075a66ff">Gist</a></li>
                        <li><a href="https://github.com/mkuba50/techbench-dump">GitHub repository</a></li>
                        <li class="active"><a href="#">Downloads</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>

        <div class="container">
            <div class="content">

                <h1>TechBench downloads</h1>
        
                <?php echo $out; ?> 

                <div class="alert alert-success">
                    <h4><span class="glyphicon glyphicon-time" aria-hidden="true"></span> Links expiration</h4>
                    <p>Links are valid for 24 hours from time of creation.<br>
                    Links will expire: <b><?php echo gmdate("Y-m-d H:i:s e", $expire); ?></b></p>
                </div>
                
                <div class="alert alert-info" style="margin-top: 1.5em">
                    <h4><span class="glyphicon glyphicon-link" aria-hidden="true"></span> Direct download links</h4>
                    <p>Need to share a link with someone? Use those links below, which generate fresh download link on the fly.</p>
                    <pre style="margin-top: 1em"><code><?php 
                        foreach ($isoName as &$iso)
                        {
                            echo 'https://mdl-tb.ct8.pl/getDirect.php?fileName='.$iso."\n";
                        }
                    ?></code></pre>
                </div>
            </div>
        </div><!-- /.container -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
