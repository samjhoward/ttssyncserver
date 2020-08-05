<?php 
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
	
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
$limit = 10;


$password = $_ENV["TTS_PASS"];
if (!array_key_exists("p", $_GET) || $_GET["p"] != $password) {
	echo "TTSSync: Bad Password!";
	return;
}

//Read messages
$file = fopen("log.txt", "r") or die("Unable to open file!");
$messages = fread($file, 10000);
fclose($file);

if (array_key_exists("m", $_GET)) {
	
	$message = htmlspecialchars($_GET["m"]); 

	//Trim if needed
	if ($message != "") {
		$messageArray = explode("\r\n", $messages);
		$count = count($messageArray);
		if ($count >= $limit) {
			$messages = "";
			for ($i = 1; $i < $count; $i++) {
				$messages .= $messageArray[$i] . "\r\n";
			}		
		} else {
			$messages .= "\r\n";
		}
	}

	//Append and write out
	$messages .= $message;

	$file = fopen("log.txt", "w") or die("Unable to open file!");
	fwrite($file, $messages);
	fclose($file);
}

if (array_key_exists("claim", $_GET)) {
	$file = fopen("log.txt", "w") or die("Unable to open file!");
	fwrite($file, "");
	fclose($file);
}

if (array_key_exists("connect", $_GET) || array_key_exists("claim", $_GET)) {
	echo "TTSSync: OK!\n";
}

echo $messages;

?>
