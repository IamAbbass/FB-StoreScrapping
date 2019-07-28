<?php
    require_once('class_function/error.php');
    require_once('class_function/session.php');
    require_once('class_function/dbconfig.php');
    require_once('class_function/function.php');

    //$txt = file_get_contents("category_dom.txt");
    //echo $txt;

    $fn = fopen("category_dom.txt","r");

    while(! feof($fn))  {
    	$result = fgets($fn);


      $result = substr($result,8,1000);

      if (preg_match("/cid=/i", $result)) {

        $result = trim($result);

          $check_dup = sql($DBH,"select * from category where url = ?",array($result),"rows");
          if(count($check_dup) == 0){
            sql($DBH,"insert into category (url) values (?)",array($result),"rows");
            echo $result." <b>INSERTED<b/><hr>";
          }else{
            echo $result." <b>EXISTS<b/><hr>";
          }


      } else {
          //echo "A match was not found.";
      }


    }

    fclose($fn);
?>
