RFLink Latest Firmware Version
  
The latest firmware version can be downloaded from:  
https://sourceforge.net/projects/rflink/files/latest/download
  
Please note that the RFLink Gateway is a freeware project.   
Stuntteam is not making money in any way.   
This means that there are no unlimited funds to purchase test devices,   
it also means the project has to rely on you, the user, to send debug data.  
  
If you want to contribute to this project, you can send a donation which is more than welcome (see www.nemcon.nl/blog2 donation button),   
or help with sending debug data of unsupported devices (you can even write and contribute plugins and/or code fixes),  
or donate the device that you would like to see supported.  
   
Right now we are looking for some older remotes and/or switches.  
Like for example: Powerfix, Blyss, Home Confort, Conrad, Kambrook, Everflourish etc.   
For the implementation of the planned 2.4Ghz support we could use some simple MySensor devices.   
For the implementation of the planned 868Mhz support we could use some devices as well.   
If you have anything that you do not use, send a mail to <b>frankzirrone@gmail.com</b>   
Thanks in advance!  
  
------------------------    
Synology NAS:  
If you want to use RFLink with a Synology NAS you can use:  
- an Arduino Mega clone based on CH340 USB/serial chip  
In all other cases:  
- connect a 10 uF capacitor between reset and ground on the Arduino.   
  Simply stick the pins of the capacitor in the power connector socket.  
  When you want to update the firmware of the Arduino, remove the capacitor and reconnect it when done.   
  For details about the Domoticz Synology package check out: http://www.jadahl.com   

------------------------   
RFlink via Network Connection:   
It is possible to use RFlink via a network connection using ser2net.   

------------------------   
RFlink Frequencies:   
RFLink can be used with various frequencies like 315, 433 or 868 mhz. 
Right now we are expanding the firmware to also work on 2.4Ghz. 

------------------------   
Note for blinds controllers:   
When using RFlink in combination with Domoticz and a Bofu, Brell or A OK blind controller,   
Domoticz will detect the remote control commands and create switch devices.   
Add the detected remote command as switch and then change the generated switch to a ventian blind.   
This will give you an icon with up/stop/down options.   
Once you have done this, you can control the blind from Domoticz.   

------------------------   
Supported Automation software:   
You can now use the RFLink Gateway with the following home automation software:   
Domoticz   
Jeedom   

------------------------    
###Debug data:   
If you have mailed debug data and your device is not implemented yet,    
then you can be sure that we are still working on it!   
We receive lots of data to analyse and on top of that also receive lots of   
support requests that all take time to answer.   

------------------------   
Changelog:   
R37:   
- Added: RTS transmit   
- Added: ProMax 75.0006.14 (Sold at Action)    
- Added: Novatys wall switches   
- Added: Perel 4 channel switch set   
- Added: Atlantic MD-210R   
- Fixed: KD101 detected   
- Fixed: GlobalTronics GT-WT-01, GT-WT-02   
- Fixed: Alecto V4   
- Fixed: Silvercrest switches   
- Fixed: UPM/Esic negative temperature values   
- Fixed: Oregon data processing speed improvement   
- Fixed: Oregon V2 data processing and crc checks     
- Fixed: Oregon RTGN318    
- Fixed: Oregon wind speed    
- Fixed: Humidity for BTHR918, BTHGN129, etc.   
- Fixed: filtering out invalid packets for BTHR918, BTHGN129, etc.   
- Fixed: Chacon remote control   

R36:   
Note: Due to some internal code changes:   
Brel motor control might have a new ID    
Some Alarm devices have moved to a new product group and also got a new ID   
   
