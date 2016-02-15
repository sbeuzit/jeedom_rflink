RFLink Loader 					Version 1.04


The RFLink Loader program runs on Windows and can program an Arduino Mega 2560 board
with the RFLink software. 
You do not need any Arduino IDE/Compiler etc.


Steps:
------
- Launch the program
- Select the file you want to program (rflink.cpp.hex)
- Select the serial port to which the Arduino is connected.
- Hit the "program" button and wait for the process to finish.



History:
--------
1.05 Fixed: Log length
1.04 Fixed: Serial port log option making a mess when text was selected
     Fixed: handling in case of serial port errors
1.03 Added: Serial port log option
1.02 Fixed: could not use serial ports > 9
1.01 Fixed: could not load the hex file if the path name contained a space 
     Added: test serial port availability before trying to program the Arduino
1.00 Initial Release
