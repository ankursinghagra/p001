<?php
require_once('lib.php');
if(isset($_GET['folder'])){
    $tempdir = $_GET['folder'];
}

$explodedpath = explode('/',$tempdir);

$dir = $explodedpath[1].'/'.$explodedpath[2];

if(!is_dir('output/'.$explodedpath[2])) {
    mkdir('output/'.$explodedpath[2]);
}

// //get all the files under the respective folder.
function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}

$results = getDirContents($dir);

//The @array results variable have all the files present under the uploaded package.

//Load the getCompleteCourseInfo file to extract the first level content.
$completecourseinfo = $dir.'/datas/getCompleteCourseInfo.js';

$completecourseinfodata = file_get_contents($completecourseinfo);

$ccidecoded = json_decode($completecourseinfodata);

if(!empty($ccidecoded)){
    foreach ($ccidecoded->chapters as $key => $value) {
       create_directories($explodedpath[2],$value, null);
    }
}
?>

<!DOCTYPE html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Upload and unzip file in webserver</title>
</head>
<style>
  body{
    margin: 100px 0 0 0;
    font-family: verdana, Arial;
    font-size: 12px;
    color: #000000;
  }
  .box{
    width: 500px;
    margin:0 auto;
    border:1px solid #CCCCCC;
  }
  .heading{
    height: 30px;
    border-bottom: 1px solid #CCCCCC;
    background: #CACACA;
    font-family: verdana, Arial;
    font-size: 12px;
    text-align: center;
    line-height: 30px;
  }
  .msg{
    text-align: center;
    line-height: 30px;
    color: #FF0000;
  }
  .form_field{
    margin: 20px 0 0 20px;
  }
  label{
    width: 130px;
    padding: 0 20px 0 10px;
  }
  .upload{
    margin: 10px 0 0 190px;
  }
  .back{
    text-align: center;
  }
</style> 
<body>

  <div class="box">
    <div class="heading">Download the Package</div>
    <div class="form_field">
      <form enctype="multipart/form-data" method="post" action="download.php">
        <input type="hidden" name="foldername" value="<?php echo $explodedpath[2]; ?>">
        <input type="submit" name="submit" value="Download" class="upload"> <br><br>
      </form>
    </div>
    <div class="back"><a href="<?=str_replace( 'extract.php', '', url());?>">Back</a></div>
  </div>
</body>
</html>
