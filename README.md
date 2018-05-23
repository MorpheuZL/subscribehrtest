# Subscribe HR DevTest

## Simple command line Point to Point connection checker.

Usage:
```
bash$ php runDevTest.php csvData.csv
```

The program requires device connection checks to be inputted as DeviceFrom DeviceTo Latency (**eg: A B 100**).

The program will iterate through an array holding the CSV data, checking both A to F and F to A. It will plot out each step between the two points and calculate the total time in ms.

```
Enter Device From, Device To and Latency separated by spaces (eg: A B 100): C A 100
Connection: C => A => 20
Check other devices? (Yes/Quit):
```
Typing **Quit** will quit the program and pressing **Enter** or typing **Yes** will allow the user to test other device connections.

Typing any other commands including incorrect device references (**eg: Y T 600**) will output a message and quit the program.
