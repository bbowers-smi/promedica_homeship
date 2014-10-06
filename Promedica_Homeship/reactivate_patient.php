<?php
include_once 'Patient.php';
include_once 'logging.php';
include_once 'Iseries_Connect.php';
include_once 'AccountUtilities.php';

date_default_timezone_set('America/New_York');

set_time_limit(45);
//Set library variable here
$lib = "R50FILES";//R50FILES
$lib2 = "R50MODSDTA";//R50MODSDTA
$weblib = "S2KCEE";//S2KCEE

$currdate = date('Ymd');
$currtime = strftime('%l%M%S',time());
$currbillto = "";

$log = new Logging();
$log->lfile('/home/sftppro/logs/promedlog.log');

$promedicadir = opendir("/home/sftppro/archives");
$ctr = 0;

if($promedicadir){

while($file = readdir($promedicadir)){
	if($file != "." && $file != ".." && substr($file,-3) == "txt"){
		
	if($handler = fopen('/home/sftppro/temp/'.$file,"r")){
		
	while($data = fgetcsv($handler)){
			if($ctr > 0){
			$patients[$ctr]=$data;
			$ctr+=1;
			}else{
				$ctr+=1;
			}
					
	}
	}else{
		$log->lwrite("Failed to open file ".$file);
	}
	}
	
}
}else{
	$log->lwrite("Didn't get directory handle");
}

$acctutils = new AccountUtilities();

foreach($patients as $row){
	
	$patientinfo = new Patient();
	if($row[0] !== "D"){
		continue;
	}
	$patientinfo->setPatient_status($row[0]);
	$patientinfo->setPatientid($row[1]);
	
	$facid = substr($row[9],0,3);
	
	$patientinfo->setPatient_billto($acctutils->getAcctId($facid));
	
	$patientrecord[$row[1]] = $patientinfo;

}

//Insert new Shipto records
$iseries = new Iseries_Connect();
$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');

$log->lwrite("Using lib ".$lib);
	foreach($patientrecord as $row){
	$patient = new Patient();
	$patient = $row;

	
	if($patient->getPatient_status() == "D"){
				
			$sqlToExecute = "update ".$lib.".VARSHIP set RVDEL='A' where RVCMP=1 and RVCUST like '155H%' and RVSHIP='".$patient->getPatientid()."'";
			
			$updaters = db2_exec($conn,$sqlToExecute);
			if(!$updaters){
					
				$log->lwrite(db2_stmt_errormsg());
				$log->lwrite("Update failed for ".$patient->getPatientid());
				$log->lwrite("SQL: ".$sqlToExecute);
			}
		}
}
db2_close($conn);

$log->lclose();

?>