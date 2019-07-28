<?php
  ini_set('max_execution_time', 300); //300 seconds = 5 minutes


  require_once('class_function/error.php');
  require_once('class_function/session.php');
  require_once('class_function/dbconfig.php');
  require_once('class_function/function.php');
  require('simple_html_dom/simple_html_dom.php');

  function price_stringify($str){
    $str = str_replace(',','',$str);
    $str = strip_tags($str);
    $str = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $str);
    $str = str_replace('Ks','',$str);
    $str = trim($str);
    return $str;
  }


// Geting URL From Category Table
  $rows = sql($DBH,"select * from category where scrapped = ?",array("false"),"rows");
  foreach ($rows as $row) {
      $id   = $row['id'];
      $url  = trim($row['url']);


      //$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
      $agent= 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2';
      $ch = curl_init();
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    	curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Language: en']);
    	$html = curl_exec($ch);

      $location = "dom_files/$id.txt";
      $myfile = file_put_contents($location, $html.PHP_EOL , FILE_APPEND | LOCK_EX);



      $html = file_get_html($location);
      //category name
      foreach($html->find('._2iem') as $e){
          $category_name = trim($e->innertext);
          $rows = sql($DBH,"update category set category_name = ? where id = ?",
          array($category_name,$id),"rows");
      }

      $product = array();
      foreach($html->find('.vTop div div div.ellipsis a') as $i => $e){
          $product[$i]['href'] = "https://www.facebook.com".$e->href;
          $product[$i]['name'] = trim($e->title);
      }
      foreach($html->find('.vTop div div div.ellipsis div.ellipsis') as $i => $e){
            $price = $e->innertext;

            if (!preg_match("/Ks/i", $price)) {//language
              $product[$i]['price'] = "Out of Stock";
            }else{
              $price_arr                = explode("was", $price);
              $product[$i]['price']     = price_stringify($price_arr[0]);
              $product[$i]['old_price'] = price_stringify($price_arr[1]);
            }
      }

    //die(json_encode($product));

      foreach($product as $p){

        $check_dup = sql($DBH,"select * from products where product_url = ?",array($p['href']),"rows");
        if(count($check_dup) == 0){
          $rows = sql($DBH,"insert into products
          (c_id,product_name,product_price,old_price,product_url)
          values
          (?,?,?,?,?) ",array($id,$p['name'],$p['price'],$p['old_price'],$p['href']),"rows");
          echo $p['href']." <b>INSERTED<b/><hr>";
        }else{
          echo $p['href']." <b>EXISTS<b/><hr>";
        }
      }

      $rows = sql($DBH,"update category set scrapped = ? where id = ?",
      array("true",$id),"rows");

  }
?>