- Added: RF Environment Optimization System (Thanks: Snips)   
- Added: Maclean Energy MCE04 & 07 remote control and switches   
- Added: Brel DC90   
- Added: Medion MD 16179 doorbell (Receive only)   
- Added: Remote 546   
- Added: X10 security sensor protocol (a.o. Marmitek)   
- Added: Everflourish EMW100 / Cotech EMW100R (Thanks: J)   
- Added: Home Easy support for direct dim values    
- Added: SelectPlus Black Bell Button (non-A model)   
- Added: Silvercrest ian7443 type 10164 R RC202   
- Added: Atlantic's security sensors (receive only)   
- Added: (initial support for) FAAC TM signals (receive only)   
- Added: Oregon Scientific RTGN318   
- Added: WT0122 pool thermometer   
- Added: GlobalTronics GT-WT-01, GT-WT-02  
- Added: Rohrmotor24    
- Changed: Removed temperature limits on LaCrosse/TFA    
- Changed: Alecto V1 ID split (rolling code & channel number) for wind sensors   
- Fixed: Ikea Koppla signal detection    
- Fixed: Ikea Koppla dimming    
- Fixed: Cresta sensor reporting period when there is low traffic   
- Fixed: EMW200 transmit function   
- Fixed: Lightwave RF transmit function   
- Fixed: UPM/Esic minus temperature handling   
- Fixed: OWL CM180 (Big thanks to Snips)   
- Fixed: Various Oregon sensor optimizations (Big thanks to Snips)   
- Fixed: WS3600 rounding of rain values for precision   
- Fixed: WS3600 wind speed only passes one value at a time, Domoticz needs both. RFlink now passes the last know value for the non-received data   
- Fixed: Improved Alecto V4 signal detection   
- Fixed: Brel motor send command handling    
- Fixed: BofuMotor send command handling   
- Fixed: Elro Doorbell detection (among others: DB200Z, DB270)   

R35: 
- Added: Full A OK Controller/Motor support   
- Added: Brel motor support   
- Added: Krippl temperature/humidity sensor support   
- Added: Elro DB270 doorbell  
- Added: InoValley SM302 Temperature&Humidity sensor (Thanks: Pirion)   
- Added: EZ6 Temperature&Humidity sensor   
- Added: Alecto SA30 & SA33 Smoke Alarm  
- Added: Xiron RS8751E3 Weather sensor   
- Added: LightwaveRF (initial support, testers needed)   
- Added: support for Oregon sensor simulator   
- Fixed: Oregon OWL CM119, CM160, CM180  
- Fixed: Oregon UV800 uv index value   
- Fixed: Corrected WH440 temperature values    
- Fixed: Improved Philips SBC   
- Fixed: Improved Chacon EMW200   
- Fixed: Improved FA500 false positive detection   
- Fixed: Removed a slash in the UPM/Esic name   
- Fixed: Mertik transmit improved   
- Fixed: Optimized the Auriol/Xiron plugin   
- Fixed: LaCrosse negative temperatures and extended allowed pulse range (Thanks: VeryBitter)   
- Fixed: Alecto v1 Windspeed value   
- Fixed: Improved Otio remote signal handling   
- Fixed: corrected WH2 handling for Tesa WH1150 sensor (Thanks: Pirion)   
- Fixed: Lacrosse rain - wind needs some more work (Thanks: Arie)   
- Tested: tested and working: Lidl / Libra TR502MSV switches   
- Tested: tested and working: Avidsen door contact 102539   
      
R34:   
- Added: Heidemann HX Silverline 70290   
- Added: Eurochron EAS 301Z / EAS 302   
- Added: Znane-01 switch set sold at Biedronka (Impuls clone)   
- Added: HomeEasy HE800 protocol support   
- Added: Fine Offset Electronics WH2, Agimex Rosenborg 66796, ClimeMET CM9088   
- Added: Somfy Smoove Origin RTS (433mhz) (receive)   
- Tested: Eurodomest 972086 (Sold at Action in Belgium)   
- Added: Eurodomest revised protocol (full autodetection)  
- Added: Prologue temperature sensor support   
- Tested: tested and working: Home Confort, Smart Home PRF-100 switch set     
- Fixed: Auto detection of "Emil Lux"/LUX-Tools remote control/switch set (Sold at Obi.de Art.Nr. 2087971) (Impuls clone)    
- Fixed: Alecto WS1100 working properly again (Adjusted pulse range and displayed humidity value)   
- Fixed: Byron SX receive and send commands   
- Fixed: Ikea Koppla Send routines   
- Fixed: Improved the Impuls remote detection   
- Fixed: Impuls transmit   
- Changed: added checks for valid temperatures in various plugins   
   
R33:   
- Updated RFlink loader to version 1.03 to include a serial log option with command sending ability!    
   
- Added: Full automatic 'Flamingo FA500/SilverCrest 91210/60494 RCS AAA3680/Mumbi M-FS300/Toom 1919384' protocol support! (send & receive!)  
         Note: re-learn your FA500 Remote in Domoticz   
