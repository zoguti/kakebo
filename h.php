<?php
$user='zoguti';
$password='Kyosuke';

$dbName='sample';

$host='localhost:8889';
$dsn="mysql:host={$host};dbname={$dbName};charset=utf8";

function h($str){
  if(is_array($str)){
    return array_map('h',$str);
  }else{
    return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
  }
}

function rireki(){
  $user='zoguti';
  $password='Kyosuke';

  $dbName='sample';

  $host='localhost:8889';
  $dsn="mysql:host={$host};dbname={$dbName};charset=utf8";


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

function datesort(){
  $user='zoguti';
  $password='Kyosuke';

  $dbName='sample';

  $host='localhost:8889';
  $dsn="mysql:host={$host};dbname={$dbName};charset=utf8";
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

function nitizi(){//年、月でまとめる
  $user='zoguti';
  $password='Kyosuke';

  $dbName='sample';

  $host='localhost:8889';
  $dsn="mysql:host={$host};dbname={$dbName};charset=utf8";

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

    echo "<th>","日付","</th>";


    echo "<th>","合計","</th>";

    echo "<tr></thead>";
    echo "<tbody>";
    foreach ($result as $key => $value) {
      $sort[$key]=$value['date'];//$resultと同じ連想多重配列

    }
    //日付順に並べる
    array_multisort($sort,SORT_ASC,$result);

    foreach($result as $key=>$value){
      $str = $value['date'];//文字列
      $cut = 3;//カットしたい文字数
      $replace = substr( $str , 0 , strlen($str)-$cut );//0000-00の形
      $result[$key]['date']=$replace;//代入
    }
    $array=[];//日付を入れる
    foreach($result as $key =>$value ){
      array_push($array,$value['date']);

    }
    $array=array_unique($array);//重複を避ける
    //var_dump($array);//日付が入っている
    $datesum=array();
    foreach($array as $key){
      $datesum=array_merge($datesum,array($key=>0));//日付をキーに
    }
    foreach($array as $key){

      foreach($result as $row){
        if($key==$row['date']){//日付一致
          if($row['type']=="収入"){
            $datesum[$key]+=$row['nedan'];
          }else{
            $datesum[$key]-=$row['nedan'];
          }
        }
      }
    }

    //表示
    foreach($array as $row){
      echo "<tr>";
      echo "<td>",$row,"</td>";


      echo "<td>",$datesum[$row],"</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";



  }catch(Exception $e){
    echo '<span class=error>エラーがありました。</span><br>';
    //echo $e->getMessage();
    exit();
  }
}

function nenzi(){//年、月でまとめる
  $user='zoguti';
  $password='Kyosuke';

  $dbName='sample';

  $host='localhost:8889';
  $dsn="mysql:host={$host};dbname={$dbName};charset=utf8";

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

    echo "<th>","年","</th>";


    echo "<th>","合計","</th>";

    echo "<tr></thead>";
    echo "<tbody>";
    foreach ($result as $key => $value) {
      $sort[$key]=$value['date'];//$resultと同じ連想多重配列

    }
    //日付順に並べる
    array_multisort($sort,SORT_ASC,$result);

    foreach($result as $key=>$value){
      $str = $value['date'];//文字列
      $cut = 6;//カットしたい文字数
      $replace = substr( $str , 0 , strlen($str)-$cut );//0000の形
      $result[$key]['date']=$replace;//代入
    }


    $array=[];//日付を入れる
    foreach($result as $key =>$value ){
      array_push($array,$value['date']);

    }
    $array=array_unique($array);//重複を避ける

    $datesum=array();
    foreach($array as $key){

      $datesum[$key]=0;
    }



    foreach($array as $key){


      foreach($result as $row){

        if($key==$row['date']){//日付一致
          if($row['type']=="収入"){
            $datesum[$key]+=$row['nedan'];
          }else{
            $datesum[$key]-=$row['nedan'];
          }
        }
      }
    }
    //表示
    foreach($array as $row){
      echo "<tr>";
      echo "<td>",$row,"</td>";


      echo "<td>",$datesum[$row],"</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";



  }catch(Exception $e){
    echo '<span class=error>エラーがありました。</span><br>';
    //echo $e->getMessage();
    exit();
  }
}





function delete(){
  $user='zoguti';
  $password='Kyosuke';

  $dbName='sample';

  $host='localhost:8889';
  $dsn="mysql:host={$host};dbname={$dbName};charset=utf8";
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
