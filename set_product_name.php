<?php
	require_once('class_function/error.php');

	require_once('class_function/dbconfig.php');

	function translate($from_lan, $to_lan, $text){
    $json = json_decode(file_get_contents('https://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=' . urlencode($text) . '&langpair=' . $from_lan . '|' . $to_lan));
    $translated_text = $json->responseData->translatedText;

    return $translated_text;
	}

	$fr_lang = "burmese";
	$to_lang = "english";

  //  $rows =   sql($DBH,"select * from products",array(),"rows");
  //    $product_name = $rows['product_name'];

    $product_name="&#x1031;&#x101b;&#x103c;&#x103d;&#x1000;&#x103a;&#x103c;&#x1014;&#x1039;&#x1038; &#x101e;&#x1018;&#x102c;&#x1040;&#x1000;&#x1019;&#x1039;&#x1038;&#x1031;&#x1007;&#x102c;&#x1039;&#x1006;&#x102e;  64ml";

     

   $product_name = translate($fr_lang, $to_lang, $product_name);

   	die($product_name);
     //urldecode($product_name);
     echo $product_name;




   
 ?>
