<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class rflink extends eqLogic {

    public static function cronDaily() {
        rflink::check('daily');
    }

    public static function check($type = 'default') {
        $xml = new DOMDocument();
        $gateway = config::byKey('gateLib','rflink');
        if ($type = 'install') {
            $release = '38';
            $version = '1';
        } else {
            $release = substr($gateway, -2);
            $version = substr($gateway, -9, 3);
        }
        $url = 'http://www.nemcon.nl/blog2/fw/update.jsp?ver=' . $version . '&rel=' . $release;
        $xml->load($url);
        log::add('rflink','debug','Recherche firmware ' .  $url );

        if ($xml->getElementsByTagName('Value')->item(0)->nodeValue == 1) {
            //update dispo
            $file = file_get_contents($xml->getElementsByTagName('Url')->item(0)->nodeValue);
            $resource_path = realpath(dirname(__FILE__) . '/../../resources/rflink/RFLink.cpp.hex');
            $release = str_replace("http://www.nemcon.nl/blog2/fw/","",str_replace("/RFLink.cpp.hex","",$xml->getElementsByTagName('Url')->item(0)->nodeValue));
            log::add('rflink','debug','Download ' . $xml->getElementsByTagName('Url')->item(0)->nodeValue . ' in ' . $resource_path . ' for release ' . $release);
            exec('sudo rm ' . $resource_path);
            file_put_contents($resource_path,$file);
            config::save('avaLib', $release,  'rflink');
            return true;
        }
    }

    public static function flashRF( ) {
        log::add('rflink','info','Flash du RFLink');
        if (config::byKey('nodeGateway', 'rflink') == 'none' || config::byKey('nodeGateway', 'rflink') == '') {
            return true;
        }
        if (config::byKey('nodeGateway', 'rflink') == 'acm') {
            $usbGateway = "/dev/ttyACM0";
        } else {
            $usbGateway = jeedom::getUsbMapping(config::byKey('nodeGateway', 'rflink'));
        }
        $resource_path = realpath(dirname(__FILE__) . '/../../resources');
        config::save('flashing', '1',  'rflink');
        rflink::deamon_stop();
        exec('/usr/bin/avrdude -v -v -v -p atmega2560 -c wiring -D -P ' . $usbGateway . ' -b 115200 -U flash:w:' . $resource_path . '/rflink/RFLink.cpp.hex:i > ' . log::getPathToLog('rflink_flash') . ' 2>&1');
        config::save('flashing', '0',  'rflink');
        return true;
    }

    public static function echoController( $command ) {
        if (config::byKey('nodeGateway', 'rflink') == 'none' || config::byKey('nodeGateway', 'rflink') == '') {
            return false;
        }

        log::add('rflink', 'info', $command);
        $fp = fsockopen('127.0.0.1', '8020', $errno, $errstr);
        if (!$fp) {
            echo "Service ne répond pas";
            return false;
        } else {
            fwrite($fp, $command);
            fclose($fp);
        }
    }

    public static function sendToController( $protocol, $id, $request ) {
        $nodeid = $protocol . '_' . $id;
        $id = str_pad($id, 6, "0", STR_PAD_LEFT);
        $msg = "10;" . $protocol . ";" . $id . ";" . $request . ";";
        log::add('rflink', 'info', $msg);
        rflink::echoController($msg);

        //sauvegarde de la valeur envoyée
        $explode = explode(";", $request);
        $cmd = $explode[0];
        $value = $explode[1];
        if ($value == 'OFF') {
            $value = '0';
        } else if ($value == 'ON') {
            $value = '1';
        }

        $rflink = self::byLogicalId($nodeid, 'rflink');
        $rflink->checkAndUpdateCmd($cmd, $value);
    }

    public function checkCmdOk($_id, $_name, $_subtype, $_value) {
        $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),$_id);
        if (!is_object($rflinkCmd)) {
            log::add('rflink', 'debug', 'Création de la commande ' . $_id);
            $rflinkCmd = new rflinkCmd();
            $cmds = $this->getCmd();
            $order = count($cmds);
            $rflinkCmd->setOrder($order);
            $rflinkCmd->setName(__($_name, __FILE__));
            $rflinkCmd->setEqLogic_id($this->id);
            $rflinkCmd->setEqType('rflink');
            $rflinkCmd->setLogicalId($_id);
            $rflinkCmd->setType('info');
            $rflinkCmd->setSubType($_subtype);
            $rflinkCmd->setTemplate("mobile",'line' );
            $rflinkCmd->setTemplate("dashboard",'line' );
            $rflinkCmd->setDisplay("forceReturnLineAfter","1");
            $rflinkCmd->setConfiguration('value',$_value);
            $rflinkCmd->save();
        }
    }

    public function checkActOk($_id, $_name, $_subtype, $_cmdid, $_request, $_maxslider) {
        $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),$_id);
        if (!is_object($rflinkCmd)) {
            log::add('rflink', 'debug', 'Création de la commande ' . $_id);
            $rflinkCmd = new rflinkCmd();
            $cmds = $this->getCmd();
            $order = count($cmds);
            $rflinkCmd->setOrder($order);
            $rflinkCmd->setName(__($_name, __FILE__));
            $rflinkCmd->setEqLogic_id($this->id);
            $rflinkCmd->setEqType('rflink');
            $rflinkCmd->setLogicalId($_id);
            $rflinkCmd->setType('action');
            $rflinkCmd->setSubType($_subtype);
            if ($_maxslider != '0') {
                $rflinkCmd->setConfiguration('minValue', 0);
                $rflinkCmd->setConfiguration('maxValue', $_maxslider);
            }
            $rflinkCmd->setConfiguration('id',$_cmdid);
            $rflinkCmd->setConfiguration('request',$_request);
            $rflinkCmd->save();
        }
    }

    public function checkInstall() {
        $protocol = 'rflink';
        $id = 'gateway';
        $nodeid = $protocol . '_' . $id;
        $rflink = self::byLogicalId($nodeid, 'rflink');
        if (!is_object($rflink)) {
            $rflink = new rflink();
            $rflink->setEqType_name('rflink');
            $rflink->setLogicalId($nodeid);
            $rflink->setConfiguration('id', $id);
            $rflink->setConfiguration('protocol',$protocol);
            $rflink->setName($nodeid);
            $rflink->save();
        }
        $rflink->checkActOk('debugon', 'Activer Debug', 'other', 'rflink', '10;RFUDEBUG=ON;', '0');
        $rflink->checkActOk('debugoff', 'Désactiver Debug', 'other', 'rflink', '10;RFUDEBUG=OFF;', '0');
        $rflink->checkActOk('reboot', 'Reboot', 'other', 'rflink', '10;REBOOT;', '0');
        rflink::check('install');
    }

    public function checkHexaCmd($_cmd, $_value) {
        $hexacmd = 'TEMP,BARO,UV,LUX,,RAIN,RAINRATE,WINSP,AWINSP,WINGS,WINCHL,WINTMP,KWATT,WATT';
        if (strpos($hexacmd,$_cmd) !== false) {
            $result = hexdec($_value);
        } else {
            $result = $_value;
        }
        return $result;
        log::add('rflink', 'debug', 'HexaCmd ' . $_value . ' value ' . $result);
    }

    public function checkNumCmd($_cmd) {
        $numcmd = 'TEMP,HUM,BARO,HSTATUS,BFORECAST,UV,RAIN,RAINRATE,WINSP,AWINSP,WINGS,WINDIR,WINCHL,WINTMP,CHIME,CO2,SOUND,KWATT,WATT,DIST,METER,VOLT;CURRENT';
        if (strpos($numcmd,$_cmd) !== false) {
            $result = 'numeric';
        } else {
            $result = 'string';
        }
        return $result;
    }

    public function checkDivCmd($_cmd, $_value) {
        $divcmd = 'TEMP,RAIN,RAINRATE,RAINTOT,WINSP,WINCHL,WINTMP,AWINSP';
        if (strpos($divcmd,$_cmd) !== false) {
            $result = $_value/10;
        } else {
            $result = $_value;
        }
        return $result;
        log::add('rflink', 'debug', 'DivCmd ' . $_value . ' value ' . $result);
    }

    public function registerRTS($_cmd, $_value) {
        //checkCmdOk($_id, $_name, $_subtype, $_value)
        //checkActOk($_id, $_name, $_subtype, $_cmdid, $_request, $_maxslider)
        $this->checkCmdOk($_cmd, 'Statut ' . $_cmd, 'binary', $_value);
        $this->checkAndUpdateCmd($_cmd, $_value);
        $this->checkActOk('PAIR' . $_cmd, 'Appairement ' . $_cmd, $_cmd, 'PAIR', '0');
        $this->checkActOk('UP' . $_cmd, 'Montée ' . $_cmd, $_cmd, 'UP', 'UP', '0');
        $this->checkActOk('DOWN' . $_cmd, 'Descente ' . $_cmd, $_cmd, 'DOWN', 'DOWN', '0');
        $this->checkActOk('STOP' . $_cmd, 'Arret ' . $_cmd, $_cmd, 'STOP', 'STOP', '0');
    }

    public function registerMilightv1($_cmd, $_value, $_rgbw) {
        //checkCmdOk($_id, $_name, $_subtype, $_value)
        //checkActOk($_id, $_name, $_subtype, $_cmdid, $_request, $_maxslider)
        $this->checkCmdOk($_cmd, 'Etat Lampe ' . $_cmd, 'string', $_value);
        $this->checkAndUpdateCmd($_cmd, $_value);
        $this->checkActOk('ON' . $_cmd, 'On ' . $_cmd, 'other', $_cmd, 'ON', '0');
        $this->checkActOk('ALLON' . $_cmd, 'All On ' . $_cmd, 'other', $_cmd, 'ALLON', '0');
        $this->checkActOk('OFF' . $_cmd, 'Off ' . $_cmd, 'other', $_cmd, 'OFF', '0');
        $this->checkActOk('ALLOFF' . $_cmd, 'All Off ' . $_cmd, 'other', $_cmd, 'ALLOFF', '0');

        $this->checkCmdOk('RGBW' . $_cmd, 'Couleur Lampe ' . $_cmd, 'string', $_rgbw);
        $this->checkAndUpdateCmd('RGBW' . $_cmd, $_rgbw);
        $this->checkCmdOk('color_val' . $_cmd, 'Couleur Valeur ' . $_cmd, 'string', substr($_rgbw, 0, 2));
        $this->checkAndUpdateCmd('color_val' . $_cmd, substr($_rgbw, 0, 2));
        $this->checkActOk('COLOR' . $_cmd, 'Couleur ' . $_cmd, 'slider', $_cmd, 'COLOR', '255');
        $this->checkCmdOk('bright_val' . $_cmd, 'Luminosité Valeur ' . $_cmd, 'string', substr($_rgbw, -2));
        $this->checkAndUpdateCmd('bright_val' . $_cmd, substr($_rgbw, -2));
        $this->checkActOk('BRIGHT' . $_cmd, 'Luminosité ' . $_cmd, 'slider', $_cmd, 'BRIGHT', '32');
    }

    public function setColorMilight($_id, $_logid, $_value) {
        //change value color or brightness with _logid and _id, then change rgbw value
        //then take the request value and replace #color# by rgbw value
        //return rgbw value
        if (strpos($_logid, 'COLOR') !== false) {
            $color = substr(dechex($_value),-2);
            $this->checkAndUpdateCmd('color_val' . $_id, $color);
            $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),'bright_val'.$_id);
            $bright = $rflinkCmd->getConfiguration('value');
        } else if (strpos($_logid, 'BRIGHT') !== false) {
            $bright = substr(dechex($_value*8),-2);
            $this->checkAndUpdateCmd('bright_val' . $_id, $bright);
            $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),'color_val'.$_id);
            $color = $rflinkCmd->getConfiguration('value');
        } else {
            $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),'color_val'.$_id);
            $color = $rflinkCmd->getConfiguration('value');
            $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),'bright_val'.$_id);
            $bright = $rflinkCmd->getConfiguration('value');
        }
        $this->checkAndUpdateCmd('RGBW' . $_id, $color.$bright);
        $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),$_logid);
        $request = $color . $bright . ';' . $rflinkCmd->getConfiguration('request');
        $this->checkAndUpdateCmd($_id, $rflinkCmd->getConfiguration('request'));
        log::add('rflink', 'debug', 'Request Milight : ' . $request);
        return $request;
    }

    public function registerSwitch($_cmd, $_value) {
        //checkCmdOk($_id, $_name, $_subtype, $_value)
        //checkActOk($_id, $_name, $_subtype, $_cmdid, $_request, $_maxslider)
        if ($_cmd[0] == '0' && strlen($_cmd) > 1) {
            //supp les 0 en début de switch
            $_cmd = ltrim($_cmd, "0");
            $_cmd = ($_cmd == '') ? '0' : $_cmd;
        }
        $binary = ($_value == 'OFF') ? '0' : '1';
        $this->checkCmdOk($_cmd, 'Statut ' . $_cmd, 'binary', $binary);
        $this->checkAndUpdateCmd($_cmd, $binary);
        $this->checkActOk($_value . $_cmd, $_value . ' ' . $_cmd, 'other', $_cmd, $_value, '0');
    }

    public function registerBattery($_value) {
        $battery = ($_value == 'LOW') ? 10 : 100;
        $this->batteryStatus($battery);
        $this->save();
        log::add('rflink', 'debug', 'Batterie ' . $_value . ' value ' . $battery);
    }

    public function registerInfo($_cmd, $_value) {
        // calcul valeur pour la temp et autres cas particuliers
        log::add('rflink', 'debug', 'Commande capteur ' . $_cmd . ' value ' . $_value);
        if ($_cmd != '') {
            if ($_cmd == 'TEMP' || $_cmd == 'WINCHL' || $_cmd == 'WINTMP') {
                if (substr($_value,0,1) != 0) {
                    $_value = '-' . hexdec(substr($_value, -3));
                } else {
                    $_value = hexdec(substr($_value, -3));
                }
            } else {
                $_value = $this->checkHexaCmd($_cmd,$_value);
            }
            $_value = $this->checkDivCmd($_cmd,$_value);
            $cmds = $this->getCmd();
            $this->checkCmdOk($_cmd, $_cmd . ' - ' . count($cmds), rflink::checkNumCmd($_cmd), $_value);
            $this->checkAndUpdateCmd($_cmd, $_value);
        }
    }

    public function setRflinkStatus($_data) {
        log::add('rflink', 'debug', 'Status ' . $_data);
        $datas = explode(";", $_data);
        $i = 0;
        $rflink = rflink::byLogicalId('rflink_gateway','rflink');
        if (!is_object($rflink)) {
            return false;
        }
        foreach ($datas as $info) {
            if ($i > 2) {
                if (strpos($info,'=') !== false) {
                    $arg = explode("=", $info);
                    log::add('rflink', 'debug', 'Status ' . $arg[0] . ' is ' . $arg[1]);
                    $rflink->checkCmdOk($arg[0], $arg[0], 'string', $arg[1]);
                    $rflink->checkAndUpdateCmd($arg[0], $arg[1]);
                    $rflink->checkActOk($arg[0] . 'off', $arg[0] . ' Off', 'other', '0', '10;' . $arg[0] . '=OFF;', '0');
                    $rflink->checkActOk($arg[0] . 'on', $arg[0] . ' On', 'other', '0', '10;' . $arg[0] . '=ON;', '0');
                }
            }
            $i++;
        }
    }

    public static function receiveData($body) {
        //log::add('rflink', 'debug', 'Body ' . print_r($json,true));
        //$body = json_decode($json, true);
        $data = $body['data'];
        if (strpos($data,'DEBUG') !== false) {
            log::add('rflink', 'debug', 'Trame de debug recue : ' . $data);
            return false;
        }

        $datas = explode(";", $data);

        if (strpos($data,'Nodo RadioFrequencyLink') !== false) {
            config::save('gateLib', $datas[2],  'rflink');
            return false;
        }

        if ($datas[0] == '10') {
            //envoi de données, on va pas plus loin
            return false;
        }

        $protocol = $datas[2];

        if ($protocol == 'STATUS') {
            //status line need special treatment
            rflink::setRflinkStatus($data);
            return true;
        }

        if (strpos($datas[3],'ID=') !== false) {
            $id = str_replace('ID=', '', $datas[3]);
        } else {
            log::add('rflink', 'debug', 'Trame non utilisable ' . $data);
            return false;
        }
        //reduire ID sans les 0 de début si plus de 6 caractères
        if ($id[0] == '0' && strlen($id) > 6) {
            $id = ltrim($id, "0");
        }
        $nodeid = $protocol . '_' . $id;
        log::add('rflink', 'debug', 'Protocole ' . $protocol . ' ID ' . $id);

        $rflink = self::byLogicalId($nodeid, 'rflink');
        if (!is_object($rflink) && config::byKey('include_mode', 'rflink') == '1') {
            $rflink = new rflink();
            $rflink->setEqType_name('rflink');
            $rflink->setLogicalId($nodeid);
            $rflink->setConfiguration('id', $id);
            $rflink->setConfiguration('protocol',$protocol);
            $rflink->setName($nodeid);
            $rflink->save();
            event::add('rflink::includeDevice',
            array(
                'state' => 1
            )
        );
    }

    if (!is_object($rflink)) {
        return false;
    }

    $rflink = self::byLogicalId($nodeid, 'rflink');
    $rflink->setConfiguration('lastCommunication', date('Y-m-d H:i:s'));
    $rflink->save();

    $i=0;
    $args = array();
    foreach ($datas as $info) {
        if ($i > 3) {
            if (strpos($info,'=') !== false) {
                $arg = explode("=", $info);
                $args[$arg[0]] = $arg[1];
            }
        }
        $i++;
    }
    foreach ($args as $type => $value) {
        log::add('rflink', 'debug', 'Commande ' . $type . ' value ' . $value);
        switch ($type) {
            case 'SWITCH' :
            switch ($protocol) {
                case 'RTS' :
                $rflink->registerRTS($value,$args['CMD']);
                break;
                case 'MiLightv1' :
                $rflink->registerMilightv1($value,$args['CMD'],$args['RGBW']);
                break;
                default :
                $rflink->registerSwitch($value,$args['CMD']);
                //SWITCH=00;CMD=OFF
                break;
            }
            break;
            case 'CMD' :
            //nothing, it's part of Switch
            break;
            case 'RGBW' :
            //nothing, it's part of Switch
            break;
            case 'BAT' :
            $rflink->registerBattery($value);
            $rflink->registerInfo($type,$value);
            break;
            default :
            $rflink->registerInfo($type,$value);
            break;
        }
    }
}

