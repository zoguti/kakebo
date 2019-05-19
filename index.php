<?php
$user='zoguti';
$password='Kyosuke';

$dbName='sample';

$host='localhost:8889';
$dsn="mysql:host={$host};dbname={$dbName};charset=utf8";


$eMsg="";

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
  //削除
  if(isset($_POST['delete'])){
    $id=$_POST['id'];

    try{
      $pdo=new PDO($dsn,$user,$password);

      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);




      $sql="DELETE FROM kakei WHERE id='$id'";
      $stm=$pdo->prepare($sql);

      $stm->execute();



    }catch(Exception $e){
      echo '<span class=error>エラーがありました。</span><br>';
      //echo $e->getMessage();
      exit();
    }
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
      日付は****/**/**の形式で入力してください<br>
      日付:<input type="date" name="date" placeholder="2000/01/01"><br>
      目的:<input type="text" name="mokuteki"><br>
      値段:<input type="text" name="nedan">
      <label><input type="radio" name="type" value="収入" checked>収入</label>
      <label><input type="radio" name="type" value="支出">支出</label><br>

      <input type="submit" name="submit" value="入力">
    </form >
    削除する場合履歴を表示させ,idを入力してください<br>
    履歴:
    <form action="index.php" method="post">
      <input type="submit" name="rireki" value="見る">

      <input type="submit" name="datesort" value="日付順">
    </form>
    <?php //日付順
      if(isset($_POST['datesort'])){

        try{
          $pdo=new PDO($dsn,$user,$password);

          $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
          $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

          $sql="SELECT * FROM kakei";
          $stm=$pdo->prepare($sql);

          $stm->execute();

          $result=$stm->fetchAll(PDO::FETCH_ASSOC);

          echo "<table>";
          echo "<thead><tr>";
          echo "<th>","id","</th>";
          echo "<th>","日付","</th>";
          echo "<th>","目的","</th>";
          echo "<th>","値段","</th>";
          echo "<th>","type","</th>";
          echo "<th>","合計","</th>";

          echo "<tr></thead>";
          echo "<tbody>";
          foreach ($result as $key => $value) {
            $sort[$key]=$value['date'];//$resultと同じ連想多重配列
            print_r($key);
          }
          array_multisort($sort,SORT_ASC,$result);
        //  print_r($result);
        //print_r($result[0]);
          $sum=0;
          foreach($result as $row){
            $nedan=$row['nedan'];
            echo "<tr>";

            echo "<td>",$row['id'],"</td>";
            echo "<td>",$row['date'],"</td>";
            echo "<td>",$row['mokuteki'],"</td>";
            echo "<td>",$row['nedan'],"</td>";
            echo "<td>",$row['type'],"</td>";

            if($row['type']=="支出"){
              $nedan=-1*$nedan;
            }
            $sum+=$nedan;
            echo "<td>",$sum,"</td>";
            echo "</tr>";
          }
          echo "</tbody>";
          echo "</table>";

      }catch (Exception $e){
        echo '<span class=error>エラーがありました。</span><br>';
        //echo $e->getMessage();
        exit();
    }
  }
     ?>

    <?php

    if(isset($_POST['rireki'])){
    try{
      $pdo=new PDO($dsn,$user,$password);

      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $sql="SELECT * FROM kakei";
      $stm=$pdo->prepare($sql);

      $stm->execute();

      $result=$stm->fetchAll(PDO::FETCH_ASSOC);

      echo "<table>";
      echo "<thead><tr>";
      echo "<th>","id","</th>";
      echo "<th>","日付","</th>";
      echo "<th>","目的","</th>";
      echo "<th>","値段","</th>";
      echo "<th>","type","</th>";
      echo "<th>","合計","</th>";

      echo "<tr></thead>";


      echo "<tbody>";
      $sum=0;
      foreach($result as $row){
        $nedan=$row['nedan'];
        echo "<tr>";

        echo "<td>",$row['id'],"</td>";
        echo "<td>",$row['date'],"</td>";
        echo "<td>",$row['mokuteki'],"</td>";
        echo "<td>",$row['nedan'],"</td>";
        echo "<td>",$row['type'],"</td>";

        if($row['type']=="支出"){
          $nedan=-1*$nedan;
        }
        $sum+=$nedan;
        echo "<td>",$sum,"</td>";
        echo "</tr>";
      }
      echo "</tbody>";
      echo "</table>";

      echo "削除:";
      echo "<form action='index.php' method='post'>";
      echo "id:<input type='text' name='id'><br>";
      echo "<input type='submit' name='delete' value='削除'>";
      echo "</form>";




      //接続を解除する
      $pdo=NULL;
    }catch (Exception $e){
      echo '<span class=error>エラーがありました。</span><br>';
      //echo $e->getMessage();
      exit();
    }
  }

     ?>



     <br>
     <br>
     加えたい機能:先月よりも使いすぎた場合の注意

  </body>
</html>
