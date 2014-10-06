<?php
include_once 'Patient.php';
include_once 'logging.php';
include_once 'Iseries_Connect.php';
include_once 'AccountDetails.php';
include_once 'AccountUtilities.php';

date_default_timezone_set('America/New_York');

set_time_limit(45);
//Set library variable here
$lib = "R50FILES";
$lib2 = "R50MODSDTA";
$weblib = "S2KCEE"; 

/* $lib = "BRIANTEST";
$lib2 = "BRIANTEST";
$weblib = "BRIANTEST"; */

$currdate = date('Ymd');
$currtime = strftime('%l%M%S',time());
$currbillto = "";

$log = new Logging();
$log->lfile('/home/sftppro/logs/promedlog.log');

$promedicadir = opendir("/home/sftppro/inbound");
$ctr = 0;

if($promedicadir){

while($file = readdir($promedicadir)){
	if($file != "." && $file != ".." && substr($file,-3) == "txt"){
	
		copy('/home/sftppro/inbound/'.$file,'/home/sftppro/archives/'.$file);
		
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
	if(trim($facid) == "41"){
		$patientinfo->setF_address1("VISITING NURSE SERVICE");
	}else{
		$patientinfo->setF_address1($addr_vals['RVADD1']);
	}
	$patientinfo->setF_address2($addr_vals['RVADD2']);
	$patientinfo->setF_city($addr_vals['RVCITY']);
	$patientinfo->setF_state($addr_vals['RVST1']);
	
	
	$patientinfo->setPatient_billto($billto);
	$patientinfo->setF_zip($addr_vals['RVMZIP']);
	$patientrecord[$row[1]] = $patientinfo;

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
	
	if($patient->getPatient_status() != "D"){
	if($currbillto == "" && $currbillto != $patient->getPatient_billto()){
		$currbillto = $patient->getPatient_billto();
		list($handling,$minordamt,$handlingfee,$freight,$allowbo) = $acctutils->getShiptoUsrDefined($patient->getPatient_billto());
	}else{
		list($handling,$minordamt,$handlingfee,$freight,$allowbo) = $acctutils->getShiptoUsrDefined($patient->getPatient_billto());
		$currbillto = $patient->getPatient_billto();
	}
	}//Do this only if not discharged patient
	
	$patient_name = $patient->getP_firstname()." ".$patient->getP_lastname();
	
	$sql = "select * from ".$lib.".varship where rvcmp=1 and rvcust='".$patient->getPatient_billto()."' and rvship='".$patient->getPatientid()."'";
	
	
		$result = db2_exec($conn,$sql);
		
			
		if(db2_fetch_row($result)){
			$sqlToExecute = "update ".$lib.".VARSHIP set RVDEL='".$patient->getPatient_status()."',RVNAME='".$patient_name."',RVADD1='".$patient->getP_address1()."',RVADD2='".$patient->getP_address2()."',RVADD3='',RVCITY='".$patient->getP_city()."',RVST1='".$patient->getP_state()."',RVMZIP='".$patient->getP_zip()."',RVCNTR='USA',RVMFON='',RVEMAL='',RVUSLC='PHP',RVLCGD='".$currdate."',RVLCGT='".$currtime."' where RVCMP=1 and RVCUST='".$patient->getPatient_billto()."' and RVSHIP='".$patient->getPatientid()."'";
			
			$updaters = db2_exec($insconn,$sqlToExecute);
			if(!$updaters){
					
				$log->lwrite(db2_stmt_errormsg());
				$log->lwrite("Update failed for ".$patient->getPatientid());
				$log->lwrite("SQL: ".$sqlToExecute);
			}
		}else{
			
			$routes = $acct->getRoutes();
			$stops = $acct->getStops();
			list($statetax,$countytax,$shiptocounty) = $acctutils->getBaseShiptoVal($patient->getPatient_billto());
			
			$inssql = "insert into ".$lib.".varship (RVDEL,RVCMP,RVTYPE,RVCUST,RVSHIP,RVNAME,RVADD1,RVADD2,RVADD3,RVCITY,
					RVST1,RVMZIP,RVCNTR,RVMFON,RVEMAL,RVCCTU,RVCCTD,RVCCTT,RVBRNO,RVSCTC,
					RVMMOD,RVSMNO,RVCOMA,RVSMNB,RVCOMB,RVBCST,RVBTST,RVODSC,RVCPRI,RVCHFR,
					RVSVIA,RVSHTR,RVFOB,RVDLOC,RVROUT,RVSTOP,RVTXST,RVTXCN,RVCNTY,RVTXL1,
					RVLOC1,RVTXL2,RVLOC2,RVMAIL,RVDSTC,RVEDIN,RVSIDM,RVSRSQ,RVTRTM,RVTTQ,
					RVCOMM,RVWHSE,RVCLOC,RVLCGD,RVLCGT,RVUSLC,RVLMTD,RVLMTT,RVLMTU,RVCMPL,
					RVACES#,RVGLNT,RVROUT1,RVSTOP1,RVROUT2,RVSTOP2,RVROUT3,RVSTOP3,RVROUT4,RVSTOP4,
					RVROUT5,RVSTOP5,RVROUT6,RVSTOP6,RVROUT7,RVSTOP7,RVPCLS,RVPGRP) values
					('A',1,'SH','".$patient->getPatient_billto()."','".$patient->getPatientid()."','".$patient_name."','".$patient->getP_address1()."','".$patient->getP_address2()."','','".$patient->getP_city()."','".$patient->getP_state()."','".$patient->getP_zip()."','USA'
					,'9999999999','','PHP','".$currdate."',$currtime,0,'',0,".$acct->getSalesmanNumber().",0,0,0,'".$patient->getPatient_billto()."','',0,0,'".$acct->getChargeFreight()."',
					'".substr($acct->getShipViaCode(),0,4)."','".$acct->getShippingTerms()."','".$acct->getFobPoint()."','".$acct->getDftInvLoc()."','',0,'".$statetax."','".$countytax."',".$shiptocounty.",'',0,'',0,'','','','','',0,'','',0,'','".$currdate."',".$currtime.",'PHP',0,0,'',
					'N',0,'','".$routes[1]."',".$stops[1].",'".$routes[2]."',".$stops[2].",'".$routes[3]."',".$stops[3].",'".$routes[4]."',".$stops[4].",'".$routes[5]."',".$stops[5].",'".$routes[6]."',".$stops[6].",'".$routes[7]."',".$stops[7].",0,'')";
		
		//$insstmt = db2_prepare($conn,$inssql);
		
		$result2 = db2_exec($insconn,$inssql);
		if(!$result2){
			
			$log->lwrite(db2_stmt_errormsg());
			$log->lwrite("SQL: ".$inssql);
		}else{
		$vebsql = "select * from ".$weblib.".vebcuship where company_nbr = 1 and customer_nbr = '".$patient->getPatient_billto()."' and shipto_nbr = '".$patient->getPatientid()."'";
		$vebrs = db2_exec($insconn,$vebsql);
		$currdate2 = date('Y-m-d');
		$currtime2 = date('h.i.s');
		if(!db2_fetch_assoc($vebrs)){
			$vebins = "insert into ".$weblib.".VEBCUSHIP (COMPANY_NBR,CUSTOMER_NBR,SHIPTO_NBR,NAME,ADDRESS1,ADDRESS2,ADDRESS3,
					CITY,STATE,ZIPCODE,COUNTRY,PHONE,EMAIL,CREATED_BY,CREATED_DATE) values (
					1,'".$patient->getPatient_billto()."','".$patient->getPatientid()."','".$patient_name."','".
					$patient->getF_address1()."','".$patient->getF_address2()."',' ','".$patient->getF_city()."','".$patient->getF_state()."','".$patient->getF_zip().
					"','USA','9999999999','','PHP','".$currdate2."')";
		$vebrs2 = db2_exec($insconn,$vebins);
		
		if(!$vebrs2){
			$log->lwrite("Failed to write new VEBCUSHIP record.");
			$log->lwrite(db2_stmt_errormsg());
		}
		}
		
		
		$bld_ship_sql = "insert into ".$lib2.".svshpudef (SVCMP,SVCUST,SVSHIP,SVCCTD,SVCCTT,
				SVCCTU,SVMHCH,SVMOR$,SVHFEE,SVFRML,SVALBO) values(1,'".$patient->getPatient_billto()."','".$patient->getPatientid()."',
						'".$currdate."',0,'PHP','".$handling."',".$minordamt.",".$handlingfee.",'".$freight."','".$allowbo."')";
		$result3 = db2_exec($insconn,$bld_ship_sql);
		
		
		if(!$result3){
				
			$log->lwrite(db2_stmt_errormsg());
			$log->lwrite("SQL: ".$bld_ship_sql);
		}
		}
		}
}
db2_close($conn);
db2_close($insconn);

$log->lclose();

?>