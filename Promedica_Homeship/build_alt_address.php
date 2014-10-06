<?php
include_once 'Iseries_Connect.php';
include_once 'logging.php';
include_once 'Patient.php';
include_once 'AccountDetails.php';
include_once 'AccountUtilities.php';

date_default_timezone_set('America/New_York');

$log = new Logging();
$log->lfile('/home/BrianB/promedlog2.log');

$ctr = 0;

$hs13_address = "5520 Monroe St";
$hs13_city    = "Sylvania";
$hs13_state   = "OH";
$hs13_zip     = "43560";

$hs14_address = "735 S Shoop Ave";
$hs14_city    = "Wauseon";
$hs14_state   = "OH";
$hs14_zip     = "43567";

$hs41_address = "1946 North 13th Street";
$hs41_city    = "Toledo";
$hs41_state   = "OH";
$hs41_zip     = "43604";

$hs99_address = "770 Riverside Ave Suite 102";
$hs99_city    = "Adrian";
$hs99_state   = "MI";
$hs99_zip     = "49221";

$hs210_address = "601 Parkway Dr";
$hs210_city    = "Fostoria";
$hs210_state   = "OH";
$hs210_zip     = "44830";

$currdate2 = date('Y-m-d');
$currtime2 = date('h.i.s');

$iseries = new Iseries_Connect();
$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');

$sql = "select * from briantest.missingadr";

$result = db2_exec($conn,$sql);
if($result){
	while($row=db2_fetch_assoc($result)){
		
		if(trim($row['RVCCTU']) == 'PHP'){
			$log->lwrite("Going into process.");
		if($row['RVCUST'] == "155HS13"){
			$billto = $row['RVCUST'];
			$patientid = $row['RVSHIP'];
			$patient_name = $row['RVNAME'];
			$F_address = "5520 Monroe St";
			$F_city = "Sylvania";
			$F_state = "OH";
			$F_zip = "43560"; 			
		}else if($row['RVCUST'] == "155HS14"){
			$billto = $row['RVCUST'];
			$patientid = $row['RVSHIP'];
			$patient_name = $row['RVNAME'];
			$F_address = "735 S Shoop Ave";
			$F_city = "Wauseon";
			$F_state = "OH";
			$F_zip = "43567";
		}else if($row['RVCUST'] == "155HS41"){
			$billto = $row['RVCUST'];
			$patientid = $row['RVSHIP'];
			$patient_name = $row['RVNAME'];
			$F_address = "1946 North 13th Street";
			$F_city = "Toledo";
			$F_state = "OH";
			$F_zip = "43604";
		}else if($row['RVCUST'] == "155HS99"){
			$billto = $row['RVCUST'];
			$patientid = $row['RVSHIP'];
			$patient_name = $row['RVNAME'];
			$F_address = "770 Riverside Ave Suite 102";
			$F_city = "Adrian";
			$F_state = "MI";
			$F_zip = "49221";
		}else if($row['RVCUST'] == "155H210"){
			$billto = $row['RVCUST'];
			$patientid = $row['RVSHIP'];
			$patient_name = $row['RVNAME'];
			$F_address = "601 Parkway Dr";
			$F_city = "Fostoria";
			$F_state = "OH";
			$F_zip = "44830";
		}
		$vebins = "insert into S2KCEE.VEBCUSHIP (COMPANY_NBR,CUSTOMER_NBR,SHIPTO_NBR,NAME,ADDRESS1,ADDRESS2,ADDRESS3,
					CITY,STATE,ZIPCODE,COUNTRY,PHONE,EMAIL,CREATED_BY,CREATED_DATE) values (
					1,'".$billto."','".$patientid."','".$patient_name."','".
							$F_address."',' ',' ','".$F_city."','".$F_state."','".$F_zip.
							"','USA','','','PHP','".$currdate2."')";
		$vebrs2 = db2_exec($conn,$vebins);
		$log->lwrite($vebins);
		if($vebrs2){
			$log->lwrite("Patient ".$patientid." added.");
		}else{
			$log->lwrite("Failed to write VEBCUSHIP");
			$log->lwrite(db2_stmt_errormsg());
		}
		}else{
			$log->lwrite("Failed to run process.");
		}
	}//Close WHILE loop
}

		
db2_close($conn);
$log->lclose();
?>