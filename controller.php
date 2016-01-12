<?php
	spl_autoload_register(function ($class) {
		include 'classes/' . $class . '.class.php';
	});

    $json_response = "";
    $string_response = "";
    $array_response = array();

    //Set defaults for any missing querystring parameters
	$path = (!isset($_REQUEST['path']) ? '\\\\NDLS018\\CaptureSV\\Logs' : $_REQUEST['path']);
	$filename = (!isset($_REQUEST['filename']) ? "" : $_REQUEST['filename']);
	$action = (!isset($_REQUEST['action']) ? 'listfiles' : $_REQUEST['action']);

    //debugging parameters
    /*$action = 'getlog';
    $filename = 'log_1408_ext.txt';
    $path = '\\\\NDLS018\\CaptureSV\\Logs';*/

	if ($filename == ""){
		$logfile = new logfile($path);
	} else {
		$logfile = new logfile($path, $filename);
	}
	
	switch($action){
	case "listfiles":
		$json_response = array_reverse($logfile->getFilesInPath());
		break;
	case "getpath":
		$string_response = $logfile->getPath();
		break;
	case "getlog":
		try {
			$logfile->readAndParseLog();
			$json_response = $logfile->getLogEntries();
		} catch (Exception $e){
			$string_response = $e->getMessage();
		}
		break;
	default:
		$string_response = "Unknown Action Specified";
	}

    if($json_response != ""){
        header('Content-Type: application/json');
	    echo json_encode($json_response);//, JSON_PRETTY_PRINT);    //Uncomment for FE dubugging
    } else if ($string_response != ""){
        echo $string_response;
    } else {
        print_r($array_response);
    }
	
?>
