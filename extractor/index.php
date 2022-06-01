<?php
$myMsg="";
function recursive_dir($dir) {
  foreach(scandir($dir) as $file) {
    if ('.' === $file || '..' === $file) continue;
    if (is_dir("$dir/$file")) recursive_dir("$dir/$file");
    else unlink("$dir/$file");
  }
  rmdir($dir);
}


if($_FILES){
  if($_FILES["zip_file"]["name"]) {
    $filename = $_FILES["zip_file"]["name"];
    $source = $_FILES["zip_file"]["tmp_name"];
    $type = $_FILES["zip_file"]["type"];

    $name = explode(".", $filename);
    $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
    foreach($accepted_types as $mime_type) {
      if($mime_type == $type) {
        $okay = true;
        break;
      }
    }

    $continue = strtolower($name[1]) == 'zip' ? true : false;
    if(!$continue) {
      $myMsg = "Please upload a valid .zip file.";
    }

    /* PHP current path */
    $path = dirname(__FILE__).'/uploads/'; 
    $filenoext = basename ($filename, '.zip'); 
    $filenoext = basename ($filenoext, '.ZIP');

$myDir = $path . $filenoext; // target directory
$myFile = $path . $filename; // target zip file


if (is_dir($myDir)) recursive_dir ( $myDir);

mkdir($myDir, 0777);

/* here it is really happening */

if(move_uploaded_file($source, $myFile)) {
  $zip = new ZipArchive();
  $x = $zip->open($myFile); // open the zip file to extract
  if ($x === true) {
  $zip->extractTo($myDir); // place in the directory with same name
  $zip->close();
  unlink($myFile);
}
$myMsg = "Your .zip file uploaded and unziped.<br/><a href='extract.php?folder=".$myDir."'>Proceed</a>";
} else {  
  $myMsg = "There was a problem with the upload.";
}
}
} 
?>
<!DOCTYPE html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Upload and unzip file in webserver</title>
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('input[type=submit]').click(function(e){
        if($('input[type=file]').val().split('.').pop().toLowerCase() == 'zip'){
          setTimeout(function(){
            $('input[type=submit]').attr('disabled',true);
          },30);
        }else{
          alert('Please select correct file format before uploading.')
          e.preventDefault();
        }
      });
      $('input[type=file]').on('change click', function(){
        $('input[type=submit]').removeAttr('disabled');
      })
    });
  </script>
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
    margin: 10px 0 0 200px;
  }
</style> 
<body>

  <div class="box">
    <div class="heading">Upload File and Unzip</div>
    <div class="msg"><?php  if($myMsg) echo "<p>$myMsg</p>"; ?></div>
    <div class="form_field">
      <form enctype="multipart/form-data" method="post" action="">
        <label>Upload Zip File: </label> <input type="file" name="zip_file" accept=".zip">
        <br><br>
        <input type="submit" name="submit" value="Upload" class="upload"> <br><br>
      </form>
    </div>
  </div>
</body>
</html>