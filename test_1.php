<?php

require_once('class_function/error.php');
require_once('class_function/session.php');
require_once('class_function/dbconfig.php');
require_once('class_function/function.php');
require('simple_html_dom/simple_html_dom.php');


  $location = "dom_files/1.txt";

  $html = file_get_html($location);



foreach($html->find('.vTop div div div.ellipsis div.ellipsis') as $i => $e){
    $product[$i]['price'] = $e->innertext;
    $tem_price =$product[$i]['price'];
      $str_len = strlen($tem_price);
     echo "$i = ";

      if ($str_len < 12)
      {
        $tem_price =substr($tem_price,2,100);
         $tem_price = str_replace(',','',$tem_price);
        print_r ($tem_price );

      }
      elseif ($str_len > 12 AND $str_len <27) {
        $product[$i]['price']  = "Out of Stock";
        echo $product[$i]['price'];

      }
      elseif ($str_len >30) {
// the values is  ‎Ks4,100.00 ‎was Ks4,300.00
$string = $product[$i]['price'];
$tem_str =  explode("was", $string);
$price = substr($tem_str[0],11,100);
$price = str_replace(',','',$price);
$dis_price = substr($tem_str[1],3,100);
$dis_price =  str_replace(',','',$dis_price);
$product[$i]['price'] = $price;
///    $discount_price= $discount_price ;

print_r( str_replace(',','',$price) );

        echo  "    Discount price :";
        print_r($dis_price );


      //echo($tem_str[0]);

          // preg_match_all('/\s+/', $string, $matches);
          // $result = array_map('strlen', $matches[0]);
        //  $result = preg_split("/[\s,]+/", "$string");
      //  $result =  preg_split("!\d+!", "$string");
        // echo $result[0];

        //  preg_match_all('!\d+!', $string, $matches);
        //  print_r($matches);
        // echo $matches;

      }
       echo "<br>";

      echo $str_len;
     echo "<hr>";


}

 ?>
