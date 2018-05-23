<?php

class SubscribeHrTest {

private $csvData = [];

public function __construct($file){

  echo "File Uploaded: ".$file.PHP_EOL;

  $this->csvData = array_map('str_getcsv', file($file));
  if(!is_array($this->csvData) || count($this->csvData)==0){
    echo "File upload failed or no data.".PHP_EOL;
    exit();
  }
      array_shift($this->csvData);
}

public function runTest(){
  print_r($this->csvData);
}




}
