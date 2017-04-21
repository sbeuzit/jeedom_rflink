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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>


<form class="form-horizontal">
  <div class="form-group">
    <fieldset>
      <?php

      if (jeedom::isCapable('sudo')) {
        $gateway = config::byKey('gateLib','rflink','Firmware non reconnu');
        $release = config::byKey('avaLib','rflink');
        echo '<div class="form-group">
        <label class="col-lg-4 control-label">{{Firmware installé}}</label>
        <div class="col-lg-3">';
        echo $gateway;
        echo '</div>
        </div>';
        echo '<div class="form-group">
        <label class="col-lg-4 control-label">{{Flasher le RFLink}}</label>
        <div class="col-lg-3">
        <a class="btn btn-warning bt_flashRF"><i class="fa fa-check"></i> ';
         echo "Installation firmware R" . $release;
        echo '</a></div>
        </div>';
      } else {
        echo '<div class="alert alert danger">{{Jeedom n\'a pas les droits sudo sur votre système, il faut lui ajouter pour qu\'il puisse installer le firmware, voir <a target="_blank" href="https://jeedom.fr/doc/documentation/installation/fr_FR/doc-installation.html#autre">ici</a> partie 1.7.4}}</div>';
      }
      ?>

      <div class="form-group">
      <label class="col-lg-4 control-label">{{Vérifier les mises à jour de firmware}}</label>
      <div class="col-lg-3">
      <a class="btn btn-success bt_check"><i class="fa fa-check"></i>
        Vérifier
      </a></div>
      </div>

      <div class="form-group">
      <label class="col-lg-4 control-label">{{Redémarrer le RFLink}}</label>
      <div class="col-lg-3">
      <a class="btn btn-warning bt_restart"><i class="fa fa-power-off"></i>
        Redémarrer
      </a></div>
      </div>

      <div class="form-group">
      <label class="col-lg-4 control-label">{{Envoi de données}}</label>
      <div class="col-lg-3">
        <input name='input_cmd' type='text' class='input_cmd' placeholder=''>
      <a class="btn btn-warning bt_send"><i class="fa fa-bug"></i>
        Envoyer
      </a></div>
      </div>

      <div class="form-group">
          <label class="col-sm-4 control-label">{{Port RFXcom}}</label>
          <div class="col-sm-4">
              <select class="configKey form-control" data-l1key="port">
                  <option value="none">{{Aucun}}</option>
                  <option value="auto">{{Auto}}</option>
                  <?php
foreach (jeedom::getUsbMapping() as $name => $value) {
  echo '<option value="' . $name . '">' . $name . ' (' . $value . ')</option>';
}
foreach (ls('/dev/', 'tty*') as $value) {
  echo '<option value="/dev/' . $value . '">/dev/' . $value . '</option>';
}
?>
             </select>
         </div>
     </div>
     <div class="form-group expertModeVisible">
         <label class="col-sm-4 control-label">{{Port socket interne}}</label>
         <div class="col-sm-2">
             <input class="configKey form-control" data-l1key="socketport" />
         </div>
     </div>
     <div class="form-group expertModeVisible">
         <label class="col-sm-4 control-label">{{Cycle (s)}}</label>
         <div class="col-sm-2">
             <input class="configKey form-control" data-l1key="cycle" />
         </div>
     </div>

    </fieldset>
  </form>


  <script>
  $('.bt_flashRF').on('click',function(){
    bootbox.confirm('{{Etes-vous sûr de vouloir flasher le RFLink ? }}', function (result) {
      if (result) {
        $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/rflink/core/ajax/rflink.ajax.php", // url du fichier php
        data: {
          action: "flashRF",
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
          handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
        if (data.state != 'ok') {
          $('#div_alert').showAlert({message: data.result, level: 'danger'});
          return;
        }
        $('.bt_flashRF').text("Lancer l'installation");
      }
    });
  }
});
});

$('.bt_check').on('click',function(){
  $.ajax({// fonction permettant de faire de l'ajax
  type: "POST", // méthode de transmission des données au fichier php
  url: "plugins/rflink/core/ajax/rflink.ajax.php", // url du fichier php
  data: {
    action: "check",
  },
  dataType: 'json',
  global: false,
  error: function (request, status, error) {
    handleAjaxError(request, status, error);
  },
  success: function (data) { // si l'appel a bien fonctionné
  if (data.state != 'ok') {
    $('#div_alert').showAlert({message: data.result, level: 'danger'});
    return;
  } else {
    window.location.href = 'index.php?v=d&p=plugin&id=rflink';
  }
}
});
});

$('.bt_send').on('click',function(){
  var cmd = $('.input_cmd').val();
  $.ajax({// fonction permettant de faire de l'ajax
  type: "POST", // méthode de transmission des données au fichier php
  url: "plugins/rflink/core/ajax/rflink.ajax.php", // url du fichier php
  data: {
    action: "send",
    value: cmd,
  },
  dataType: 'json',
  global: false,
  error: function (request, status, error) {
    handleAjaxError(request, status, error);
  },
  success: function (data) { // si l'appel a bien fonctionné
  if (data.state != 'ok') {
    $('#div_alert').showAlert({message: data.result, level: 'danger'});
    return;
  }
}
});
});
</script>
</div>
</fieldset>
</form>
