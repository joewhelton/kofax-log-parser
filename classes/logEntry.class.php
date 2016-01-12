<?php

class logEntry implements JsonSerializable {
    private $batchName;
    private $batchType;
    private $batchScanDate;
    private $batchRecognitionStartTime;
    private $batchRecognitionStopTime;
    private $avgPerPageRecognition;
    private $batchPDFDate;
    private $batchPDFStartTime;
    private $batchPDFStopTime;
    private $batchDocs;
    private $batchPages;
    private $avgPerPagePDF;
    private $batchExportStartTime;
    private $batchExportStopTime;
    private $avgExportPerPage;
	private $batchExportDate;

    public function __construct($batchName, $batchType, $batchScanDate, $batchRecognitionStartTime, $batchRecognitionStopTime, $batchPDFDate, $batchPDFStartTime, $batchPDFStopTime, $batchDocs, $batchPages, $batchExportStartTime, $batchExportStopTime, $batchExportDate ){
        $this->batchName = $batchName;
        $this->batchType = $batchType;
        $this->batchScanDate = $batchScanDate;
        $this->batchRecognitionStartTime = $batchRecognitionStartTime;
        $this->batchRecognitionStopTime = $batchRecognitionStopTime;
        $this->batchPDFDate = $batchPDFDate;
        $this->batchDocs = $batchDocs;
        $this->batchPages = $batchPages;
        $this->batchPDFStartTime = $batchPDFStartTime;
        $this->batchPDFStopTime = $batchPDFStopTime;
        $this->batchExportStartTime = $batchExportStartTime;
        $this->batchExportStopTime = $batchExportStopTime;
        $this->avgPerPagePDF = @$this->avgPerPage($this->batchPDFStartTime, $this->batchPDFStopTime);
        $this->avgPerPageRecognition = @$this->avgPerPage($this->batchRecognitionStartTime, $this->batchRecognitionStopTime);
        $this->avgExportPerPage = @$this->avgPerPage($this->batchExportStartTime, $this->batchExportStopTime);
        $this->batchExportDate = $batchExportDate;
    }
	
	public function getBatchName(){
		return $this->batchName;
	}
	
	public function getBatchScanDate(){
		return $this->batchScanDate;
	}

	public function getBatchPDFDate(){
		return $this->batchPDFDate;
	}
	
	public function getVars() {
        return get_object_vars($this);
    }

	public function jsonSerialize() {
		return $this->getVars();
    }

	private function avgPerPage($startTime, $stopTime){
		$seconds = abs(strtotime($stopTime) - strtotime($startTime));
        try{
            return $seconds / ($this->batchPages);
        } catch (exception $e){
            return "N/A";   //In case of divide by zero
        }
	}
}

?>