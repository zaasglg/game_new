const mysql = require("mysql2"); 
const axios = require('axios').default; 

const express = require('express');
const app = express();
const http = require('http');
const server = http.createServer( app ); 
const cors = require('cors');

var $trace = true; 

const connection = mysql.createPool({  
	connectionLimit: 5,
	host: "localhost",
	port: 3306,
	user: "root",
	database: "aviator_game",
	password: "p@ssword", 
	waitForConnections: true,
	connectionLimit: 10, 
	queueLimit: 0 
}).promise(); 

// Внешняя база данных valor отключена для локальной разработки
// const valor = mysql.createPool({  
//	connectionLimit: 5,
//	host: "111.90.151.49",
//	user: "panelhos_root2",
//	database: "panelhos_dbvalor",
//	password: "fO2~lJf=4]-H", 
//	waitForConnections: true,
//	connectionLimit: 10, 
//	queueLimit: 0 
// }).promise(); 

app.use(cors({
	methods: 'GET,POST,PATCH,PUT,DELETE,OPTIONS',
	optionsSuccessStatus: 200,
	origin: '*', //process.env.ORIGIN, 
	credentials: true,
	headers: 'Authorization,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken'
}));
app.options('*', cors());

const io = require('socket.io')(server, {
  	cors: {
	    origin: '*', 
		credentials: true, 
		allowEIO3: false, 
		rejectUnauthorized: false,
		methods: ['GET','POST','PATCH','PUT','DELETE','OPTIONS'] 
	}
});

app.get('/', (req, res) => { res.send('Connection'); }); 
io.sockets.on('connection', (socket) => { 
	console.log('a user connected'); 
	socket.emit('message', JSON.stringify({ uid:"all", msg:'connection complete' })); 
	socket.on( 'message', ( $input ) => {
		var $fsoc = {} 
		console.log("Recieved message: ", $input); 
	});
}); 

server.listen(2345, () => { console.log('listening on *:2345'); }); 

var TIMERS = [
	{ 'id': 'loading', 'time': 5000 }, 
	{ 'id': 'flying', 'time': 30000 }, 
	{ 'id': 'finish', 'time': 5000 }
];
var CUR_STATE = 2; 
var START = new Date().getTime(); 
var CURRENT_GAME = 0; 
var CURRENT_CF = 1; 

async function current_game(){
	var $Q = "SELECT g.`id`, g.`cf`, g.`status`, g.`finish`, cf.`amount`, "+
	                "( UNIX_TIMESTAMP() - UNIX_TIMESTAMP( g.`date` ) ) AS 'delta', "+
	                "DATE_FORMAT( g.`date`, '%Y-%m-%d %H:%i:%s' ) AS 'start' "+
	            "FROM `games` AS g "+
	            "LEFT JOIN `cf` AS cf ON cf.`id`=g.`cf` "+ 
	            "WHERE g.`finish` IS NULL AND g.`status` IN (1,2) "+ 
	            "ORDER BY g.`id` DESC LIMIT 1 "; 
	var $last_game = await connection.execute( $Q ); 
	console.log("Current game: ", $last_game[0][0] ); 

	return $last_game[0][0]; 
} 

async function last_game(){
	var $Q = "SELECT g.`id`, g.`cf`, g.`status`, g.`finish`, cf.`amount` "+
	//                "( UNIX_TIMESTAMP() - UNIX_TIMESTAMP( g.`date` ) ) AS 'delta', "+
	//                "DATE_FORMAT( g.`date`, '%Y-%m-%d %H:%i:%s' ) AS 'start' "+
	            "FROM `games` AS g "+
	            "LEFT JOIN `cf` AS cf ON cf.`id`=g.`cf` "+ 
	            "WHERE g.`finish` IS NOT NULL AND g.`status`=7 "+ 
	            "ORDER BY g.`id` DESC LIMIT 1 "; 
	var $last_game = await connection.execute( $Q ); 
	console.log("Last game: ", $last_game[0][0] ); 

	return $last_game[0][0]; 
} 

