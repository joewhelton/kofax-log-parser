<?php
	class logfile{
		private $path;
		private $currentFilename;
        private $filesInPath = array();
		private $logEntries = array();
				
		public function __construct($path, $filename=false){
			$this->path = $path;
            $this->readFileList();
			if($filename!==false){
				$this->currentFilename = $filename;
			}
		}
		
		public function getPath(){
			return $this->path;
		}
		
		public function getCurrentFilename(){
			return $this->currentFilename;
		}
		
		public function setCurrentFilename($currentFilename){
			$this->currentFilename = $currentFilename;
		}
		
		public function getFilesInPath(){
			return $this->filesInPath;
		}
		
		public function getLogEntries(){
			return $this->logEntries;
		}

		public function readFileList(){
			$handler = opendir($this->path);
			while ($file = @readdir($handler)) {
				if (substr($file, 0, 3) == "log" ) {
                    $this->filesInPath[] = $file;
				}
			}
			@closedir($handler);
		}

        public function getLogEntriesByScanDate($date){
            $output = Array();
            foreach($this->logEntries as $obj){
                if($obj->getBatchScanDate() == $date){
                    $output[] = $obj;
                }
            }
            return $output;
        }

        public function getLogEntriesByPDFDate($date){
            $output = Array();
            foreach($this->logEntries as $obj){
                if($obj->getBatchPDFDate() == $date){
                    $output[] = $obj;
                }
            }
            return $output;
        }

        public function readAndParseLog(){
            if(!isset($this->currentFilename)){
                throw new Exception('Filename Not Set');
            } else {
                //Define regex strings -
                $nameRE = '#("01",")([0-9/]*)([0-9A-Z :]*)(",.*)#i';
                $typeRE = '#("04",")([a-z 0-9]*)(".*)#i';
                $pdfRE = '#("05",")(20[0-9-]*)(",")([0-9:]*)(","201.*",")([0-9:]*)(","PDF.*)#';
                $docPageRE ='#("03","[0-9]*","[0-9]*","[0-9]*","[0-9]*","[0-9]*",")([0-9]*)(",")([0-9]*)(.*)#';
                $recognitionRE = '#("05",")([0-9-]*)(",")([0-9:]*)(","[0-9-]*",")([0-9:]*)(","Recognition)#';
                $exportRE = '#("05",")([0-9-]*)(",")([0-9:]*)(","[0-9-]*",")([0-9:]*)(","Export)#';

                //Temp variables, will be passed to logEntry__construct()
                $batchName = "";
                $batchType = "";
                $batchScanDate = "";
                $batchRecognitionStartTime = "";
                $batchRecognitionStopTime = "";
                $batchPDFDate = "";
                $batchPDFStartTime = "";
                $batchPDFStopTime = "";
                $batchExportStartTime = "";
                $batchExportStopTime = "";
                $batchExportDate = "";
                $batchDocs = "";
                $batchPages = "";

                $file = fopen($this->path . "\\" . $this->currentFilename, "r");
                while(!@feof($file)){

                    $line = mb_convert_encoding(trim(@fgets($file)), "UTF-8", "UCS-2LE");
                    if (preg_match($nameRE, $line, $match)){
                        $batchName = $match[2] . $match[3];
                        $batchScanDate = $match[2];
                        do{
                            $line = mb_convert_encoding(trim(fgets($file)), "UTF-8", "UCS-2LE");
                            if (preg_match($typeRE, $line, $match2)){
                                $batchType = $match2[2];
                            }
                            if (preg_match($docPageRE, $line, $match2)){
                                $batchDocs = $match2[2];
                                $batchPages = $match2[4];
                            }
                            if (preg_match($recognitionRE, $line, $match2)){
                                $batchRecognitionStartTime = $match2[4];
                                $batchRecognitionStopTime = $match2[6];
                            }
                            if (preg_match($pdfRE, $line, $match2)){
                                $batchPDFDate = $match2[2];
                                $batchPDFStartTime = $match2[4];
                                $batchPDFStopTime = $match2[6];
                            }
                            if (preg_match($exportRE, $line, $match2)){
                                $batchExportDate = $match2[2];
                                $batchExportStartTime = $match2[4];
                                $batchExportStopTime = $match2[6];
                                break;
                            }
                        } while(strlen($line) > 5);
                    }
                    $logEntry = new logEntry($batchName, $batchType, implode("/", array_reverse(explode("/", $batchScanDate))), $batchRecognitionStartTime, $batchRecognitionStopTime, preg_replace('@-@', '/', $batchPDFDate), $batchPDFStartTime, $batchPDFStopTime, $batchDocs, $batchPages, $batchExportStartTime, $batchExportStopTime, preg_replace('@-@', '/', $batchExportDate));
                    if(($logEntry->getBatchName() != "") && ($logEntry != end($this->logEntries))){   //Stops empty and duplicate results
                        $this->logEntries[] = $logEntry;
                        $batchName = "";
                        $batchType = "";
                        $batchScanDate = "";
                        $batchRecognitionStartTime = "";
                        $batchRecognitionStopTime = "";
                        $batchPDFDate = "";
                        $batchPDFStartTime = "";
                        $batchPDFStopTime = "";
                        $batchDocs = "";
                        $batchPages = "";
                        $batchExportStartTime = "";
                        $batchExportStopTime = "";
                        $batchExportDate = "";
                    }
                }
                fclose($file);
            }
        }
	}
?>