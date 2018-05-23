<?php
require_once "Helpers.php";

class SubscribeHrTest {

  private $csvData = [];
  private static $devices = ['A','B','C','D','E','F'];
  private $usedKeys = [];
  private $firstKey = null;

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
    $this->usedKeys = [];
    $this->firstKey = null;
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
              $overLatency = false;

              //Loop forward through CSV Data checking if connections match input.

              for($i=0; $i<count($this->csvData); $i++){
                $doDeviceCheck = $this->doDeviceConnectionCheck($output, $checkDevice, $latency, $device1, $device2, $i, false);
                if($doDeviceCheck===true){

                  if($latency > $max_latency){
                    //connections exist but the latency in longer than the max latency given by the user. Below allows a further check.
                    $overLatency = true;
                    $this->usedKeys[] = $this->firstKey;
                    $checkDevice = '';
                    break;
                  }

                  break;
                } else if($doDeviceCheck==='continue'){
                  continue;
                }
                if($latency > $max_latency){
                  $checkDevice = '';
                  break;
                }
              }

              //below allows a further check if a previous connection match returned a latency longer than the inputted latency by the user.

              if($overLatency==true){
                $this->resetVals($output, $checkDevice, $latency, $device1, $device2,$overLatency,$inputData);
                $this->checkOverLatency($output, $checkDevice, $latency, $max_latency, $device1, $device2, false);
              }

              //If the forward iteration returns no results, we attempt to iterate backwards through the CSV data.
              if($checkDevice == ''){
                $this->resetVals($output, $checkDevice, $latency, $device1, $device2,$overLatency,$inputData);
                $this->usedKeys = [];
                $this->firstKey = null;
                //Loop backwards through CSV Data checking if connections match input.

                for($i=max(array_keys($this->csvData)); $i>=0; $i--){
                  $doDeviceCheck = $this->doDeviceConnectionCheck($output, $checkDevice, $latency, $device1, $device2, $i, true);
                  if($doDeviceCheck===true){

                    if($latency > $max_latency){
                      $overLatency = true;
                      $this->usedKeys[] = $this->firstKey;
                      $checkDevice = '';
                      break;
                    }

                    break;
                  } else if($doDeviceCheck==='continue'){

                    continue;
                  }
                  if($latency > $max_latency){
                    $checkDevice = '';
                    break;
                  }
                }

                if($overLatency==true){
                  $this->resetVals($output, $checkDevice, $latency, $device1, $device2,$overLatency,$inputData);
                  $this->checkOverLatency($output, $checkDevice, $latency,$max_latency, $device1, $device2, true);
                }

              }
              $output = ($checkDevice == '') ? "Path Not Found" : $output;
              echo "Connection: ".$output. PHP_EOL;
              $this->waitForUser();
            } else {
              echo "Latency must be numeric and greater than 0.".PHP_EOL;
              exit();
            }
          } else {
            echo "Incorrect second device definition. Please use A to F.".PHP_EOL;
            exit();
          }
        } else {
          echo "Incorrect first device definition. Please use A to F.".PHP_EOL;
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

  private function resetVals(& $output, & $checkDevice, & $latency, & $device1,& $device2,& $overLatency,$inputData){
    $device1 = $inputData[0];
    $device2 = $inputData[1];

    $output = '';
    $output .= $device1;
    $latency = 0;
    $checkDevice = '';
    $overLatency = false;
  }

  private function doDeviceConnectionCheck(& $output, & $checkDevice, & $latency, & $device1,& $device2, $i, $reverse){

    if($reverse==true){
      $firstDevice = 1;
      $secondDevice = 0;
    } else {
      $firstDevice = 0;
      $secondDevice = 1;
    }

    if($device1 == $this->csvData[$i][$firstDevice]){
      $this->firstKey = (is_null($this->firstKey))? $i : $this->firstKey;

      if(in_array($i,$this->usedKeys))
      return 'continue';

      $latency = $latency + $this->csvData[$i][2];
      if($this->csvData[$i][$secondDevice] == $device2){
        $output .= ' => '.$this->csvData[$i][$secondDevice].' => '.$latency;
        $checkDevice = $this->csvData[$i][$secondDevice];
        return true;
      }else{
        $output .= ' => '.$this->csvData[$i][$secondDevice];
      }
      $device1 = $this->csvData[$i][$secondDevice];
    }
  }

  private function checkOverLatency(& $output, & $checkDevice, $latency, $max_latency, $device1, $device2, $reverse){
    for($i=0; $i<count($this->csvData); $i++){
      if($this->doDeviceConnectionCheck($output, $checkDevice, $latency, $device1, $device2, $i, $reverse)===true){
        if($latency > $max_latency){
          $checkDevice = '';
          break;
        }
        break;
      }
      if($latency > $max_latency){
        break;
      }
    }
  }
}
