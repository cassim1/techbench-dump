<?php
$fileName = isset($_GET['fileName']) ? $_GET['fileName'] : 'Win7_Pro_SP1_English_x64.iso';

$req = curl_init("http://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=160bb813-f54e-4e9f-bffc-38c6eb56e061&host=www.microsoft.com&segments=software-download%2cwindows10&query=&action=GetProductDownloadLinkForFriendlyFileName&friendlyFileName=" . urlencode($fileName));

curl_setopt($req, CURLOPT_HEADER, 0);
curl_setopt($req, CURLOPT_REFERER, "https://www.microsoft.com/en-us/software-download/windows10ISO");
curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

$out = curl_exec($req);

$out = str_replace('<div id="control"><div id="html"><div xmlns:mscom="http://schemas.microsoft.com/CMSvNext" xmlns:md="http://schemas.microsoft.com/mscom-data" class="CSPvNext " xmlns="http://www.w3.org/1999/xhtml"><span class="page-data-sources"></span>

        <script>/*<![CDATA[*/var softwareDownload=softwareDownload||{};softwareDownload.productDownload={uri:"', '<a class="btn btn-primary" href="', $out);
$out = str_replace('",downloadType:"', '">', $out);
$out = str_replace('"}/*]]>*/</script>


</div></div></div>', '</a>', $out);
$out = str_replace('Unknown', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download', $out);
$out = str_replace('IsoX64', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> 64-bit', $out);
$out = str_replace('IsoX86', '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> 32-bit', $out);
$out = str_replace('<button class="button-flat button-purple modal-dismiss">Close</button>', '', $out);

curl_close($req);
$expire = time() + 86400;
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

        <h1 class="uk-heading-line uk-text-center"><span>TechBench downloads</span></h1>

        <?php
        echo "<h3><span class=\"glyphicon glyphicon-file\" aria-hidden=\"true\"></span> $fileName</h3>";
        echo $out;
        ?>

        <p style="padding-top: 1.5em"><i>Links valid for 24 hours from time of creation.<br>
        <?php echo "Links expire: " . gmdate("n/j/Y g:i:s A e", $expire); ?></i></p>
        
        <div class="alert alert-warning" style="margin-top: 1.5em">
            <p><b>NOTE:</b> This website <b>does not</b> check if file exists on Microsoft servers</p>
        </div>

      </div>
    </div><!-- /.container -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </body>
</html>
