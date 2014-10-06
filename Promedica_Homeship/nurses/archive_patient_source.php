<?php
include_once 'Patient.php';
include_once 'logging.php';
include_once 'Iseries_Connect.php';
include_once 'AccountDetails.php';
include_once 'AccountUtilities.php';

date_default_timezone_set('America/New_York');

set_time_limit(45);
//Set library variable here


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
		
	if($handler = fopen('/home/sftppro/archives/'.$file,"r")){
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
		if($row[0] == "D"){
			continue;
		}
		$facid = substr($row[9],0,3);
		$billto = $acctutils->getAcctId($facid);
	
		$addr_vals = $acctutils->getOfficeAddress($billto);
	
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
		$patientinfo->setF_address1($addr_vals['RVADD1']);
		$patientinfo->setF_address2($addr_vals['RVADD2']);
		$patientinfo->setF_city($addr_vals['RVCITY']);
		$patientinfo->setF_state($addr_vals['RVST1']);
	
	
		$patientinfo->setPatient_billto($billto);
		$patientinfo->setF_zip($addr_vals['RVMZIP']);
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
				 VALUES('".$patientinfo->getPatientid()."','".$patientinfo->getPatient_status()."','"
				 		.$patientinfo->getP_firstname()."','".$patientinfo->getP_lastname()."','".$patientinfo->getP_address1().
					"','".$patientinfo->getP_address2()."','".$patientinfo->getP_city()."','".$patientinfo->getP_state()
					."','".$patientinfo->getP_zip()."','".$patientinfo->getFacility_name()."','".$patientinfo->getF_address1()
					."','".$patientinfo->getF_address2()."','".$patientinfo->getF_city()."','".$patientinfo->getF_state()
					."','".$patientinfo->getF_zip()."',".$dateadded;
		$result = db2_exec($conn,$insert_sql);
		
		if(!$result){
			$log->lwrite(db2_stmt_errormsg());
			$log->lwrite("SQL: ".$insert_sql);
		}
	}
	
}
}else{
	$log->lwrite("Didn't get directory handle");
}


db2_close($conn);

$log->lclose();

?>