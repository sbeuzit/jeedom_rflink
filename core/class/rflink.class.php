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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


class rflink extends eqLogic {

  public static function health() {
    $return = array();
    $statusGateway=false;
    $statusGateway = config::byKey('gateway','rflink');
    $libVer = config::byKey('gateLib','rflink');
    if ($libVer=='') {
      $libVer = '{{inconnue}}';
    }

    $return[] = array(
      'test' => __('Gateway', __FILE__),
      'result' => ($statusGateway) ? $libVer : __('NOK', __FILE__),
      'advice' => ($statusGateway) ? '' : __('Indique si la gateway est connectée avec sa version', __FILE__),
      'state' => $statusGateway,
    );
    return $return;
  }

  public static function deamon_info() {
    $return = array();
    $return['log'] = 'rflink_node';
    $return['state'] = 'nok';
    $pid = trim( shell_exec ('ps ax | grep "rflink/node/rflink.js" | grep -v "grep" | wc -l') );
    if ($pid != '' && $pid != '0') {
      $return['state'] = 'ok';
    }
    $return['launchable'] = 'ok';
    if (config::byKey('nodeGateway', 'rflink') == 'none' && config::byKey('netgate','rflink') == '') {
      $return['launchable'] = 'nok';
      $return['launchable_message'] = __('Le port n\'est pas configuré', __FILE__);
    }
    if (config::byKey('flashing', 'rflink') == '1') {
      $return['launchable'] = 'nok';
      $return['launchable_message'] = __('Flash en cours', __FILE__);
    }
    return $return;
  }

