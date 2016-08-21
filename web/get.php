<?php
$skuId = isset($_GET['skuId']) ? $_GET['skuId'] : '6PC-00020';

$req = curl_init("http://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=GetProductDownloadLinksBySku&skuId=" . urlencode($skuId));

curl_setopt($req, CURLOPT_HEADER, 0);
curl_setopt($req, CURLOPT_REFERER, "https://www.microsoft.com/en-us/software-download/windows10ISO");
curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

$out = curl_exec($req);

$out = str_replace('</div></div>
									<div class="row-fluid" data-cols="1" data-view1="1" data-view2="1" data-view3="1" data-view4="1"><div class="span bp0-col-1-1 bp1-col-1-1 bp2-col-1-1 bp3-col-1-1">', ' ', $out);

$out = preg_replace('/button button-long button-flat button-purple/', 'btn btn-primary', $out, 1);
$out = str_replace('button button-long button-flat button-purple', 'btn btn-default', $out);
$out = str_replace('<button class="button-flat button-purple modal-dismiss">Close</button>', '', $out);
$out = str_replace('<span class="product-download-type">IsoX64</span>', 'IsoX64', $out);
$out = str_replace('<span class="product-download-type">IsoX86</span>', 'IsoX86', $out);
$out = str_replace('<span class="product-download-type">Unknown</span>', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>', $out);
$out = str_replace('IsoX64 Download', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> 64-bit', $out);
$out = str_replace('IsoX86 Download', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> 32-bit', $out);
$out = str_replace('<h2>Downloads</h2>', '<h1>Downloads</h1>', $out);
$out = str_replace('<h2>', '<h3>', $out);
$out = str_replace('</h2>', '</h3>', $out);

curl_close($req);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TechBench downloads</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet">

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

				<?php echo $out; ?>

    </div><!-- /.container -->
  </body>
</html>
