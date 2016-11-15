var net = require('net');
var fs = require('fs');
var request = require('request');

var urlJeedom = '';
var gwAddress = '';
var gwNetwork = '';
var log = '';

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

// print process.argv
process.argv.forEach(function(val, index, array) {
	switch ( index ) {
		case 2 : urlJeedom = val; break;
		case 3 : gwAddress = val; break;
		case 4 : gwNetwork = val; break;
		case 5 : log = val; break;
	}
});

var fs = require('fs');

function saveValue(data) {
	console.log((new Date()) + " - Send Value : " + data.toString() );
	url = urlJeedom + "&messagetype=saveValue&type=rflink";
	request({
		url: url,
		method: 'PUT',
		json: {"data": data.toString()},
	},
	function (error, response, body) {
		if (!error && response.statusCode == 200) {
			if (log == 'debug') {console.log((new Date()) + " - Got response Value : " + response.statusCode);}
		}else{
			console.log((new Date()) + " - SaveValue Error : "  + error );
		}
	});
}

function rfReceived(data) {
	if ((data != null) && (data != "")) {
		//LogDate("debug", "-> "  + td.toString() );
		// decoding message
		var datas = data.toString().split(";");
		var type = +datas[0];

		// decision on appropriate response
		switch (type) {
			case '20':
			saveValue(data);
			break;
		}
	}
}

console.log((new Date()) + " - Jeedom url : " + urlJeedom + ", gwAddress : " + gwAddress);

var relaunchGw = false,
    attempsGw = 0;
function launchGateway() {
  if (gwNetwork == 'none') {
  	//pour la connexion avec Jeedom => Node
  	var pathsocket = '/tmp/rflink.sock';
  	fs.unlink(pathsocket, function () {
  		var server = net.createServer(function(c) {
  			console.log((new Date()) + " - Server connected");
  			c.on('error', function(e) {
  				console.log((new Date()) + " - Error server disconnected");
  			});
  			c.on('close', function() {
  				//console.log((new Date()) + " - Connexion closed");
  			});
  			c.on('data', function(data) {
  				//console.log((new Date()) + " - Response: " + data);
  				gw.write(data.toString() + '\n');
  			});
  		});
  		server.listen(8020, function(e) {
  			console.log((new Date()) + " - server bound on 8020");
  		});
  	});

    var com = require("serialport");
	gw = new com.SerialPort(gwAddress, {
		baudrate: 57600,
		parser: com.parsers.readline('\r\n')
    });
  	gw.on('open', function() {
  		console.log((new Date()) + " - connected to serial gateway at " + gwAddress);
      relaunchGw = true;
      attemptsGw = 0;
  	}).on('data', function(rd) {
        if (log == 'debug') {console.log((new Date()) + " - "  + rd)};
        saveValue(rd);
  	}).on('end', function() {
  		console.log((new Date()) + " - disconnected from serial gateway");
  	}).on('error', function(err) {
	    if (attempsGw < 5 && relaunchGw) {
        console.log((new Date()) + ' Tentative de reconnexion de la gateway...');
        setTimeout(function() {
          gw.close();
          attempsGw++;
          launchGateway();
	      }, 5000);
	    } else if (attempsGw >= 5) {
	      console.log((new Date()) + ' 5 tentatives de connexion à la gateway ('+gwAddress+') ont échouées...');
	      gw.close();
	    } else {
	      console.log((new Date()) + ' Error gateway: ' + err.toString());
	      console.log((new Date()) + ' ' + err.stack);
	    }
  	});
  } else {
		//pour la connexion avec Jeedom => Node
  	var pathsocket = '/tmp/rflink.sock';
  	fs.unlink(pathsocket, function () {
  		var server = net.createServer(function(c) {
  			console.log((new Date()) + " - Server connected");
  			c.on('error', function(e) {
  				console.log((new Date()) + " - Error server disconnected");
  			});
  			c.on('close', function() {
  				//console.log((new Date()) + " - Connexion closed");
  			});
  			c.on('data', function(data) {
  				//console.log((new Date()) + " - Response: " + data);
  				gw.write(data.toString() + '\n');
  			});
  		});
  		server.listen(8020, function(e) {
  			console.log((new Date()) + " - server bound on 8020");
  		});
  	});

    var tmp = gwNetwork.split(':');
  	gw = require('net').Socket();
  	gw.connect({port: tmp[1], host: tmp[0]});
  	gw.setEncoding('ascii');
  	gw.on('connect', function() {
  		console.log((new Date()) + " - connected to network gateway at " + gwAddress + ":" + type);
      relaunchGw = true;
      attemptsGw = 0;
  	}).on('data', function(rd) {
        if (log == 'debug') {console.log((new Date()) + " - "  + rd)};
        saveValue(rd);
  	}).on('end', function() {
  		console.log((new Date()) + " - disconnected from network gateway");
  	}).on('error', function(err) {
	    if (attempsGw < 5 && relaunchGw) {
        console.log((new Date()) + ' Tentative de reconnexion de la gateway...');
        setTimeout(function() {
          gw.destroy();
          attempsGw++;
          launchGateway();
	      }, 1000);
	    } else if (attempsGw >= 5) {
	      console.log((new Date()) + ' 5 tentatives de connexion à la gateway ('+gwAddress+') ont échouées...');
	      gw.destroy();
	    } else {
	      console.log((new Date()) + ' Error gateway: ' + err.toString());
	      console.log(err.stack);
	    }
  	});
  }
}
launchGateway();

process.on('uncaughtException', function ( err ) {
  console.log((new Date()) + ' - An uncaughtException was found, the program will end');
  console.log((new Date()) + ' ' + err.stack);
	//process.exit(1);
});