async function next_cf(){ 
	var $next_cf = {}
	var $last_game = await last_game(); 
	if( +$last_game.cf ){ 
		$Q = "SELECT cf.* FROM `cf` AS cf "+ 
                "WHERE cf.`id`>'"+ $last_game.cf +"' AND cf.`status` NOT IN (5) "+ 
                "ORDER BY cf.`id` ASC LIMIT 1"; 
        $next_cf = await connection.execute( $Q );
        if( !$next_cf || !$next_cf[0] || !$next_cf[0][0] ){
            $Q = "SELECT cf.* FROM `cf` AS cf "+ 
                    "WHERE cf.`status` NOT IN (5) "+
                    "ORDER BY cf.`id` ASC LIMIT 1";
            $next_cf = await connection.execute( $Q );
        }
    } 
    else {
	    $Q = "SELECT cf.* FROM `cf` AS cf "+ 
	            "WHERE cf.`status` NOT IN (5) "+ 
	            "ORDER BY cf.`id` ASC LIMIT 1"; 
	    $next_cf = await connection.execute( $Q );
	}
	console.log("Next cf: ", $next_cf[0][0]);
	return $next_cf[0][0]; 
}

async function create_game(){ 
	var $next_cf = await next_cf(); 
	if( $next_cf ){ 
		CURRENT_CF = $next_cf.amount; 
		var $Q = "INSERT INTO `games` (`cf`,`status`) VALUES('"+ $next_cf.id +"',1)"; 
		var $new_game = await connection.execute( $Q ); 
		console.log("New game: ", $new_game[0].insertId); 
		CURRENT_GAME = $new_game[0].insertId
	} 
	else { 
		console.log("Get next cf failed"); 
	} 
}

async function start_game(){
	var $current_game = await current_game(); 
	if( $current_game ){
		var $Q = "UPDATE `games` SET `status`=2 "+
					"WHERE `id`='"+ $current_game.id +"' AND `status`=1"; 
		var $start_game = await connection.execute( $Q );
		console.log("Start game: ", $start_game);
	} 
	else {
		console.log("Current game for start not found");
	}
}

async function close_bets( $game_id ){
	var $Q = "SELECT GROUP_CONCAT(`id`) AS 'ids' FROM `bets` "+
				"WHERE `game`='"+ $game_id +"' AND `status`=2"; 
	var $bets = await connection.execute( $Q ); 
	console.log("Bets found for game "+ $game_id +":", $bets[0][0]);
	if( $bets && $bets[0] && $bets[0][0] && $bets[0][0].ids ){
		$Q = "UPDATE `bets` SET `result`=0, `cf`=0, `status`=7 "+ 
				"WHERE `id` IN ("+ $bets[0][0].ids +")";
	}
}

async function finish_game(){ 
	var $current_game = await current_game(); 
	if( $current_game && $current_game.id ){ 
		$Q = "UPDATE `games` SET `status`=7, `finish`=NOW() "+
				"WHERE `status` IN (1,2) AND `finish` IS NULL";
		await connection.execute( $Q ); 
		close_bets( $current_game.id ); 
	} 
	else {
		console.log("Current game for finish not found");
	}
}

async function update_server(){
	var $cur_time = new Date().getTime(); 
	var $delta = $cur_time - START; 
	
	// Отправляем текущий коэффициент во время полета
	if( CUR_STATE === 1 ) { // flying state
		var current_cf = 1 + ($delta / 1000) * 0.05;
		if( current_cf >= CURRENT_CF ) {
			current_cf = CURRENT_CF;
			// Завершаем игру когда достигнут целевой коэффициент
			START = $cur_time;
			CUR_STATE = 2; // переходим к finish
		}
		io.emit('coefficient', JSON.stringify({
			uid: "all",
			msg: "coefficient_update",
			cf: current_cf.toFixed(2)
		}));
	}
	
	if( $delta >= TIMERS[ CUR_STATE ].time ){
		START = $cur_time; 
		CUR_STATE += 1; 
		CUR_STATE = CUR_STATE >= TIMERS.length ? 0 : CUR_STATE; 
		var $res = {
			uid: "all", 
			msg: "Change game state", 
			game: {
				id: CURRENT_GAME, 
				state: TIMERS[ CUR_STATE ].id, 
				cf: CURRENT_CF, 
				delta: TIMERS[ CUR_STATE ].time 
			}
		}
		switch( CUR_STATE ){
			case 0: 		// loading
				create_game();
				break; 
			case 1: 		// flying 
				start_game();
				TIMERS[ CUR_STATE ].time = $res.game.delta = parseInt( 5000 * Math.log( 2 * CURRENT_CF - 1 ) ); 
				console.log("Set fly timer to: "+TIMERS[ CUR_STATE ].time );
				break; 
			case 2: 		// finish 
				finish_game(); 
				break;  
		}
		console.log("System message: ", $res );
		io.emit('message', JSON.stringify( $res )); 
	}
}

var process_game = setInterval( update_server, 1000 );

//process.exit(); 

