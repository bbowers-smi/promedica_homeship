<?php
include_once 'Iseries_Connect.php';
include_once 'logging.php';
include_once 'Patient.php';
include_once 'AccountDetails.php';
include_once 'AccountUtilities.php';

date_default_timezone_set('America/New_York');

$log = new Logging();
$log->lfile('/home/sftppro/logs/promedlog2.log');

$weblib = "S2KCEE";
$ctr = 0;

$promedicadir = opendir("/home/sftppro/inbound");

if($promedicadir){
	while($file = readdir($promedicadir)){
		if($file != "." && $file != ".." && substr($file,-3) == "txt"){
			if($handler = fopen('/home/sftppro/inbound/'.$file,"r")){
				while($data = fgetcsv($handler)){
					if($ctr > 0){
						$patients[$ctr]=$data;
						$ctr+=1;
					}else{
						$ctr+=1;
					}
						
				}//Close WHILE loop read of file
			}else{
				$log->lwrite("Failed to open file ".$file);
			}
		}//Close IF check for coorect file type
	}//Close WHILE loop
	
}//Close IF check for dir handle
$acctutils = new AccountUtilities();

foreach($patients as $row){

	$patientinfo = new Patient();
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
	$patientinfo->setF_address($row[10]);

	$patientinfo->setF_city($row[12]);
	$patientinfo->setF_state($row[13]);
	$facid = substr($row[9],0,3);

	$patientinfo->setPatient_billto($acctutils->getAcctId($facid));

	$patientrecord[$row[1]] = $patientinfo;

}
$iseries = new Iseries_Connect();
$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');

foreach($patientrecord as $row){
	$patient = new Patient();
	$patient = $row;
	$patient_name = $patient->getP_firstname()." ".$patient->getP_lastname();
	$vebsql = "select * from ".$weblib.".vebcuship where company_nbr = 1 and customer_nbr = '".$patient->getPatient_billto()."' and shipto_nbr = '".$patient->getPatientid()."'";
	
	$vebrs = db2_exec($conn,$vebsql);
	$currdate2 = date('Y-m-d');
	$currtime2 = date('h.i.s');
	if(!db2_fetch_assoc($vebrs)){
		$vebins = "insert into ".$weblib.".VEBCUSHIP (COMPANY_NBR,CUSTOMER_NBR,SHIPTO_NBR,NAME,ADDRESS1,ADDRESS2,ADDRESS3,
					CITY,STATE,ZIPCODE,COUNTRY,PHONE,EMAIL,CREATED_BY,CREATED_DATE) values (
					1,'".$patient->getPatient_billto()."','".$patient->getPatientid()."','".$patient_name."','".
						$patient->getF_address()."',' ',' ','".$patient->getF_city()."','".$patient->getF_state()."','".$patient->getF_zip().
						"','USA','','','PHP','".$currdate2."')";
		$vebrs2 = db2_exec($conn,$vebins);
	}
}
db2_close($conn);
$log->lclose();
?>