<?php

class Helpers {
  public static function getDate(){
    return "[".date("Y-m-d H:i:s")."] ";
  }

  public static function cleanInput($input){
    return trim(strtolower($input));
  }
}
