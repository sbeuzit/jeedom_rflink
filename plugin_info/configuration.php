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
        $gateway = config::byKey('gateLib','rflink');
        $actual = substr($gateway, -2);
        $release = config::byKey('avaLib','rflink');
        echo '<div class="form-group">
        <label class="col-lg-4 control-label">{{Flasher le RFLink}}</label>
        <div class="col-lg-3">
        <a class="btn btn-warning bt_flashRF"><i class="fa fa-check"></i> ';
        if ($release > $actual) {
          echo "Mettre à jour en R" . $release;
        } else {
          echo "Lancer l'installation";
        }
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
      <label class="col-lg-4 control-label">{{Logger les trames inconnues}}</label>
      <div class="col-lg-3">
      <a class="btn btn-warning bt_debug"><i class="fa fa-bug"></i>
        Activer
      </a></div>
      </div>

      <div id="div_local" class="form-group">
        <label class="col-lg-4 control-label">{{RFLink USB}} :</label>
        <div class="col-lg-4">
          <select style="margin-top:5px" class="configKey form-control" data-l1key="nodeGateway">
            <option value="none">{{Aucun}}</option>
            <?php
            foreach (jeedom::getUsbMapping('', true) as $name => $value) {
              echo '<option value="' . $name . '">' . $name . ' (' . $value . ')</option>';
            }
            ?>

          </select>
        </div>
      </div>
      <div id="div_local" class="form-group">
        <label class="col-lg-4 control-label">{{RFLink réseau}} :</label>
        <div class="col-lg-4 div_network">
          <a class="btn btn-default bt_network"><i class="fa fa-plus-circle"></i>
            Ajouter un Rflink réseau
          </a>
          <table id="table_net" class="table table-bordered table-condensed">
              <tbody>
                <?php

                if (config::byKey('netgate','rflink') != '') {
                  $net = explode(";", config::byKey('netgate','rflink'));
                  foreach ($net as $value) {
                    echo "<tr><td><input name='network' type='text' class='input_network' placeholder='ip:port' value='" . $value . "'></td><td><i class='fa fa-minus-circle cursor'></i></td></tr>";
                  }
                }

                 ?>
              </tbody>
          </table>

          </div>
        </div>
      </div>
    </fieldset>
  </form>
  <?php
  if (config::byKey('jeeNetwork::mode') == 'master') {
    foreach (jeeNetwork::byPlugin('rflink') as $jeeNetwork) {
      ?>
      <form class="form-horizontal slaveConfig" data-slave_id="<?php echo $jeeNetwork->getId(); ?>">
        <fieldset>
          <legend>{{RFlink sur l'esclave}} <?php echo $jeeNetwork->getName() ?></legend>
          <div class="form-group">
            <label class="col-lg-4 control-label">{{RFLink USB}}</label>
            <div class="col-lg-4">
              <select class="slaveConfigKey form-control" data-l1key="nodeGateway">
                <option value="none">{{Aucun}}</option>
                <?php
                foreach ($jeeNetwork->sendRawRequest('jeedom::getUsbMapping', array('gpio' => true)) as $name => $value) {
                  echo '<option value="' . $name . '">' . $name . ' (' . $value . ')</option>';
                }
                ?>
              </select>
            </div>
          </div>

        </fieldset>
      </form>
      <?php
    }
  }
  ?>



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
    window.location.reload();
  }
}
});
});

$('.bt_restart').on('click',function(){
      $.ajax({// fonction permettant de faire de l'ajax
      type: "POST", // méthode de transmission des données au fichier php
      url: "plugins/rflink/core/ajax/rflink.ajax.php", // url du fichier php
      data: {
        action: "restart",
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

$('.bt_debug').on('click',function(){
  $.ajax({// fonction permettant de faire de l'ajax
  type: "POST", // méthode de transmission des données au fichier php
  url: "plugins/rflink/core/ajax/rflink.ajax.php", // url du fichier php
  data: {
    action: "debug",
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

$('.bt_network').on('click',function(){
  var newInput = $("<tr><td><input name='network' type='text' class='input_network' placeholder='ip:port'></td><td><i class='fa fa-minus-circle cursor'></i></td></tr>");
  $('#table_net tbody').append(newInput);
});

$('.cursor').on('click',function(){
  $(this).closest('tr').remove();
});

function rflink_postSaveConfiguration(){
var network = '';
$('.input_network').each(function(index, value) {
  if (network != '' ) {
    network = network + ';' + $(this).value();
  } else {
    network = $(this).value();
  }
});
$.ajax({// fonction permettant de faire de l'ajax
    type: "POST", // methode de transmission des données au fichier php
    url: "plugins/rflink/core/ajax/rflink.ajax.php", // url du fichier php
    data: {
        action: "netgate",
        value: network,
    },
    dataType: 'json',
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
}


</script>
</div>
</fieldset>
</form>