- Added: Unitec 48111/48112 (receive)   
- Added: Avidsen   
- Added: Somfy Telis (433mhz) (receive)   
- Fixed: Extreme temperature value rejection for various sensor types (TFA/LaCrosse)   
- Fixed: Improved Blyss send routines   
- Added: Support for old Xiron temperature sensor in Cresta plugin (Temperature only sensor was not handled)   
- Added: Biowin meteo sensor   
- Fixed: Imagintronix humidity and temperature values were sometimes incorrect   
- Fixed: AB4400/Sartano/Phenix detection corrected   
- Fixed: Modification to allow EMW200/203 to work better   
- Changed: ARC (and compatible) remote and switch handling improved   
- Fixed: Improved Impuls handling   
- Fixed: Auriol V3 minus temperature handling    
- Fixed: TRC02RGB send   
- Fixed: Oregon OWL180 data   
- Changed: Aster signal detection so that L^Home model 32311T is recognized as well   
- Changed: ID for Nodo Slave 'Wind direction/Wind gust' combined so that Domoticz can handle the data   
- Changed: Protocol handling order for 'multi-protocol' transmitting devices   
   
R32: 
- Added: Europe RS-200, Conrad TR-200  
- Added: Bofu motor transmit   
- Added: ARC group command support  
- Added: support for ARC based tri-state protocol  
- Tested and working: Hormann (868mhz) receive
- Changed: Bofu motor signal repetition detection improved  
- Fixed: Aster transmit routines  
- Fixed: plugin 003 output was not processed correctly by Domoticz  
- Changed: Chacon/Powerfix/Mandolyn/Quigg transmit routine and optimized timing  
- Changed: Increased the number of re-transmits in the Home Easy protocol to improve signal reception and distance  
- Changed: Sensor plugins now suppressing ARC derived protocols a bit better  
   
R31:  
- New Device: Forrinx Wireless Doorbell  
- New Device: TRC02 RGB controller  
- New Device: OWL CM180  
- New Device: ELMES CTX3H and CTX4H contact sensor  
- New Device: Bofu Motor (receive)  
- New Device: Aster / GEMINI   EMC/99/STI/037   
- Changed: EV1527 based sensors were reported as X10, now they are reported as EV1527. Note that it might be needed to re-add the devices to Domoticz  
- Changed: increased number of retransmits for ARC and AC protocols  
- Fixed: Koppla switch number was incorrect  
- Fixed: Powerfix/Mandolyn/Chacon Parity calculation in send routines  
- Fixed: Powerfix/Mandolyn/Chacon timing  
- Fixed Windspeed value for WS2300  
- Fixed: Home Easy HE300 ON/OFF signal was reversed  
- Changed: HomeEasy suppressing additional protocol data to avoid reporting the same event multiple times under different protocols  
- Fixed: More fixes to avoid duplicate reporting of the same event (various protocols)  
  
R30:  
- New Device: Conrad 9771 Pool Thermometer  
- New Device: SilverCrest Z31370-TX Doorbell  
- New Device: Smartwares remote controls (among others: SH5-TDR-K 10.037.17)   
- New Device: Chuango Alarm devices Motion/Door/Window etc. (among others: CG-105S)  
- New Device: Oregon Scientific NR868 PIR/night light  
- New Device: Oregon Scientific MSR939 PIR  
- New Device: Imagintronix Temperature/Soil humidity sensor  
- New Device: Ikea Koppla  
- New Device: Chacon (TR-502MSV, NR.RC402)
- Fixed: Arc protocol send  
- Fixed: Impuls. Note: pair devices with the KAKU protocol, the remote is recognized separately  
- Changed: Plugin 3 send method, combined routines  
- Changed: HomeConfort was recognized as Impuls, now using GDR2 name  
- Changed: HomeEasy remotes can deliver various signals, now skipping KAKU compatible signals and just reporting the HomeEasy code when both codes are transmitted  
- Fixed: HomeEasy group on/off command was reversed for HE8xx devices, now correctly detects differences between HE3xx and HE8xx  
- Fixed: HomeEasy was not able to control HE87x switches, changed the entire transmit routine  
- Changed: stretched Xiron timing checks  
- Changed: Various timing modifications (NewKaku/AC, Blyss) due to the new timing introduced at version R26  
- Changed: Plugin 61, Chinese Alarm devices, reversed bits as it seemed to correspond better to bit settings, increased address range  
- Fixed: Flamingo Smokedetector packet detection tightened up to prevent false positives  
- Fixed: Corrected Conrad RSL command interpretation  
- Added: Extended Nodo Slave support to support separate and combined sensors  
- Added: Extended Nodo Slave support to support pulse meters  
   
