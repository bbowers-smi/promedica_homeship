<?php
include_once 'Patient.php';
include_once 'logging.php';
include_once 'Iseries_Connect.php';

date_default_timezone_set('America/New_York');

set_time_limit(45);
//Set library variable here


$log = new Logging();
$log->lfile('/home/sftppro/logs/promedlog.log');

$promedicadir = opendir("/home/sftppro/inbound");


if($promedicadir){

while($file = readdir($promedicadir)){
	$ctr = 0;
	if($file != "." && $file != ".." && substr($file,-3) == "txt"){
		
	if($handler = fopen('/home/sftppro/inbound/'.$file,"r")){
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
	foreach($patients as $row){
	
		$patientinfo = new Patient();

		$facid = substr($row[9],0,3);
			
		$patientinfo->setPatient_status($row[0]);
		$patientinfo->setPatientid($row[1]);
		$patientinfo->setP_firstname($row[2]);
		$patientinfo->setP_lastname($row[3]);
		$patientinfo->setP_address1($row[4]);
		$patientinfo->setP_address2($row[5]);
		$patientinfo->setP_city($row[6]);
		$patientinfo->setP_state($row[7]);
		$patientinfo->setP_zip($row[8]);
		$patientinfo->setFacility_name($row[9]);
		$patientinfo->setF_address1($row[10]);
		$patientinfo->setF_address2($row[11]);
		$patientinfo->setF_city($row[12]);
		$patientinfo->setF_state($row[13]);
	
		$patientrecord[$row[1]] = $patientinfo;
	
	}
	$iseries = new Iseries_Connect();
	$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
	foreach($patientrecord as $row){
		$patient = new Patient();
		$patient = $row;
		$dateadded = date('Y-m-d');
		$insert_sql = "insert into R50MODSDTA.ARCPHSSRC (EPISODEID,PATSTAT,PFIRST,PLAST,PADDR1,PADDR2,
				PCITY,PSTATE,PZIPCOD,FACILITY,FACADD1,FACADD2,FACCIT,FACSTA,DATADD)
				 VALUES('".$patient->getPatientid()."','".$patient->getPatient_status()."','"
				 		.$patient->getP_firstname()."','".$patient->getP_lastname()."','".$patient->getP_address1().
					"','".$patient->getP_address2()."','".$patient->getP_city()."','".$patient->getP_state()
					."','".$patient->getP_zip()."','".$patient->getFacility_name()."','".$patient->getF_address1()
					."','".$patient->getF_address2()."','".$patient->getF_city()."','".$patient->getF_state()."','"
					.$dateadded."')";
		$result = db2_exec($conn,$insert_sql);
		
		if(!$result){
			$log->lwrite(db2_stmt_errormsg());
			$log->lwrite("SQL: ".$insert_sql);
		}
	}
	unset($patients);
	unset($patientrecord);
}
}else{
	$log->lwrite("Didn't get directory handle");
}


db2_close($conn);

$log->lclose();

?>