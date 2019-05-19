<?php
function h($str){
  if(is_array($str)){
    return array_map('h',$str);
  }else{
    return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
  }
}
 ?>
