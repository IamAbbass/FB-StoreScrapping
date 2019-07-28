<?php
  require_once('class_function/error.php');
  require_once('class_function/session.php');
  require_once('class_function/dbconfig.php');
  require_once('class_function/function.php');
  require('simple_html_dom/simple_html_dom.php');

  $id = $_GET['id'];
  echo file_get_contents("dom_files/$id.txt");
?>
