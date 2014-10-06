<?php
include_once 'Nurses.php';
include_once '../logging.php';
include_once '../Iseries_Connect.php';
include_once '../AccountDetails.php';
include_once '../AccountUtilities.php';

date_default_timezone_set('America/New_York');

set_time_limit(45);
//Set library variable here
/* $lib = "R50FILES";
$lib2 = "R50MODSDTA";
$weblib = "S2KCEE"; */ 

$lib = "BRIANTEST";
$lib2 = "BRIANTEST";
$weblib = "BRIANTEST"; 

$currdate = date('Ymd');
$currtime = strftime('%l%M%S',time());
$currbillto = "";

$log = new Logging();
$log->lfile('/home/sftppro/logs/nurseslog.log');

$promedicadir = opendir("/home/sftppro/nurses");
$ctr = 0;

if($promedicadir){

while($file = readdir($promedicadir)){
	if($file != "." && $file != ".." && substr($file,-3) == "csv"){
	
		copy('/home/sftppro/nurses/'.$file,'/home/sftppro/archives/'.$file);
		
	if($handler = fopen('/home/sftppro/nurses/'.$file,"r")){
	while($data = fgetcsv($handler)){
			
			$nurses[$ctr]=$data;
			$ctr+=1;
					
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
$nurseID = "66";
foreach($nurses as $row){
	
	$nurseinfo = new Nurses();
	
	$billto = "155HS13";
	
	$addr_vals = $acctutils->getOfficeAddress($billto);
	
	$nurseinfo->setNurseid($nurseID);
	list($lastn,$firstn) = explode(',',$row[0]);
	$nurseinfo->setN_firstname($firstn);
	$nurseinfo->setN_lastname($lastn);
	$nurseinfo->setN_address1(substr($row[1],0,29));
	$nurseinfo->setN_address2("");
	$nurseinfo->setN_city($row[2]);
	$nurseinfo->setN_state($row[3]);
	$nurseinfo->setN_zip($row[4]);
	$nurseinfo->setN_phone($row[5]);
	$nurseinfo->setFacility_name("ProMedica -Home Care");
	$nurseinfo->setF_address1($addr_vals['RVADD1']);
	$nurseinfo->setF_address2($addr_vals['RVADD2']);
	$nurseinfo->setF_city($addr_vals['RVCITY']);
	$nurseinfo->setF_state($addr_vals['RVST1']);
	
	
	$nurseinfo->setNurse_billto($billto);
	$nurseinfo->setF_zip($addr_vals['RVMZIP']);
	$nurserecord[$nurseID] = $nurseinfo;
	$nurseID++;

}

//Insert new Shipto records
$iseries = new Iseries_Connect();
$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
$insconn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');


	foreach($nurserecord as $row){
	$nurse = new Nurses();
	$nurse = $row;
	
	$acct = new AccountDetails();
	$acct = $acctutils->getAccountInfo($nurse->getNurse_billto());
	
	list($handling,$minordamt,$handlingfee,$freight,$allowbo) = $acctutils->getShiptoUsrDefined($nurse->getNurse_billto());
	
	$nurse_name = $nurse->getN_firstname()." ".$nurse->getN_lastname();
	$log->lwrite("Nurse: ".$nurse_name);
			$routes = $acct->getRoutes();
			$stops = $acct->getStops();
			list($statetax,$countytax,$shiptocounty) = $acctutils->getBaseShiptoVal($nurse->getNurse_billto());
			
			$inssql = "insert into ".$lib.".varship (RVDEL,RVCMP,RVTYPE,RVCUST,RVSHIP,RVNAME,RVADD1,RVADD2,RVADD3,RVCITY,
					RVST1,RVMZIP,RVCNTR,RVMFON,RVEMAL,RVCCTU,RVCCTD,RVCCTT,RVBRNO,RVSCTC,
					RVMMOD,RVSMNO,RVCOMA,RVSMNB,RVCOMB,RVBCST,RVBTST,RVODSC,RVCPRI,RVCHFR,
					RVSVIA,RVSHTR,RVFOB,RVDLOC,RVROUT,RVSTOP,RVTXST,RVTXCN,RVCNTY,RVTXL1,
					RVLOC1,RVTXL2,RVLOC2,RVMAIL,RVDSTC,RVEDIN,RVSIDM,RVSRSQ,RVTRTM,RVTTQ,
					RVCOMM,RVWHSE,RVCLOC,RVLCGD,RVLCGT,RVUSLC,RVLMTD,RVLMTT,RVLMTU,RVCMPL,
					RVACES#,RVGLNT,RVROUT1,RVSTOP1,RVROUT2,RVSTOP2,RVROUT3,RVSTOP3,RVROUT4,RVSTOP4,
					RVROUT5,RVSTOP5,RVROUT6,RVSTOP6,RVROUT7,RVSTOP7,RVPCLS,RVPGRP) values
					('A',1,'SH','".$nurse->getNurse_billto()."','".$nurse->getNurseid()."','".$nurse_name."','".$nurse->getN_address1()."','".$nurse->getN_address2()."','','".$nurse->getN_city()."','".$nurse->getN_state()."','".$nurse->getN_zip()."','USA'
					,'9999999999','','PHP','".$currdate."',$currtime,0,'',0,".$acct->getSalesmanNumber().",0,0,0,'".$nurse->getNurse_billto()."','',0,0,'".$acct->getChargeFreight()."',
					'".substr($acct->getShipViaCode(),0,4)."','".$acct->getShippingTerms()."','".$acct->getFobPoint()."','".$acct->getDftInvLoc()."','',0,'".$statetax."','".$countytax."',".$shiptocounty.",'',0,'',0,'','','','','',0,'','',0,'','".$currdate."',".$currtime.",'PHP',0,0,'',
					'N',0,'','".$routes[1]."',".$stops[1].",'".$routes[2]."',".$stops[2].",'".$routes[3]."',".$stops[3].",'".$routes[4]."',".$stops[4].",'".$routes[5]."',".$stops[5].",'".$routes[6]."',".$stops[6].",'".$routes[7]."',".$stops[7].",0,'')";
		
		//$insstmt = db2_prepare($conn,$inssql);
		
		$result2 = db2_exec($insconn,$inssql);
		if(!$result2){
			
			$log->lwrite(db2_stmt_errormsg());
			$log->lwrite("SQL: ".$inssql);
		}else{
		
		$currdate2 = date('Y-m-d');
		$currtime2 = date('h.i.s');
		
			$vebins = "insert into ".$weblib.".VEBCUSHIP (COMPANY_NBR,CUSTOMER_NBR,SHIPTO_NBR,NAME,ADDRESS1,ADDRESS2,ADDRESS3,
					CITY,STATE,ZIPCODE,COUNTRY,PHONE,EMAIL,CREATED_BY,CREATED_DATE) values (
					1,'".$nurse->getNurse_billto()."','".$nurse->getNurseid()."','".$nurse_name."','".
					$nurse->getF_address1()."','".$nurse->getF_address2()."',' ','".$nurse->getF_city()."','".$nurse->getF_state()."','".$nurse->getF_zip().
					"','USA','9999999999','','PHP','".$currdate2."')";
		$vebrs2 = db2_exec($insconn,$vebins);
		
		if(!$vebrs2){
			$log->lwrite("Failed to write new VEBCUSHIP record.");
			$log->lwrite(db2_stmt_errormsg());
		}
		
		
		
		$bld_ship_sql = "insert into ".$lib2.".svshpudef (SVCMP,SVCUST,SVSHIP,SVCCTD,SVCCTT,
				SVCCTU,SVMHCH,SVMOR$,SVHFEE,SVFRML,SVALBO) values(1,'".$nurse->getNurse_billto()."','".$nurse->getNurseid()."',
						'".$currdate."',0,'PHP','".$handling."',".$minordamt.",".$handlingfee.",'".$freight."','".$allowbo."')";
		$result3 = db2_exec($insconn,$bld_ship_sql);
		
		
		if(!$result3){
				
			$log->lwrite(db2_stmt_errormsg());
			$log->lwrite("SQL: ".$bld_ship_sql);
		}
		}
		
		}

db2_close($conn);
db2_close($insconn);

$log->lclose();

?>