public static function saveInclude($mode) {
    config::save('include_mode', $mode,  'rflink');
    $state = 1;
    if ($mode == 1) {
        $state = 0;
    }
    event::add('rflink::controller.data.controllerState',
    array(
        'state' => $state
    )
);
}

public function preSave() {
    $this->setLogicalId($this->getConfiguration('protocol') . '_' . $this->getConfiguration('id'));
  }

  public static function deamon_info() {
      $return = array();
      $return['log'] = 'rflink';
      $return['state'] = 'nok';
      $pid_file = jeedom::getTmpFolder('rflink') . '/deamon.pid';
      if (file_exists($pid_file)) {
          $pid = trim(file_get_contents($pid_file));
          if (is_numeric($pid) && posix_getsid($pid)) {
              $return['state'] = 'ok';
          } else {
              shell_exec(system::getCmdSudo() . 'rm -rf ' . $pid_file . ' 2>&1 > /dev/null;rm -rf ' . $pid_file . ' 2>&1 > /dev/null;');
          }
      }
      $return['launchable'] = 'ok';
      $port = config::byKey('port', 'rflink');
      if ($port != 'auto') {
          $port = jeedom::getUsbMapping($port);
          if (is_string($port)) {
              if (@!file_exists($port)) {
                  $return['launchable'] = 'nok';
                  $return['launchable_message'] = __('Le port n\'est pas configuré', __FILE__);
              }
              exec(system::getCmdSudo() . 'chmod 777 ' . $port . ' > /dev/null 2>&1');
          }
      }
      return $return;
  }

  public static function deamon_start() {
      self::deamon_stop();
      $deamon_info = self::deamon_info();
      if ($deamon_info['launchable'] != 'ok') {
          throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
      }
      $port = config::byKey('port', 'rflink');
      if ($port != 'auto') {
          $port = jeedom::getUsbMapping($port);
      }
      $rflink_path = realpath(dirname(__FILE__) . '/../../resources/rflinkd');
      $cmd = '/usr/bin/python ' . $rflink_path . '/rflinkd.py';
      $cmd .= ' --device ' . $port;
      $cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel('rflink'));
      $cmd .= ' --socketport ' . config::byKey('socketport', 'rflink');
      $cmd .= ' --serialrate 57600';
      $cmd .= ' --callback ' . network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/rflink/core/api/rflink.php';
      $cmd .= ' --apikey ' . jeedom::getApiKey('rflink');
      $cmd .= ' --cycle ' . config::byKey('cycle', 'rflink');
      $cmd .= ' --pid ' . jeedom::getTmpFolder('rflink') . '/deamon.pid';
      log::add('rflink', 'info', 'Lancement démon rflinkd : ' . $cmd);
      exec($cmd . ' >> ' . log::getPathToLog('rflink') . ' 2>&1 &');
      $i = 0;
      while ($i < 30) {
          $deamon_info = self::deamon_info();
          if ($deamon_info['state'] == 'ok') {
              break;
          }
          sleep(1);
          $i++;
      }
      if ($i >= 30) {
          log::add('rflink', 'error', 'Impossible de lancer le démon rflink, vérifiez le log rfxcmd', 'unableStartDeamon');
          return false;
      }
      message::removeAll('rflink', 'unableStartDeamon');
      sleep(2);
      self::sendIdToDeamon();
      config::save('include_mode', 0, 'rflink');
      log::add('rflink', 'info', 'Démon rflink lancé');
      return true;
  }

  public static function deamon_stop() {
      $pid_file = jeedom::getTmpFolder('rflink') . '/deamon.pid';
      if (file_exists($pid_file)) {
          $pid = intval(trim(file_get_contents($pid_file)));
          system::kill($pid);
      }
      system::kill('rflinkd.py');
      system::fuserk(config::byKey('socketport', 'rflink'));
      $port = config::byKey('port', 'rflink');
      if ($port != 'auto') {
          system::fuserk(jeedom::getUsbMapping($port));
      }
      sleep(1);
  }

