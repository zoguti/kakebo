<?php
  $db_name='sample.db';
  $ext=file_exists('enq.db');
  $eMsg="";


  if(isset($_POST['submit'])||$_POST['rireki']){

    if(isset($_POST['submit'])){
      if(empty($_POST['mokuteki'])&&$button){
        $eMsg="目的を入力してください<br>";
      }
      elseif(empty(($_POST['nedan']))){
        $eMsg="値段を入力してください<br>";
      }
      elseif(!ctype_digit($_POST['nedan'])){
        $eMsg="数字で入力してください<br>";
      }
    }

    $db=new




  }


 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>家計簿</title>
    <link rel="stylesheet" href="/css/index.css">
  </head>
  <body>
    <?php
      if($eMsg){
        echo "<div id='eMsg'>".$eMsg."</div>";
      }
     ?>
    <form action="index.php" method="post">

      目的:<input type="text" name="mokuteki"><br>
      値段:<input type="text" name="nedan">

      <input type="submit" name="submit" value="入力">
    </form >


    履歴:
    <form action="index.php" method="post">
      <input type="submit" name="rireki" value="見る">
    </form>

  </body>
</html>
