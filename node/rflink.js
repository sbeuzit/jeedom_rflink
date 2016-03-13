var net = require('net');
var fs = require('fs');
var request = require('request');

var urlJeedom = '';
var gwAddress = '';
var gwNetwork = '';
var debug = '';
var gw = new Array();

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

// print process.argv
process.argv.forEach(function(val, index, array) {
	switch ( index ) {
		case 2 : urlJeedom = val; break;
		case 3 : gwAddress = val; break;
		case 4 : gwNetwork = val; break;
		case 5 : debug = val; break;
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
			if (debug == 1) {console.log((new Date()) + "Got response Value: " + response.statusCode);}
		}else{
			console.log((new Date()) + " - SaveValue Error : "  + error );
		}
	});
}

function saveGateway(status) {
	console.log((new Date()) + " - Save Gateway Status " + status);
	url = urlJeedom + "&messagetype=saveGateway&type=rflink&status="+status;
	request(url, function (error, response, body) {
		if (!error && response.statusCode == 200) {
			if (debug == 1) {console.log((new Date()) + "Got response saveSensor: " + response.statusCode);}
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

var pathsocket = '/tmp/rflink.sock';
fs.unlink(pathsocket, function () {
	var server = net.createServer(function(c) {

		console.log((new Date()) + " - server connected");

		c.on('error', function(e) {
			console.log((new Date()) + " - Error server disconnected");
		});

		c.on('close', function() {
			console.log((new Date()) + " - server disconnected");
		});

		c.on('data', function(data) {
			console.log((new Date()) + " - Response: " + data);
			gw['serial'].write(data.toString() + '\n');
		});

	});
	server.listen(8020, function(e) {
		console.log((new Date()) + " - server bound on 8020");
	});
});

if (gwNetwork != 'none') {
	var i = 0;
	gwNetwork.split(";").forEach(function (item) {
		var netgate =	item.split(":");
		console.log("net" + i + ": " + netgate[0] + " sur " + netgate[1]);
		var net = ["net" + i];
		gw[net] = require('net').Socket();
		gw[net].connect(netgate[0], netgate[1]);
		gw[net].setEncoding('ascii');
		gw[net].on('connect', function() {
			console.log((new Date()) + " - connected to network gateway at " + gwAddress + ":" + gwPort);
			saveGateway('1');
		}).on('data', function(rd) {
			if (debug == 1) {console.log((new Date()) + " - "  + rd)};
			saveValue(rd);
		}).on('end', function() {
			console.log((new Date()) + " - disconnected from network gateway");
			saveGateway('0');
		}).on('error', function() {
			console.log((new Date()) + " - connection error - trying to reconnect");
			saveGateway('0');
			gw[net].connect(netgate[0], netgate[1]);
			gw[net].setEncoding('ascii');
		});
		i++;

	});


}

if (gwAddress != 'none') {
	var com = require("serialport");
	gw['serial'] = new com.SerialPort(gwAddress, {
		baudrate: 57600,
		parser: com.parsers.readline('\r\n')
	});
	gw['serial'].open();
	gw['serial'].on('open', function() {
		console.log((new Date()) + " - connected to serial gateway at " + gwAddress);
		saveGateway('1');
	}).on('data', function(rd) {
		if (debug == 1) {console.log((new Date()) + " - "  + rd)};
		saveValue(rd);
	}).on('end', function() {
		console.log((new Date()) + " - disconnected from serial gateway");
		saveGateway('0');
	}).on('error', function(error) {
		console.log((new Date()) + " - connection error - trying to reconnect");
		saveGateway('0');
		setTimeout(function() {gw['serial'].open();}, 5000);
	});
}

process.on('uncaughtException', function ( err ) {
	console.log((new Date()) + " - An uncaughtException was found, the program will end");
	//process.exit(1);
});
