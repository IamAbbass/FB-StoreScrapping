<?php

 ini_set('max_execution_time', 300);



require_once('class_function/error.php');
require_once('class_function/session.php');
require_once('class_function/dbconfig.php');
require_once('class_function/function.php');
require('simple_html_dom/simple_html_dom.php');

function make_image_url($url){
  $url = str_replace("amp;","",$url);
  return $url;
}

function make_image_name($product_name){
  $product_name = str_replace("amp;","",$product_name);
  $product_name = str_replace("&#039;s","",$product_name);
  return $product_name;
}

function grab_image($url,$saveto){
    $ch = curl_init ($url);


    $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_VERBOSE, true);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_USERAGENT, $agent);
     curl_setopt($ch, CURLOPT_URL,$url);



    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}

//echo make_image_url("https://scontent.fkhi15-1.fna.fbcdn.net/v/t45.5328-4/64731298_2857033274338641_3990220206499692544_n.jpg?_nc_cat=108&amp;_nc_oc=AQk4ODhzbAwd14aBbQJfNX_EjOfDVarx7UCT_ooBnAuj3phloZJBkprDfwGHdJq94WE&amp;_nc_ht=scontent.fkhi15-1.fna&amp;oh=c5c35189fd5c47f6c59218766927b7c3&amp;oe=5D8424E1");
//exit;



// Geting URL From products Table
$rows = sql($DBH,"select * from products where scrapped = ?",array("false"),"rows");//3200
foreach ($rows as $row) {
    $id   = $row['id'];
    $product_name   = $row['product_name'];
    $url  = trim($row['product_url']);

    $url = str_replace("www","web",$url);

    $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
    //$agent= 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2';
    //$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
    //$agent = $_SERVER['HTTP_USER_AGENT'];


       $ch = curl_init();
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_VERBOSE, true);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_USERAGENT, $agent);
       curl_setopt($ch, CURLOPT_URL,$url);
       $html = curl_exec($ch);

       $location = "images/urls/$id.txt";
       $myfile = file_put_contents($location, $html.PHP_EOL , FILE_APPEND | LOCK_EX);

    //$location = "images/urls/1.html";
    $html = file_get_html($location);

    if(strlen($html) == 0){
      die("no response from ID $id , url $url");
    }


    //Get and set Image URL
    foreach($html->find('meta[property="og:image"]') as $e){
        $image = $e->content;
        $image =  make_image_url($image);
        $product_name = make_image_name($product_name);

        $img_path = "images/".$id.".png";
        grab_image($image,$img_path);
    }

    // Store Image path in data base
    $rows = sql($DBH,"update products set product_image = ? where id = ?",
    array($img_path,$id),"rows");

    // Removing &amp; in product name
    $rows = sql($DBH,"update products set product_name = ? where id = ?",
    array($product_name,$id),"rows");

    $rows = sql($DBH,"update products set scrapped = ? where id = ?",
    array("true",$id),"rows");

    echo "$id Updated";
    echo "<a href='$image'>image</a> <a href='$img_path'>img_path</a> <hr>";

    break;

}

echo "<br>Finished !";

?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
  $(document).ready(function(){
    setTimeout(function(){
      location.reload();
    },1000);
  });
</script>