  public static function deamon_start($_debug = false) {
    self::deamon_stop();
    $deamon_info = self::deamon_info();
    if ($deamon_info['launchable'] != 'ok') {
      throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
    }
    log::add('rflink', 'info', 'Lancement du démon rflink');

    //$inclusion = config::byKey('include_mode', 'rflink');
    $inclusion = 1;

    if (config::byKey('nodeGateway', 'rflink') != 'none' && config::byKey('nodeGateway', 'rflink') != '') {
      if (config::byKey('nodeGateway', 'rflink') == 'acm') {
        $usbGateway = "/dev/ttyACM0";
      } else {
        $usbGateway = jeedom::getUsbMapping(config::byKey('nodeGateway', 'rflink'));
      }
      if ($usbGateway == '' ) {
        throw new Exception(__('Le port : n\'existe pas', __FILE__));
      }
    } else {
      $usbGateway == 'none';
    }

    if (config::byKey('netgate','rflink') != '') {
      $net = config::byKey('netgate','rflink');
    } else {
      $net = 'none';
    }


    if (config::byKey('jeeNetwork::mode') != 'master') { //Je suis l'esclave
      $url  = config::byKey('jeeNetwork::master::ip') . '/core/api/jeeApi.php?api=' . config::byKey('jeeNetwork::master::apikey');
    } else {
      if (!config::byKey('internalPort')) {
        $url = config::byKey('internalProtocol') . config::byKey('internalAddr') . config::byKey('internalComplement') . '/core/api/jeeApi.php?api=' . config::byKey('api');
      } else {
        $url = config::byKey('internalProtocol') . config::byKey('internalAddr'). ':' . config::byKey('internalPort') . config::byKey('internalComplement') . '/core/api/jeeApi.php?api=' . config::byKey('api');
      }
    }


    if ($_debug = true) {
      $log = "1";
    } else {
      $log = "0";
    }
    $sensor_path = realpath(dirname(__FILE__) . '/../../node');
    $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/rflink.js ' . $url . ' ' . $usbGateway . ' "' . $net . '" ' . $inclusion . ' 1 ' . $log;

    log::add('rflink', 'debug', 'Lancement démon rflink : ' . $cmd);

    $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('rflink_node') . ' 2>&1 &');
    if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
      log::add('rflink', 'error', $result);
      return false;
    }

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
      log::add('rflink', 'error', 'Impossible de lancer le démon rflink, vérifiez le port', 'unableStartDeamon');
      return false;
    }
    message::removeAll('rflink', 'unableStartDeamon');
    log::add('rflink', 'info', 'Démon rflink lancé');
    return true;
  }

  public static function deamon_stop() {
    exec('kill $(ps aux | grep "rflink/node/rflink.js" | awk \'{print $2}\')');
    log::add('rflink', 'info', 'Arrêt du service rflink');
    $deamon_info = self::deamon_info();
    if ($deamon_info['state'] == 'ok') {
      sleep(1);
      exec('kill -9 $(ps aux | grep "rflink/node/rflink.js" | awk \'{print $2}\')');
    }
    $deamon_info = self::deamon_info();
    if ($deamon_info['state'] == 'ok') {
      sleep(1);
      exec('sudo kill -9 $(ps aux | grep "rflink/node/rflink.js" | awk \'{print $2}\')');
    }
    config::save('gateway', '0',  'rflink');
  }


  public static function cronDaily() {
    $xml = new DOMDocument();
    $xml->loadHTMLFile('http://sourceforge.net/projects/rflink/files/?source=navbar');
    foreach($xml->getElementsByTagName('a') as $link) {
      if ($link->getAttribute('href') == "/projects/rflink/files/latest/download?source=files") {
        $title = $link->getAttribute('title');
        log::add('rflink','debug','Firmware dispo ' . $title );
        $rmzip = explode(".zip", $title);
        $release = substr($rmzip[0], -2);
      }
    }

    $gateway = config::byKey('gateLib','rflink');
    $actual = substr($gateway, -2);

    log::add('rflink','debug','Firmware dispo' . $release . ' actuel ' . $actual );

    if ($release > $actual) {
      //dwld
      //http://sourceforge.net/projects/rflink/files/latest/download
      $resource_path = realpath(dirname(__FILE__) . '/../../resources/');
      $file = file_get_contents('http://sourceforge.net/projects/rflink/files/latest/download?source=files');
      file_put_contents('/tmp/rflink.zip',$file);
      passthru('/bin/bash ' . $resource_path . '/update_firmware.sh ' . $resource_path . ' > ' . log::getPathToLog('rflink_flash') . ' 2>&1 &');
      config::save('avaLib', $release,  'rflink');
    }
  }

  public static function dependancy_info() {
    $return = array();
    $return['log'] = 'rflink_dep';
    $serialport = realpath(dirname(__FILE__) . '/../../node/node_modules/serialport');
    $request = realpath(dirname(__FILE__) . '/../../node/node_modules/request');
    $return['progress_file'] = '/tmp/rflink_dep';
    if (is_dir($serialport) && is_dir($request)) {
      $return['state'] = 'ok';
    } else {
      $return['state'] = 'nok';
    }
    return $return;
  }

  public static function dependancy_install() {
    log::add('rflink','info','Installation des dépéndances nodejs');
    $resource_path = realpath(dirname(__FILE__) . '/../../resources');
    passthru('/bin/bash ' . $resource_path . '/nodejs.sh ' . $resource_path . ' > ' . log::getPathToLog('rflink_dep') . ' 2>&1 &');
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

  public static function sendCommand( $protocol, $id, $request ) {
    foreach (jeeNetwork::byPlugin('rflink') as $jeeNetwork) {
      $jsonrpc = $jeeNetwork->getJsonRpc();
      if (!$jsonrpc->sendRequest('sendToController', array('plugin' => 'rflink', 'protocol' => $protocol, 'id' => $id, 'request' => $request))) {
        throw new Exception($jsonrpc->getError(), $jsonrpc->getErrorCode());
      }
    }
    rflink::sendToController($protocol,$id,$request);
  }

  public static function controlController( $command ) {
    if (config::byKey('nodeGateway', 'rflink') == 'none' || config::byKey('nodeGateway', 'rflink') == '') {
        return false;
    }
    $urlNode = "127.0.0.1";

    $msg = "10;" . $command . ";";
    log::add('rflink', 'info', $msg);
    $fp = fsockopen($urlNode, 8020, $errno, $errstr);
    if (!$fp) {
      echo "Service ne répond pas";
      return false;
    } else {
      fwrite($fp, $msg);
      fclose($fp);
    }
  }

  public static function sendToController( $protocol, $id, $request ) {
    if (config::byKey('nodeGateway', 'rflink') == 'none' || config::byKey('nodeGateway', 'rflink') == '') {
        return false;
    }
    $urlNode = "127.0.0.1";

    $nodeid = $protocol . '_' . $id;
    $id = str_pad($id, 6, "0", STR_PAD_LEFT);
    $msg = "10;" . $protocol . ";" . $id . ";" . $request . ";";
    log::add('rflink', 'info', $msg);
    $fp = fsockopen($urlNode, 8020, $errno, $errstr);
    if (!$fp) {
      echo "ERROR: $errno - $errstr<br />\n";
    } else {
      fwrite($fp, $msg);
      fclose($fp);
    }


    //envoi aux Rflink réseau
    if (config::byKey('netgate','rflink') != '') {
      $net = explode(";", config::byKey('netgate','rflink'));
      foreach ($net as $value) {
        $gate = explode(";", $value);
        $fp = fsockopen($gate[0], $gate[1], $errno, $errstr);
        if (!$fp) {
          echo "ERROR: $errno - $errstr<br />\n";
        } else {
          fwrite($fp, $msg);
          fclose($fp);
        }
      }
    }

    //sauvegarde de la valeur envoyée
    if (config::byKey('jeeNetwork::mode') == 'master') { //Je suis l'esclave
      $explode = explode(";", $request);
      $cmd = $explode[0];
      $value = $explode[1];
      if ($value == 'OFF') {
        $value = '0';
      }
      if ($value == 'ON') {
        $value = '1';
      }

      $rflink = self::byLogicalId($nodeid, 'rflink');
      $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($rflink->getId(),$cmd);
      if (!is_object($rflinkCmd)) {
        log::add('rflink', 'debug', 'Commande non existante, création ' . $cmd . ' sur ' . $nodeid);
        $cmds = $rflink->getCmd();
        $order = count($cmds);
        $rflinkCmd = new rflinkCmd();
        $rflinkCmd->setEqLogic_id($rflink->getId());
        $rflinkCmd->setEqType('rflink');
        $rflinkCmd->setOrder($order);
        $rflinkCmd->setLogicalId($cmd);
        $rflinkCmd->setType('info');
        $rflinkCmd->setSubType('string');
        $rflinkCmd->setName( $cmd . ' - ' . $order );
      }
      $rflinkCmd->setConfiguration('value', $value);
      $rflinkCmd->setConfiguration('request', $request);
      $rflinkCmd->save();
      $rflinkCmd->event($value);
    }


    return true;
  }

  public static function saveValue() {
    $json = file_get_contents('php://input');
    //log::add('rflink', 'debug', 'Body ' . print_r($json,true));
    $body = json_decode($json, true);
    $data = $body['data'];
    if (strpos($data,'DEBUG') !== false) {
      log::add('rflink', 'debug', 'Trame de debug recue ' . $arg[1]);
      return false;
    }
    $datas = explode(";", $data);

    if ($datas[0] == '10') {
      return false;
    }

    if (strpos($data,'Nodo RadioFrequencyLink') !== false) {
      config::save('gateLib', $datas[2],  'rflink');
      return false;
    }

    //$id = init('id');
    //$protocol = $datas[2];
    $i = 0;
    $switch = 0;
    $battery = 0;
    $hexacmd = 'TEMP,BARO,UV,RAIN,RAINTOT,WINSP,AWINSP,WINGS,WINCHL,WINTMP';
    $numcmd = 'TEMP,HUM,BARO,HSTATUS,BFORECAST,UV,RAIN,RAINTOT,WINSP,AWINSP,WINGS,WINDIR,WINCHL,WINTMP,CHIME';
    $divcmd = 'TEMP,WINSP,AWINSP';
    $generictype = 'GENERIC';
    foreach ($datas as $value) {
      if ($i == 2) {
        $protocol = $value;
      }
      if ($i == 3) {
        if (strpos($value,'=') !== false) {
          $arg = explode("=", $value);
          if (count($arg) != 2) {
            log::add('rflink', 'debug', 'Trame recue avec ID vide');
            return false;
          }
          $id = $arg[1];
        }
      }
      if ($i > 3) {
        if (strpos($value,'=') !== false) {
        $arg = explode("=", $value);
        $args[$arg[0]] = $arg[1];
        log::add('rflink', 'debug', 'Commande ' . $arg[0] . ' value ' . $arg[1]);
        if ($arg[0] == 'SWITCH') {
          // on garde de côté qu'on est sur une commande et pas une info
          $switch = 1;
        }
        if ($arg[0] == 'BAT') {
          // on garde de côté qu'on est sur une commande et pas une info
          if ($arg[1] == 'LOW') {
            // on garde de côté qu'on est sur une commande et pas une info
            $battery = 10;
          } else {
            $battery = 100;
          }
        }
      }
    }
      $i++;
    }

    if (!isset($id)) {
      log::add('rflink', 'debug', 'Trame non utilisable ' . $data);
      return false;
    }

    //reduire ID sans les 0 de début si plus de 6 caractères
    if ($id[0] == '0' && strlen($id) > 6) {
      //supp les 0 en début de switch
      $id = ltrim($id, "0");
    }
    $nodeid = $protocol . '_' . $id;
    log::add('rflink', 'debug', 'Protocole ' . $protocol . ' ID ' . $id);
    //log::add('rflink', 'debug', 'Args ' . print_r($args,true));

    $rflink = self::byLogicalId($nodeid, 'rflink');

    if (!is_object($rflink) && config::byKey('include_mode', 'rflink') == '1') {
      $rflink = new rflink();
      $rflink->setEqType_name('rflink');
      $rflink->setLogicalId($nodeid);
      $rflink->setConfiguration('id', $id);
      $rflink->setConfiguration('protocol',$protocol);
      $rflink->setName($nodeid);
      $rflink->setIsEnable(true);
      $rflink->setConfiguration('lastCommunication', date('Y-m-d H:i:s'));
      $rflink->save();
    }

    if (is_object($rflink)) {
      log::add('rflink', 'debug', 'Traitement des infos');
      $rflink->setConfiguration('lastCommunication', date('Y-m-d H:i:s'));
      $rflink->save();

      if ($battery != 0) {
          $rflink->batteryStatus($battery);
          $rflink->save();
          log::add('rflink', 'debug', 'Batterie ' . $battery);
      }

      if ($switch == 1) {
        $cmd = $args['SWITCH'];
        log::add('rflink', 'debug', 'Switch recu ' . $cmd);
        if ($cmd[0] == '0' && strlen($cmd) > 1) {
          //supp les 0 en début de switch
          $cmd = ltrim($cmd, "0");
          if ($cmd == '') {
            $cmd = '0';
          }
        }
        $value = $args['CMD'];
        if ($value == 'OFF') {
          $request = $cmd . ';' . $value;
          $value = '0';
          $generictype = 'ENERGY_OFF';
        }
        if ($value == 'ON') {
          $request = $cmd . ';' . $value;
          $value = '1';
          $generictype = 'ENERGY_ON';
        }

        //saveValue
        $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($rflink->getId(),$cmd);
        if (!is_object($rflinkCmd)) {
          log::add('rflink', 'debug', 'Commande non existante, création ' . $cmd . ' sur ' . $nodeid);
          $cmds = $rflink->getCmd();
          $order = count($cmds);
          $rflinkCmd = new rflinkCmd();
          $rflinkCmd->setEqLogic_id($rflink->getId());
          $rflinkCmd->setEqType('rflink');
          $rflinkCmd->setOrder($order);
          $rflinkCmd->setLogicalId($cmd);
          $rflinkCmd->setType('info');
          $rflinkCmd->setSubType('binary');
          $rflinkCmd->setDisplay('generic_type','ENERGY_STATUS');
          $rflinkCmd->setName( 'Switch ' . $cmd . ' - ' . $order );
        }
        $rflinkCmd->setConfiguration('value', $value);
        $rflinkCmd->setConfiguration('request', $request);
        $rflinkCmd->save();
        $rflinkCmd->event($value);
        $cmId = $rflinkCmd->getId();
        //createCmd
        $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($rflink->getId(),$request);
        if (!is_object($rflinkCmd) && is_object($rflink)) {
          log::add('rflink', 'debug', 'Commande non existante, création ' . $request . ' sur ' . $nodeid);
          $cmds = $rflink->getCmd();
          $order = count($cmds);
          $rflinkCmd = new rflinkCmd();
          $rflinkCmd->setEqLogic_id($rflink->getId());
          $rflinkCmd->setEqType('rflink');
          $rflinkCmd->setOrder($order);
          $rflinkCmd->setLogicalId($request);
          $rflinkCmd->setValue($cmId);
          $rflinkCmd->setType('action');
          $rflinkCmd->setSubType('other');
          $rflinkCmd->setName( 'Switch ' . $request . ' - ' . $order );
          $rflinkCmd->setConfiguration('value', $value);
          $rflinkCmd->setConfiguration('request', $request);
          $rflinkCmd->setDisplay('generic_type',$generictype);
          $rflinkCmd->save();
        }
      } else {
        log::add('rflink', 'debug', 'Valeur de capteur');
        foreach ($args as $cmd => $value) {
          log::add('rflink', 'debug', 'Commande ' . $cmd . ' value ' . $value);
          if ($cmd != '') {

            $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($rflink->getId(),$cmd);
            if (!is_object($rflinkCmd)) {
              log::add('rflink', 'debug', 'Commande non existante, création ' . $cmd . ' sur ' . $nodeid);
              $cmds = $rflink->getCmd();
              $order = count($cmds);
              $rflinkCmd = new rflinkCmd();
              $rflinkCmd->setEqLogic_id($rflink->getId());
              $rflinkCmd->setEqType('rflink');
              $rflinkCmd->setOrder($order);
              $rflinkCmd->setLogicalId($cmd);
              $rflinkCmd->setType('info');
              switch ($cmd) {
                case 'SWITCH' :
                $generictype = 'ENERGY_STATE';
                break;
                case 'CMD' :
                $generictype = 'ENERGY_STATE';
                break;
                case 'TEMP' :
                $generictype = 'TEMPERATURE';
                break;
                case 'HUM' :
                $generictype = 'HUMIDITY';
                break;
                case 'BARO' :
                $generictype = 'PRESSURE';
                break;
                case 'HSTATUS' :
                $generictype = 'HEATING_STATE';
                break;
                case 'UV' :
                $generictype = 'UV';
                break;
                case 'BAT' :
                $generictype = 'BATTERY';
                break;
                case 'RAIN' :
                $generictype = 'RAIN_CURRENT';
                break;
                case 'RAINTOT' :
                $generictype = 'RAIN_TOTAL';
                break;
                case 'WINSP' :
                $generictype = 'WIND_SPEED';
                break;
                case 'AWINSP' :
                $generictype = 'WIND_SPEED';
                break;
                case 'WINGS' :
                $generictype = 'WIND_SPEED';
                break;
                case 'AWINGS' :
                $generictype = 'WIND_SPEED';
                break;
                case 'WINDIR' :
                $generictype = 'WIND_DIRECTION';
                break;
                case 'SMOKEALERT' :
                $generictype = 'SMOKE';
                break;
                case 'PIR' :
                $generictype = 'PRESENCE';
                break;
                case 'CO2' :
                $generictype = 'CO2';
                break;
                case 'KWATT' :
                $generictype = 'CONSUMPTION';
                break;
                case 'WATT' :
                $generictype = 'CONSUMPTION';
                break;
                case 'VOLT' :
                $generictype = 'VOLTAGE';
                break;
                case 'CURRENT' :
                $generictype = 'VOLTAGE';
                break;
              }
              $rflinkCmd->setDisplay('generic_type',$generictype);
              if (strpos($numcmd,$cmd) !== false) {
                $rflinkCmd->setSubType('numeric');
              } else {
                $rflinkCmd->setSubType('string');
              }
              $rflinkCmd->setName( $cmd . ' - ' . $order );
            }
            // calcul valeur pour la temp et autres cas particuliers
            if ($cmd == 'TEMP') {
              if (substr($value,0,1) != 0) {
                $value = '-' . hexdec(substr($value, -3));
              } else {
                $value = hexdec(substr($value, -3));
              }
            } else if (strpos($hexacmd,$cmd) !== false) {
              $value = hexdec($value);
            }
            if (strpos($divcmd,$cmd) !== false) {
              $value = $value/10;
            }
            $rflinkCmd->setConfiguration('value', $value);
            $rflinkCmd->save();
            $rflinkCmd->event($value);
            log::add('rflink', 'debug', 'Commande ' . $cmd . ' value ' . $value);
          }
        }
      }
    }

  }

  public static function saveGateway() {
    $status = init('status');
    config::save('gateway', $status,  'rflink');
  }

  public static function saveInclude($mode) {
    config::save('include_mode', $mode,  'rflink');
  }

  public static function saveNetGate($value) {
    config::save('netgate', $value,  'rflink');
  }

  public function preSave() {
    if ($this->getConfiguration('idManuel')!='') {
      $id = $this->getConfiguration('idManuel');
      $protocol = $this->getConfiguration('protocolManuel');
      log::add('rflink', 'debug', 'Création équipement ' . $id . ' en ' . $protocol);
      $this->setLogicalId($protocol . '_' . $id);
      $this->setConfiguration('protocol',$protocol);
      $this->setConfiguration('id',$id);
      $this->setConfiguration('protocolManuel',$protocol);
      $this->setConfiguration('idManuel',$id);
    }
  }

  public static function getNetwork() {
    $return = "{";
    $i = 0;
    $net = explode(";", config::byKey('netgate','rflink'));
    foreach ($net as $value) {
      if (strpos($value,':') === false) {
        throw new Exception(__('Saisie non valide pour la gateway réseau : ', __FILE__) . $value);
      }
      $gate = explode(":", $value);
      if ($return != "{") {
        $return .= ",";
      }
      $return .= "'net" . $i . "':{'addr':'" . $gate[0] . "','port':'" . $gate[1] . "'}";
      $i++;
    }
    $return .= "}";
    print $return;
  }

  public static function event() {

    $messageType = init('messagetype');
    switch ($messageType) {
      case 'saveValue' : self::saveValue(); break;
      case 'saveGateway' : self::saveGateway(); break;
      case 'getNetwork' : self::getNetwork(); break;
    }

  }

}

class rflinkCmd extends cmd {

  public function execute($_options = null) {

    switch ($this->getType()) {

      case 'info' :
      return $this->getConfiguration('value');
      break;

      case 'action' :
      $request = $this->getConfiguration('request');

      switch ($this->getSubType()) {
        case 'slider':
        $request = str_replace('#slider#', $_options['slider'], $request);
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

        }
        else
        $request = 1;

        break;
        default : $request == null ?  1 : $request;

      }

      $eqLogic = $this->getEqLogic();

      rflink::sendCommand(
      $eqLogic->getConfiguration('protocol') ,
      $eqLogic->getConfiguration('id') ,
      $request );

      $result = $request;

      return $result;
    }
    return true;
  }



  /*     * **********************Getteur Setteur*************************** */
}
