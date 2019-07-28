<?php
 ini_set('max_execution_time', 300);

require_once('class_function/error.php');
require_once('class_function/session.php');
require_once('class_function/dbconfig.php');
require_once('class_function/function.php');
require('simple_html_dom/simple_html_dom.php');

function is_image($path)
{
	$a = getimagesize($path);
	$image_type = $a[2];

	if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
	{
		return true;
	}
	return false;
}

function make_image_url($url){
  $url = str_replace("amp;","",$url);
  return $url;
}
function make_image_name($product_name){
  $product_name = str_replace("amp;","",$product_name);
  $product_name = str_replace("&#039;s","",$product_name);
  return $product_name;
}

$total = 0;
$true = 0;
$false = 0;

$rows = sql($DBH,"select * from products",array(),"rows");//3200
foreach ($rows as $row) {
    $total++;
    $id   = $row['id'];
    $image  = trim($row['product_image']);


    if(getimagesize($image) == true){
      echo $image."TRUE <br>";
      $true++;
    }else{
      echo $image."FALSE <br>";
      $false++;
      //unlink ($image);
      //$rows = sql($DBH,"update products set scrapped = ? where id = ?",array("false",$id),"rows");//3200
    }
    break;
}

echo "total:$total true:$true false:$false";
?>
