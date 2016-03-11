RFLink Latest Firmware Version
  
The latest firmware version can be downloaded from:  
Sourceforge (https://sourceforge.net/projects/rflink/files/latest/download)   
  
Please note that the RFLink Gateway is a freeware project.   
Stuntteam is not making money in any way.   
This means that there are no unlimited funds to purchase test devices,   
it also means the project has to rely on you, the user, to send debug data.  
  
If you want to contribute to this project, you can send a donation which is more than welcome (see www.nemcon.nl/blog2 donation button),   
or help with sending debug data of unsupported devices (you can even write and contribute plugins and/or code fixes),  
or donate the device that you would like to see supported.  
   
Right now we are looking for some older remotes and/or switches.  
Like for example: Blyss, Home Confort, Conrad, Kambrook etc.   
For the implementation of the planned 2.4Ghz support we could use some simple MySensor devices.   
For the implementation of the planned 868Mhz support we could use some devices as well.   
If you have anything that you do not use, send a mail to frankzirrone@gmail.com   
Thanks in advance!  
  

------------------------    
Debug data:   
If you have mailed debug data and your device is not implemented yet,    
then you can be sure that we are still working on it!   
We receive lots of data to analyse and on top of that also receive lots of   
support requests that all take time to answer.   

------------------------   
Changelog:   

R39: 
- New Device: Fine Offset / Viking / WMS Mk3     

- Fixed: Modified X10 security sensor pulse ranges   

- Changed: Removed some "extra debug" information at the end of valid packets because Jeedom was skipping data   
   
R38: 
  Note: Elmes changed into Keyloq you might have to re-learn the device..   
- New Device: Initial NodoNRF sensor receive support (2.4ghz) (Thanks: Martinus)   
- New Device: Nodo 3.8 slave support   
- New Device: Catching of anti-tampering for Atlantic sensors   
- New Device: Oregon, extra checks for out of range values   
- New Device: Tunex MF-0211 temperature sensor   
- New Device: Alecto V3 Rain - Alternative version of WS1200   
- New Device: Byron SX21   
- New Device: MC145026 based sensors   
- New Device: FunkBus remote control support (Insta/Berker/Gira/Jung) (receive only) (Thanks: D.)     
- New Device: Kingpin motor controls   
- New Device: Livolo     
- New Device: Koch remote controls    
- New Device: Additional X10 protocol devices    
- New Device: Forest "Touch Sense Motors" Curtain control (Thanks: JPe)   
- New Device: Faher motor controller DC305, DC307   
- New Device: Biltema 84056 temperature and humidity sensor   
- New Device: Logipark   
- New Device: Friedland EVO doorbell Decor ED1/ED3 (receive only)      
- New Device: Friedland DC4 PIR   
- New Device: Lobeco door sensors   
- New Device: Hadex T093, WH5029   
- New Device: FineOffset Temperature & Humidity sensors   
- New Device: XT200 temperature sensor   
- New Device: Conrad RSL blind controller 640579    
- New Device: Smartwares SH5-TDR-A / SHS 5300 Heating Controller / Radiator Valve (Initial support)    
- New Device: BFT port opener remotes (Thanks: CS.)      
- New Device: Etekcity ZAP   
- Added: Oregon, extra checks for out of range values   
- Fixed: Oregon Rain sensors (PCR800 and others) (Thanks: D.)   
- Fixed: Oregon humidity    
- Fixed: Esic WT450H detection   
- Fixed: Alecto V1 wind speed values   
- Fixed: Alecto V3 Rain values and added battery level support (Thanks: D.)   
- Fixed: EverFlourish EMW203T detection   
- Fixed: Xiron/Aok humidity   
- Fixed: Added more range checks on Aster protocol   
- Fixed: Optimized Conrad RSl transmit function   
- Fixed: Additional checks on Keeloq protocol   
- Fixed: Additional checks on Chuango protocol   
- Fixed: Improved EV1527 / Chuango recognition (some issues left)   
- Fixed: Added support for the ProMax master buttons   
- Fixed: Improved ProMax signal detection   
- Fixed: Some changes to X10 command parsing (Thanks: JPe)  
- Fixed: RTS increased broadcast time   
- Fixed: Ikea Koppla transmit bug   
- Fixed: Ikea Koppla dimming issue   
- Fixed: Oregon wind speed   
- Changed: Created a seperate Novatys plugin   
- Changed: Smoke detectors now respond as a switch as well   
- Changed: Corrected Elmes label into Keyloq label   
- Changed: Splitted the WS1100/WS1200 plugin to improve compatibility   
- Changed: Improved WS1200 signal handling   
- Changed: Improved incoming command parsing   
- Changed: Big speed-up for plugin 1 converted packets   
- Changed: Regrouped plugins so new ones can be added more easily    
    
R37:   
- New Device: RTS transmit   
- New Device: ProMax 75.0006.14 (Sold at Action)    
- New Device: Novatys wall switches   
- New Device: Perel 4 channel switch set   
- New Device: Atlantic MD-210R    
- New Device: Solight switch set    
- Fixed: KD101 detection   
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
- New Device: RF Environment Optimization System (Thanks: Snips)   
- New Device: Maclean Energy MCE04 & 07 remote control and switches   
- New Device: Brel DC90   
- New Device: Medion MD 16179 doorbell (Receive only)   
- New Device: Remote 546   
- New Device: X10 security sensor protocol (a.o. Marmitek)   
- New Device: Everflourish EMW100 / Cotech EMW100R (Thanks: J)   
- New Device: Home Easy support for direct dim values    
- New Device: SelectPlus Black Bell Button (non-A model)   
- New Device: Silvercrest ian7443 type 10164 R RC202   
- New Device: Atlantic's security sensors (receive only)   
- New Device: (initial support for) FAAC TM signals (receive only)   
- New Device: Oregon Scientific RTGN318   
- New Device: WT0122 pool thermometer   
- New Device: GlobalTronics GT-WT-01, GT-WT-02  
- New Device: Rohrmotor24    
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
- New Device: Full A OK Controller/Motor support   
- New Device: Brel motor support   
- New Device: Krippl temperature/humidity sensor support   
- New Device: Elro DB270 doorbell  
- New Device: InoValley SM302 Temperature&Humidity sensor (Thanks: Pirion)   
- New Device: EZ6 Temperature&Humidity sensor   
- New Device: Alecto SA30 & SA33 Smoke Alarm  
- New Device: Xiron RS8751E3 Weather sensor   
- New Device: LightwaveRF (initial support, testers needed)   
- New Device: support for Oregon sensor simulator   
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
- Tested: tested and working: Avidsen door contact 102359   
      
R34:   
- New Device: Heidemann HX Silverline 70290   
- New Device: Eurochron EAS 301Z / EAS 302   
- New Device: Znane-01 switch set sold at Biedronka (Impuls clone)   
- New Device: HomeEasy HE800 protocol support   
- New Device: Fine Offset Electronics WH2, Agimex Rosenborg 66796, ClimeMET CM9088   
- New Device: Somfy Smoove Origin RTS (433mhz) (receive)   
- New Device: Eurodomest 972086 (Sold at Action in Belgium)   
- New Device: Eurodomest revised protocol (full autodetection)  
- New Device: Prologue temperature sensor support   
- New Device: tested and working: Home Confort, Smart Home PRF-100 switch set     
- Fixed: Auto detection of "Emil Lux"/LUX-Tools remote control/switch set (Sold at Obi.de Art.Nr. 2087971) (Impuls clone)    
- Fixed: Alecto WS1100 working properly again (Adjusted pulse range and displayed humidity value)   
- Fixed: Byron SX receive and send commands   
- Fixed: Ikea Koppla Send routines   
- Fixed: Improved the Impuls remote detection   
- Fixed: Impuls transmit   
- Changed: added checks for valid temperatures in various plugins   
   
R33:   
- Updated RFlink loader to version 1.03 to include a serial log option with command sending ability!    
- New Device: Full automatic 'Flamingo FA500/SilverCrest 91210/60494 RCS AAA3680/Mumbi M-FS300/Toom 1919384' protocol support! (send & receive!)  
              Note: re-learn your FA500 Remote in Domoticz   
- New Device: Unitec 48111/48112 (receive)   
- New Device: Avidsen sensors (pir/door contacts)   
- New Device: Somfy Telis (433mhz) (receive)   
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
- New Device: Europe RS-200, Conrad TR-200  
- New Device: Bofu motor transmit   
- New Device: ARC group command support  
- New Device: Support for ARC based tri-state protocol  
- New Device: Hormann (868mhz) receive
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
   
Special thanks to everyone who contributed with feedback, suggestions, debug data, tests and even complete protocol plugins!   

