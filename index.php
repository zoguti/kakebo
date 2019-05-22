<?php

//
$user='zoguti';
$password='Kyosuke';

$dbName='sample';

$host='localhost:8889';
$dsn="mysql:host={$host};dbname={$dbName};charset=utf8";


$eMsg="";
if(!$eMsg){
  echo "<div id='eMsg'>".$eMsg."</div>";
}

require_once 'h.php';

  if(isset($_POST['submit'])){//データベースに入れる
      $mokuteki=h($_POST['mokuteki']);
      $nedan=h($_POST['nedan']);
      $pattern=preg_match("#^[0-9]{4}/[0-9]{2}/[0-9]{2}$#",$_POST['date']);

      $sum=0;
      if($_POST['type']=="収入"){//足し算か引き算か
          $type="収入";

      }else{
          $type="支出";

      }
      if(empty($_POST['date'])){
          $eMsg="日付を入力してください<br>";
      }
      elseif(!$pattern){
        $eMsg="日付を形式通りに入力してください";
      }
      elseif(empty($mokuteki)){
        $eMsg="目的を入力してください<br>";
      }
      elseif(empty($nedan)){
        $eMsg="値段を入力してください<br>";
      }
      elseif(!ctype_digit($nedan)){
        $eMsg="数字で入力してください<br>";
      }



      if(!$eMsg){
        if($_POST['date']){
          $dateArray=explode("/",$_POST['date']);
          $theYear=$dateArray[0];
          $theMonth=$dateArray[1];
          $theDay=$dateArray[2];
          $Day=$theYear."-".$theMonth."-".$theDay;

        }

        try{
          $pdo=new PDO($dsn,$user,$password);

          $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
          $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);






          $sql="INSERT kakei (date,mokuteki,nedan,type) VALUES (:date,:mokuteki,:nedan,:type)";

          $stm=$pdo->prepare($sql);
            $stm->bindValue(':date',$Day,PDO::PARAM_STR);
          $stm->bindValue(':mokuteki',$mokuteki,PDO::PARAM_STR);
          $stm->bindValue(':nedan',$nedan,PDO::PARAM_INT);
          $stm->bindValue(':type',$type,PDO::PARAM_INT);




          $stm->execute();



        }catch(Exception $e){
          echo '<span class=error>エラーがありました。</span><br>';
          echo $e->getMessage();
          exit();
        }
      }
  }



 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>家計簿</title>

     <link rel="stylesheet" href="../css/index.css" type="test/css">
   </head>
   <body>
     <?php
     echo "<div id='eMsg'>".$eMsg."</div>";

      ?>
      日付は****/**/**の形式で入力してください<br>
     <div id="form-1">
     <form action="index.php" method="post">

       日付:<input type="date" name="date" placeholder="2000/01/01"><br>
       目的:<input type="text" name="mokuteki"><br>
       値段:<input type="text" name="nedan">
       <label><input type="radio" name="type" value="収入" checked>収入</label>
       <label><input type="radio" name="type" value="支出">支出</label><br>

       <input type="submit" name="submit" value="入力">
     </form >
   </div>
     削除する場合履歴を表示させ,idを入力してください<br>


     <h3>履歴<h3>


     <form action="index.php" method="post">
       <input class="button" type="submit" name="rireki" value="履歴">
       <input class="button" type="submit" name="datesort" value="日付順">
       <input class="button" type="submit" name="nitizi" value="月次集計">
       <input class="button" type="submit" name="nenzi" value="年次集計">
       
     </form>


   </body>
 </html>




    <?php

    if(isset($_POST['rireki'])) {
      rireki();
    }
    if(isset($_POST['datesort'])){
      datesort();
    }
    if(isset($_POST['nitizi'])){
      nitizi();

    }
    if(isset($_POST['nenzi'])){
      nenzi();
    }

    if(isset($_POST['delete'])){
      delete();
    }
     ?>

     <br>
     <br>
  </body>
</html>
