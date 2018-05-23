# Subscribe HR DevTest

## Simple command line Point to Point connection checker.

Usage:
```
bash$ php runDevTest.php csvData.csv
```

The program requires device connection checks to be inputted as DeviceFrom DeviceTo Latency (**eg: A B 100**).

The program will iterate through an array holding the CSV data, checking both A to F and F to A. It will plot out each step between the two points and calculate the total time in ms.
