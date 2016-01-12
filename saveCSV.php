<?php
    spl_autoload_register(function ($class) {
        include 'classes/' . $class . '.class.php';
    });

    $path = (!isset($_REQUEST['path']) ? '\\\\NDLS018\\CaptureSV\\Logs' : $_REQUEST['path']);
    $logfile = new logfile($path);
	$latestFilename = $logfile->getFilesInPath();
    $logfile->setCurrentFilename(end($latestFilename));
    $logfile->readAndParseLog();
    $subset = $logfile->getLogEntriesByPDFDate(date("Y/m/d", strtotime("yesterday")));
	
	$handle = fopen((dirname( realpath( __FILE__ ) ) . DIRECTORY_SEPARATOR . "reports" . DIRECTORY_SEPARATOR . "Output Log " . date("Y-m-d", strtotime("yesterday")) . ".csv"), "x+");
    fwrite($handle, implode(',', array_keys($subset[0]->getVars())) . PHP_EOL);
    foreach($subset as $obj){
        $line = Array();
        foreach($obj->getVars() as $key=>$value){
            $line[] = $value;
        }
        fwrite($handle,implode(',', $line) . PHP_EOL);
    }
    fclose($handle);
?>