R29:  
- Fixed: AC/NewKaku high unit numbers were incorrect.   
         If you already have devices with high unit numbers in Domoticz, just throw them away and let them be recognized again  

R28:  
- Fixed: FA20RF smoke detector transmit from Domoticz  

R27:  
- Added: OSV1 battery status   
- Fixed: OSV1 boundaries and removed some debug info   
- Fixed: Some plugins set an incorrect sampling rate divider value   
- Changed: AlectoV1 false positives filter was too agressive  
  
R26:
- Added: QRFDEBUG command to do faster logging of undecoded data  
- Added: VERSION command  
- Added: Powerfix/Quigg switches  
- Added: proper Lacrosse V3 WS7000 sensor support  
- Changed: config file and plugin integration  
- Changed: timeout and divider value  
- Changed: Lacrosse V2 WS2300/WS3600 plugin number to get faster processing, changed various other parts as well  
- Changed: Lacrosse V1 pulse duration checks  
- Changed: various parts to improve speed  
- Changed: Flamingo Smoke detector signal re-transmits from 8 to 10 times  
- Added: Additional tests on Alecto V1 and Alecto V4 to filter out false positives  
- Fixed: AC (NewKaku) protocol send for some device numbers  
- Fixed: little bug in UPM code  
- Fixed: Oregon wind speed reporting  
- Fixed: Wind speed calculations  
- Fixed: Wind direction reporting in all plugins  
- Fixed: AlectoV3 humidity value displaying out of range values  
- Fixed: OregonV1 decoding  
    
R25:  
- Fixed: Eurodomest address range check  
- Fixed: Alecto V1 and V3 humidity handling  
- Fixed: Lacrosse WS2300/WS3600 and labelled as LacrosseV2  
  
R24:  
- Fixed: Flamingo Smoke Detector timings and device address usage  
- Fixed: Timing for Nexa/Jula Anslut  

R23:  
- Changed: Alecto V1 temperature data filtering  
- Added: Alecto V1 battery status now shown for temperature sensors  

R22:  
- Various additional tests and fixes after intensive tests  
- Added: Home Confort send and recognition by Domoticz  
  
R21:   
- Re-Activated PIR & Door/Window sensors (plugin 60/61)  
  
R20:  
- Switched to Arduino 1.6.5  
  
R19:  
- Complete rewrite  
- Added: Home Confort Smart Home - TEL-010  
- Added: RGB LED Controller  
- Added: RL-02 Digital Doorbell  
- Added: Deltronic Doorbell  
- Added: Sartano 2606 remote & switch  
  
r18:
- Added Banggood SKU174397, Sako CH113, Homemart/Onemall FD030 and Blokker (Dake) 1730796 outdoor temperature sensor  
- Tested Okay: Promax RSL366T, Profile PR-44N & PR-47N  
- Fixed: LaCrosse humidity values are correctly displayed  
- Fixed: Humidity values that originate from slave Nodos are correctly displayed  
- Fixed: UPM/Esic insane temperature values are skipped  
- Removed Xiron & Auriol debug data  
- Tightened pulse range on various protocols to prevent false positives  

r17:  
- Modified Oregon THGR228N code  
- Modified Newkaku(AC) dim values  
- Corrected support for KAKU door switches  
- Fixed Nodo Slave sensors  
- Improved speed and priorities so that group commands are properly transmitting  

r16:  
- Fixed Aleco V1 temperature ID to match wind sensors  
- Fixed HomeEasy transmit  
- Added AC(NewKaku) dimmer support  

r15:  
- Improved large packet translation  

r14:  
- Changed Motion sensors (60/61)  

r13:  
- Flamingo Smoke detector fix  
- Added Xiron sensor support  

r11/12:  
- Mertik / Dru Send added  

r10:  
- Added Auriol Z32171A  

r9:  
- Fixed Kaku send with high device id's (P1 M1 etc)  

r8:  
- Improved descriptions  

r7:  
- Fixed Oregon RTGR328N ID and humidity format  
- Fixed UPM/Esic humidity format  
- Fixed Alecto humidity format  

r6:  
- Fixed Auriol V2 plugin  
- Updated Auriol plugin  
- Fixed Lacrosse Humidity  

r1/2/3/4/5:  
- Added X10 receive/transmit plugin  
- Minor changes & improvements  
   
Special thanks to everyone who contributed with feedback, suggestions, debug data, tests etc.   
