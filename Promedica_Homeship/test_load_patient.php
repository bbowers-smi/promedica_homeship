<?php
include_once 'Patient.php';
include_once 'logging.php';
include_once 'Iseries_Connect.php';
include_once 'AccountDetails.php';
include_once 'AccountUtilities.php';

date_default_timezone_set('America/New_York');

//Set library variable here
$lib = "briantest";

$currdate = date('Ymd');
$currtime = strftime('%l%M%S',time());

$log = new Logging();
$log->lfile('/home/sftppro/logs/promedlog.log');

$promedicadir = opendir("/home/sftppro/inbound");
$ctr = 0;
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
	$patientinfo->setPatientid($row[0]);
	$patientinfo->setP_firstname($row[1]);
	$patientinfo->setP_lastname($row[2]);
	$patientinfo->setP_address($row[3]);
	$patientinfo->setP_city($row[4]);
	$patientinfo->setP_state($row[5]);
	$patientinfo->setP_zip($row[6]);
	$patientinfo->setFacility_name($row[7]);
	$patientinfo->setF_address($row[8]);
	$patientinfo->setF_city($row[9]);
	$patientinfo->setF_state($row[10]);
	$patientinfo->setF_zip($row[11]);
	$facid = substr($row[7],0,3);
	
	$patientinfo->setPatient_billto($acctutils->getAcctId($facid));
	
	$patientrecord[$row[0]] = $patientinfo;
}

//Insert new Shipto records
 $iseries = new Iseries_Connect();
$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
$insconn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');


	
	foreach($patientrecord as $row){
	$patient = new Patient();
	$patient = $row;
	
	$acct = new AccountDetails();
	$acct = $acctutils->getAccountInfo($patient->getPatient_billto());
	
	$sql = "select * from ".$lib.".varship where rvcmp=1 and rvcust='".$patient->getPatient_billto()."' and rvship='".$patient->getPatientid()."'";
	
	$stmt = db2_prepare($conn,$sql);
	if($stmt){
		$result = db2_execute($stmt);
		if(db2_fetch_row($stmt)){
			echo "Found a shipto record";
		}else{
			$patient_name = $patient->getP_firstname()." ".$patient->getP_lastname();
			$routes = $acct->getRoutes();
			$stops = $acct->getStops();
			
			$inssql = "insert into ".$lib.".varship (RVDEL,RVCMP,RVTYPE,RVCUST,RVSHIP,RVNAME,RVADD1,RVADD2,RVADD3,RVCITY,
					RVST1,RVMZIP,RVCNTR,RVMFON,RVEMAL,RVCCTU,RVCCTD,RVCCTT,RVBRNO,RVSCTC,
					RVMMOD,RVSMNO,RVCOMA,RVSMNB,RVCOMB,RVBCST,RVBTST,RVODSC,RVCPRI,RVCHFR,
					RVSVIA,RVSHTR,RVFOB,RVDLOC,RVROUT,RVSTOP,RVTXST,RVTXCN,RVCNTY,RVTXL1,
					RVLOC1,RVTXL2,RVLOC2,RVMAIL,RVDSTC,RVEDIN,RVSIDM,RVSRSQ,RVTRTM,RVTTQ,
					RVCOMM,RVWHSE,RVCLOC,RVLCGD,RVLCGT,RVUSLC,RVLMTD,RVLMTT,RVLMTU,RVCMPL,
					RVACES#,RVGLNT,RVROUT1,RVSTOP1,RVROUT2,RVSTOP2,RVROUT3,RVSTOP3,RVROUT4,RVSTOP4,
					RVROUT5,RVSTOP5,RVROUT6,RVSTOP6,RVROUT7,RVSTOP7,RVPCLS,RVPGRP) values
					('A',1,'SH','".$patient->getPatient_billto()."',".$patient->getPatientid().",'".$patient_name."','".$patient->getP_address()."','','','".$patient->getP_city()."','".$patient->getP_state()."','".$patient->getP_zip()."','USA'
					,'','','PHP','".$currdate."',$currtime,0,'',0,".$acct->getSalesmanNumber().",0,0,0,'','',0,0,'".$acct->getChargeFreight()."',
					'".substr($acct->getShipViaCode(),0,4)."','".$acct->getShippingTerms()."','".$acct->getFobPoint()."','".$acct->getDftInvLoc()."','',0,'Y','Y',0,'',0,'',0,'','','','','',0,'','',0,'','".$currdate."',".$currtime.",'PHP',0,0,'',
					'N',0,'','".$routes[1]."',".$stops[1].",'".$routes[2]."',".$stops[2].",'".$routes[3]."',".$stops[3].",'".$routes[4]."',".$stops[4].",'".$routes[5]."',".$stops[5].",'".$routes[6]."',".$stops[6].",'".$routes[7]."',".$stops[7].",0,'')";
			$result2 = db2_exec($insconn,$inssql);
			if($result2){
				$log->lwrite("Record should have been written.");
			}else{
				$log->lwrite(db2_stmt_errormsg());
				$log->lwrite("Address length: ".strlen($patient->getP_address()));
				$log->lwrite("SQL: ".$inssql);
			}
		}
	}
} 
db2_close($conn);
db2_close($insconn);
//}
$log->lclose();
echo "Insert is done.";


?>