public static function dependancy_info() {
    $return = array();
    $return['progress_file'] = jeedom::getTmpFolder('rflink') . '/dependance';
    if (exec(system::getCmdSudo() . system::get('cmd_check') . '-E "python\-serial|python\-request|python\-pyudev" | wc -l') >= 3) {
        $return['state'] = 'ok';
    } else {
        $return['state'] = 'nok';
    }
    return $return;
}
public static function dependancy_install() {
    log::remove(__CLASS__ . '_update');
    return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder('rflink') . '/dependance', 'log' => log::getPathToLog(__CLASS__ . '_update'));
}

}

class rflinkCmd extends cmd {

    public function execute($_options = null) {

        switch ($this->getType()) {

            case 'info' :
            return $this->getConfiguration('value');
            break;

            case 'action' :
            $id = $this->getConfiguration('id');
            $request = $this->getConfiguration('request');
            $eqLogic = $this->getEqLogic();

            switch ($this->getSubType()) {
                case 'slider':
                if ($eqLogic->getConfiguration('protocol') == 'MiLightv1') {
                    $request = $eqLogic->setColorMilight($this->getConfiguration('id'),$this->getLogicalId(),$_options['slider']);
                } else {
                    $request = str_replace('#slider#', $_options['slider'], $request);
                }
                break;
                case 'color':
                $request = str_replace('#color#', $_options['color'], $request);
                break;
                case 'message':
                if ($_options != null)  {
                    $replace = array('#title#', '#message#');
                    $replaceBy = array($_options['title'], $_options['message']);
                    if ( $_options['title'] == '') {
                        throw new Exception(__('Le sujet ne peuvent être vide', __FILE__));
                    }
                    $request = str_replace($replace, $replaceBy, $request);
                } else {
                    $request = 1;
                }
                break;
                case 'other':
                if ($eqLogic->getConfiguration('protocol') == 'MiLightv1') {
                    $request = $eqLogic->setColorMilight($this->getConfiguration('id'),$this->getLogicalId(),'other');
                } else if ($eqLogic->getConfiguration('protocol') == 'rflink') {
                    rflink::echoController($request);
                    rflink::echoController('10;STATUS;');
                    return true;
                } else {
                    $request = $request;
                    $binary = ($request == 'OFF' || $request == 'ALLOFF') ? '0' : '1';
                    $eqLogic->checkAndUpdateCmd($id, $binary);
                }
                break;
                default : $request == null ?  1 : $request;
            }

            if ($request != 'PAIR') {
                rflink::sendToController(
                    $eqLogic->getConfiguration('protocol') ,
                    $eqLogic->getConfiguration('id') ,
                    $id . ';' . $request );
            } else {
                rflink::sendToController(
                    $eqLogic->getConfiguration('protocol') ,
                    $eqLogic->getConfiguration('id') ,
                    '0;ON' );

                $id1 = dechex(hexdec($eqLogic->getConfiguration('id')) + 1);

                rflink::sendToController(
                    $eqLogic->getConfiguration('protocol') ,
                    $id1 ,
                    '0123;PAIR' );

                rflink::sendToController(
                    $eqLogic->getConfiguration('protocol') ,
                    $id1 ,
                    '0123;0;PAIR' );
                }
            }
            return true;
    }
}
