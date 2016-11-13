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
        rflink::check();
    }

    public static function check() {
        $xml = new DOMDocument();
        $gateway = config::byKey('gateLib','rflink');
        $release = substr($gateway, -2);
        $version = substr($gateway, -9, 3);
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

    public function checkActOk($_id, $_name, $_subtype, $_cmdid, $_request, $_setval, $_maxslider) {
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
            if ($_setval != '0') {
                $rflinkCmd->setValue($_setval);
            }
            if ($_maxslider != '0') {
                $rflinkCmd->setConfiguration('minValue', 0);
                $rflinkCmd->setConfiguration('maxValue', $_maxslider);
            }
            $rflinkCmd->setConfiguration('id',$_cmdid);
            $rflinkCmd->setConfiguration('request',$_request);
            $rflinkCmd->save();
        }
    }

    public function checkHexaCmd($_cmd, $_value) {
        $hexacmd = 'TEMP,BARO,UV,LUX,,RAIN,RAINRATE,WINSP,AWINSP,WINGS,WINCHL,WINTMP,KWATT,WATT';
        if (strpos($hexacmd,$_cmd) !== false) {
            $result = hexdec($_value);
        } else {
            $result = $_value;
        }
        return $result;
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
        $divcmd = 'TEMP,RAIN,RAINRATE,RAINTOT,WINSP,AWINSP';
        if (strpos($divcmd,$_cmd) !== false) {
            $result = $_value/10;
        } else {
            $result = $_value;
        }
        return $result;
    }

    public function registerRTS($_cmd, $_value) {
        //checkCmdOk($_id, $_name, $_subtype, $_value)
        //checkActOk($_id, $_name, $_subtype, $_cmdid, $_request, $_setval, $_maxslider)
        $this->checkCmdOk($_cmd, 'Statut ' . $_cmd, 'binary', $_value);
        $this->checkAndUpdateCmd($_cmd, $_value);
        $thisCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),$_cmd);
        $cmId = $thisCmd->getId();
        $this->checkActOk('PAIR' . $_cmd, 'Appairement ' . $_cmd, $_cmd, 'PAIR', $cmId, '0');
        $this->checkActOk('UP' . $_cmd, 'Montée ' . $_cmd, $_cmd, 'UP', 'UP', $cmId, '0');
        $this->checkActOk('DOWN' . $_cmd, 'Descente ' . $_cmd, $_cmd, 'DOWN', 'DOWN', $cmId, '0');
        $this->checkActOk('STOP' . $_cmd, 'Arret ' . $_cmd, $_cmd, 'STOP', 'STOP', $cmId, '0');
    }

    public function registerMilightv1($_cmd, $_value, $_rgbw) {
        //checkCmdOk($_id, $_name, $_subtype, $_value)
        //checkActOk($_id, $_name, $_subtype, $_cmdid, $_request, $_setval, $_maxslider)
        $this->checkCmdOk($_cmd, 'Etat Lampe ' . $_cmd, 'string', $_value);
        $thisCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),$_cmd);
        $cmId = $thisCmd->getId();
        $this->checkCmdOk('ON' . $cmd, 'On ' . $cmd, 'other', $_cmd, '#color#;ON', $cmId, '0');
        $this->checkCmdOk('ALLON' . $cmd, 'All On ' . $cmd, 'other', $_cmd, '#color#;ALLON', $cmId, '0');
        $this->checkCmdOk('OFF' . $cmd, 'Off ' . $cmd, 'other', $_cmd, '#color#;OFF', $cmId, '0');
        $this->checkCmdOk('ALLOFF' . $cmd, 'All Off ' . $cmd, 'other', $_cmd, '#color#;ALLOFF', $cmId, '0');

        $this->checkCmdOk('RGBW' . $_cmd, 'Couleur Lampe ' . $_cmd, 'string', $_rgbw);
        $this->checkAndUpdateCmd('RGBW' . $cmd, $_rgbw);
        $this->checkCmdOk('color_val' . $_cmd, 'Couleur Valeur ' . $_cmd, 'string', substr($_rgbw, 0, 2));
        $thisCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),'color_val' . $_cmd);
        $cmId = $thisCmd->getId();
        $this->checkCmdOk('COLOR' . $_cmd, 'Couleur ' . $_cmd, 'slider', $_cmd, '#color#;COLOR', $cmId, '255');
        $this->checkCmdOk('bright_val' . $_cmd, 'Luminosité Valeur ' . $_cmd, 'string', substr($_rgbw, -2));
        $thisCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),'bright_val' . $_cmd);
        $cmId = $thisCmd->getId();
        $this->checkCmdOk('BRIGHT' . $_cmd, 'Luminosité ' . $_cmd, 'slider', $_cmd, '#color#;BRIGHT', $cmId, '32');
    }

    public function registerSwitch($_cmd, $_value) {
        //checkCmdOk($_id, $_name, $_subtype, $_value)
        //checkActOk($_id, $_name, $_subtype, $_cmdid, $_request, $_setval, $_maxslider)
        if ($_cmd[0] == '0' && strlen($_cmd) > 1) {
            //supp les 0 en début de switch
            $_cmd = ltrim($_cmd, "0");
            $_cmd = ($_cmd == '') ? '0' : $_cmd;
        }
        $binary = ($_value == 'OFF') ? '0' : '1';
        $this->checkCmdOk($_cmd, 'Statut ' . $_cmd, 'binary', $binary);
        $this->checkAndUpdateCmd($_cmd, $binary);
        $thisCmd = rflinkCmd::byEqLogicIdAndLogicalId($this->getId(),$_cmd);
        $cmId = $thisCmd->getId();
        $this->checkCmdOk($_value . $_cmd, $_value . $_cmd, 'other', $_cmd, 'ON', $cmId, '0');
    }

    public function registerBattery($_value) {
        $battery = ($_value == 'LOW') ? '10' : '100';
        $this->batteryStatus($battery);
        $this->save();
    }

    public function registerInfo($_cmd, $_value) {
        // calcul valeur pour la temp et autres cas particuliers
        log::add('rflink', 'debug', 'Commande capteur ' . $_cmd . ' value ' . $_value);
        if ($_cmd != '') {
            if ($_cmd == 'TEMP') {
                if (substr($_value,0,1) != 0) {
                    $_value = '-' . hexdec(substr($_value, -3));
                } else {
                    $_value = hexdec(substr($_value, -3));
                }
            }
            $_value = $this->checkHexaCmd($_cmd,$_value);
            $_value = $this->checkDivCmd($_cmd,$_value);
            $cmds = $this->getCmd();
            $this->checkCmdOk($_cmd, $_cmd . ' - ' . count($cmds), 'info', rflink::checkNumCmd($_cmd), $_value, '0', '0');
            $this->checkAndUpdateCmd($_cmd, $_value);
        }
    }

    public static function receiveData($json) {
        //log::add('rflink', 'debug', 'Body ' . print_r($json,true));
        $body = json_decode($json, true);
        $data = $body['data'];
        if (strpos($data,'DEBUG') !== false) {
            log::add('rflink', 'debug', 'Trame de debug recue : ' . $data);
            return false;
        }
        if (strpos($data,'Nodo RadioFrequencyLink') !== false) {
            config::save('gateLib', $datas[2],  'rflink');
            return false;
        }
        $datas = explode(";", $data);
        if ($datas[0] == '10') {
            //envoi de données, on va pas plus loin
            return false;
        }

        $protocol = $datas[2];

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
                'state' => $state
            )
        );
    }

    if (!is_object($rflink)) {
        return false;
    }

    $i=0;
    $args = array();
    foreach ($datas as $info) {
        if ($i > 3) {
            if (strpos($value,'=') !== false) {
                $arg = explode("=", $value);
                $args[$arg[1]] = $arg[2];
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
                $rflink->registerRTS($value,$datas['CMD']);
                break;
                case 'MiLightv1' :
                $rflink->registerMilightv1($value,$datas['CMD'],$datas['RGBW']);
                break;
                default :
                $rflink->registerSwitch($value,$datas['CMD']);
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

public function postUpdate() {
    $nodeid = $this->getConfiguration('protocol') . '_' . $this->getConfiguration('id');
    $this->setLogicalId($nodeid);
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
    if (config::byKey('nodeGateway', 'rflink') == 'none' || config::byKey('nodeGateway','rflink') == '') {
        $return['launchable'] = 'nok';
        $return['launchable_message'] = __('Le port n\'est pas configuré', __FILE__);
    }
    if (config::byKey('flashing', 'rflink') == '1') {
        $return['launchable'] = 'nok';
        $return['launchable_message'] = __('Flash en cours', __FILE__);
    }
    return $return;
}

public static function deamon_start() {
    self::deamon_stop();
    $deamon_info = self::deamon_info();
    if ($deamon_info['launchable'] != 'ok') {
        throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
    }
    log::add('rflink', 'info', 'Lancement du démon rflink');

    if (config::byKey('nodeGateway', 'rflink') == 'acm') {
        $usbGateway = "/dev/ttyACM0";
    } else if (config::byKey('nodeGateway', 'rflink') == 'network') {
        $usbGateway = 'network';
    } else {
        $usbGateway = jeedom::getUsbMapping(config::byKey('nodeGateway', 'rflink'));
    }
    if ($usbGateway == '' ) {
        throw new Exception(__('Le port : n\'existe pas', __FILE__));
    }

    $net = config::byKey('netGateway', 'rflink', 'none');

    $url = network::getNetworkAccess('internal') . '/plugins/rflink/core/api/rflink.php?apikey=' . jeedom::getApiKey('rflink');

    $log = log::convertLogLevel(log::getLogLevel('rflink'));

    $sensor_path = realpath(dirname(__FILE__) . '/../../node');
    if ($usbGateway != "none") {
        exec('sudo chmod -R 777 ' . $usbGateway);
    }
    $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/rflink.js ' . $url . ' ' . $usbGateway . ' ' . $net . ' ' . $log;

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

}

class rflinkCmd extends cmd {

    public function execute($_options = null) {

        switch ($this->getType()) {

            case 'info' :
            return $this->getConfiguration('value');
            break;

            case 'action' :
            $request = $this->getConfiguration('request');
            $eqLogic = $this->getEqLogic();

            switch ($this->getSubType()) {
                case 'slider':
                if ($eqLogic->getConfiguration('protocol') == 'MiLightv1') {
                    if ($this->getConfiguration('milight') == 'color') {
                        $color = substr(dechex($_options['slider']),-2);
                        $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'color_val'.$this->getConfiguration('id'));
                        $rflinkCmd->setConfiguration('value', $color);
                        $rflinkCmd->save();
                        //log::add('rflink', 'debug', 'Milight Color ' . $color);
                    } else {
                        $color = substr(dechex($_options['slider']*8),-2);
                        $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'bright_val'.$this->getConfiguration('id'));
                        $rflinkCmd->setConfiguration('value', $color);
                        $rflinkCmd->save();
                        //log::add('rflink', 'debug', 'Milight Bright ' . $color);
                    }
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

                }
                else
                $request = 1;
                break;
                default : $request == null ?  1 : $request;
            }

            if ($request != 'PAIR') {
                if ($eqLogic->getConfiguration('protocol') == 'MiLightv1') {
                    $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'color_val'.$this->getConfiguration('id'));
                    $color = $rflinkCmd->getConfiguration('value');
                    $rflinkCmd = rflinkCmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'bright_val'.$this->getConfiguration('id'));
                    $bright = $rflinkCmd->getConfiguration('value');
                    $request = str_replace('#color#', $color.$bright, $this->getConfiguration('request'));
                    //log::add('rflink', 'debug', 'Milight ' . $color.$bright . ' : '. $request);
                    rflink::sendToController(
                        $eqLogic->getConfiguration('protocol') ,
                        $eqLogic->getConfiguration('id') ,
                        $request );
                    } else {
                        rflink::sendToController(
                            $eqLogic->getConfiguration('protocol') ,
                            $eqLogic->getConfiguration('id') ,
                            $request );
                        }
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

                                $result = $request;
                                return $result;
                            }
                            return true;
                        }
                    }
