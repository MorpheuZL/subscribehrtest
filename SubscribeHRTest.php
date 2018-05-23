<?php
require_once "Helpers.php";
class SubscribeHrTest {

  private $csvData = [];
  private static $devices = ['A','B','C','D','E','F'];

  public function __construct($file){
    if($file==""){
      echo "No file uploaded.".PHP_EOL;
      exit();
    }

    echo "File Uploaded: ".$file.PHP_EOL;

    $this->csvData = array_map('str_getcsv', file($file));
    if(!is_array($this->csvData) || count($this->csvData)==0){
      echo "File upload failed or no data.".PHP_EOL;
      exit();
    }
    array_shift($this->csvData);
    echo "Device connections: ". count($this->csvData).PHP_EOL;
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

        $inputData = explode(' ',$input);

        if(self::checkValidDevices($inputData[0])){
          if(self::checkValidDevices($inputData[1])){
            if(is_numeric(trim($inputData[2]))){

              $device1 = $inputData[0];
              $device2 = $inputData[1];
              $max_latency = $inputData[2];

              $output = '';
              $output .= $device1;
              $latency = 0;
              $checkDevice = '';
              for($i=0; $i<count($this->csvData); $i++){
                if($this->doDeviceConnectionCheck($output, $checkDevice, $latency, $device1, $device2, $i)===true){
                  break;
                }
                if($latency > $max_latency){break;}
              }
              echo $checkDevice."-----------".PHP_EOL;
              if($checkDevice == ''){
                $device1 = $inputData[0];
                $device2 = $inputData[1];
                $max_latency = $inputData[2];

                $output = '';
                $output .= $device1;
                $latency = 0;
                $checkDevice = '';
                for($i=count($this->csvData)-1; $i>0; $i++){
                  if($this->doDeviceConnectionCheck($output, $checkDevice, $latency, $device1, $device2, $i)===true){
                    break;
                  }
                  if($latency > $max_latency){break;}
                }
              }
              $output = $checkDevice == '' ? "Path Not Found" : $output;
              print $output. PHP_EOL;

            } else {
              echo "Latency must be numeric and greater than 0.".PHP_EOL;
              exit();
            }
          } else {
            echo "Incorrect second device definition.".PHP_EOL;
            exit();
          }
        } else {
          echo "Incorrect first device definition.".PHP_EOL;
          exit();
        }
      }
    }
  }

  private static function checkValidDevices($device){
    if(in_array(trim($device),self::$devices))
    return true;

    return false;
  }

  private function doDeviceConnectionCheck(& $output, & $checkDevice, & $latency, & $device1,& $device2, $i){
    if($device1 == $this->csvData[$i][0]){
      $latency = $latency + $this->csvData[$i][2];
      if($this->csvData[$i][1] == $device2){
        $output .= ' => '.$this->csvData[$i][1].' => '.$latency;
        $checkDevice = $this->csvData[$i][1];
        return true;
      }else{
        $output .= ' => '.$this->csvData[$i][1].' => ';
      }
      $device1 = $this->csvData[$i][1];
    }
  }


}
