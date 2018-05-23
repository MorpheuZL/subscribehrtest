<?php
require_once "Helpers.php";
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
  echo "Enter Device From, Device To and Latency separated by spaces (eg: A B 100): ";
  $input = fgets(STDIN);

  $result = $this->handleUserInput($input);
  $this->waitForUser();
}

private function waitForUser(){
  echo "Check other devices? (Yes/Quit): ";
  $input = fgets(STDIN);
  $this->handleUserInput($input);
}

private function handleUserInput($input){
    if(Helpers::cleanInput($input) == 'yes' || Helpers::cleanInput($input) == ''){
      $this->runTest();
    } else {
      if(Helpers::cleanInput($input) == 'quit'){
        echo "Goodbye.".PHP_EOL;
        exit();
      } else {
          echo $input;

      }
    }
}

}
