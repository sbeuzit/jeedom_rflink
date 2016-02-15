var net = require('net');
var fs = require('fs');
var request = require('request');

var urlJeedom = '';
var gwAddress = '';
var gwNetwork = '';
var inclusion = '';
var logLevel = new Array();
var gw = new Array();

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

// print process.argv
process.argv.forEach(function(val, index, array) {

	switch ( index ) {
		case 2 : urlJeedom = val; break;
		case 3 : gwAddress = val; break;
		case 4 : gwNetwork = val; break;
		case 5 : inclusion = val; break;
		case 6 : logLevel['info'] = val; break;
		case 7 : logLevel['debug'] = val; break;
	}

});



const gwBaud = 57600;

var fs = require('fs');
var appendedString="";

Date.prototype.getFullDay = function () {
   if (this.getDate() < 10) {
       return '0' + this.getDate();
   }
   return this.getDate();
};

Date.prototype.getFullMonth = function () {
   t = this.getMonth() + 1;
   if (t < 10) {
       return '0' + t;
   }
   return t;
};

Date.prototype.getFullHours = function () {
   if (this.getHours() < 10) {
       return '0' + this.getHours();
   }
   return this.getHours();
};

Date.prototype.getFullMinutes = function () {
   if (this.getMinutes() < 10) {
       return '0' + this.getMinutes();
   }
   return this.getMinutes();
};

Date.prototype.getFullSeconds = function () {
   if (this.getSeconds() < 10) {
       return '0' + this.getSeconds();
   }
   return this.getSeconds();
};

function LogDate(Type, Message) {
 if ( logLevel[Type] == 0 ) return;
   var ceJour = new Date();
//       var ceJourJeedom = ceJour.getDate() + "/" + ceJour.getMonth() + "/" + ceJour.getFullYear() + " " + ceJour.getHours() + ":" + ceJour.getMinutes() + ":" + ceJour.getSeconds();
       var ceJourJeedom = ceJour.getFullDay() + "-" + ceJour.getFullMonth() + "-" + ceJour.getFullYear() + " " + ceJour.getFullHours() + ":" + ceJour.getFullMinutes() + ":" + ceJour.getFullSeconds();
       console.log(ceJourJeedom + " | " + Type + " | " + Message);
}

function saveValue(data) {
	LogDate("info", "Send Value : " + data.toString() );
	url = urlJeedom + "&messagetype=saveValue&type=rflink";
	request({
		url: url,
		method: 'PUT',
		json: {"data": data.toString()},
	},
function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("debug", "Got response Value: " + response.statusCode);
	  }else{
	  	LogDate("debug", "SaveValue Error : "  + error );
	  }
	});
}

function saveGateway(status) {
	LogDate("info", "Save Gateway Status " + status);

	url = urlJeedom + "&messagetype=saveGateway&type=rflink&status="+status;

	request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("debug", "Got response saveSensor: " + response.statusCode);
	  }
	});
}

function appendData(str, gw) {
    pos=0;
    while (str.charAt(pos) != '\n' && pos < str.length) {
        appendedString=appendedString+str.charAt(pos);
        pos++;
    }
    if (str.charAt(pos) == '\n') {
        rfReceived(appendedString.trim(), gw);
        appendedString="";
    }
    if (pos < str.length) {
        appendData(str.substr(pos+1,str.length-pos-1), gw);
    }
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

LogDate("info", "Jeedom url : " + urlJeedom);
LogDate("info", "gwAddress : " + gwAddress);
LogDate("info", "Inclusion : " + inclusion);

	var pathsocket = '/tmp/rflink.sock';
	fs.unlink(pathsocket, function () {
	  var server = net.createServer(function(c) {

		LogDate("debug", "server connected");

		c.on('error', function(e) {
		  LogDate("error", "Error server disconnected");
		});

		c.on('close', function() {
		  LogDate("debug", "server disconnected");
		});

		c.on('data', function(data) {
			LogDate("info", "Response: " + data);
				gw['serial'].write(data.toString() + '\n');
		});

	  });
	  server.listen(8020, function(e) {
		LogDate("info", "server bound on 8020");
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
			LogDate("info", "connected to network gateway at " + gwAddress + ":" + gwPort);
			saveGateway('1');
		}).on('data', function(rd) {
			saveValue(rd);
		}).on('end', function() {
			LogDate("error", "disconnected from network gateway");
			saveGateway('0');
		}).on('error', function() {
			LogDate("error", "connection error - trying to reconnect");
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
			LogDate("info", "connected to serial gateway at " + gwAddress);
			saveGateway('1');
		}).on('data', function(rd) {
			LogDate("debug", rd);
			saveValue(rd);
		}).on('end', function() {
			LogDate("error", "disconnected from serial gateway");
			saveGateway('0');
		}).on('error', function(error) {
            LogDate("error", "connection error - trying to reconnect: " + error);
            saveGateway('0');
            setTimeout(function() {gw['serial'].open();}, 5000);
		});
}

	process.on('uncaughtException', function ( err ) {
    console.error('An uncaughtException was found, the program will end.');
    //hopefully do some logging.
    //process.exit(1);
});
