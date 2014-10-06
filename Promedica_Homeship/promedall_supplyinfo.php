<?php
include_once 'Iseries_Connect.php';
include_once 'logging.php';

$log = new Logging();
$log->lfile('/home/sftppro/logs/promedlog.log');

//Get connection to iSeries
$iseries = new Iseries_Connect();
$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');

$lib = "OURLIB";
$lib2 = "R50FILES";
$lib3 = "R50MODSDTA";
$workdate = '20140812';
if($conn){
	
	$sql = "select prcust from ".$lib.".prhmsh";
	
	$result = db2_exec($conn,$sql);
	if($result){
		while($row=db2_fetch_assoc($result)){
			$custlist[] = $row;
		}
	}
	$newfile = fopen("/home/sftppro/outgoing/homeship".date('Ymd',strtotime('-1 day')).".csv","w");
	$headers = array("EPISODE ","SUPPLY ID NUMBER","DESCRIPTION ","QTY ","UNITS ","COST ","HCPCS CODE","REVENUE CODE","BILLABLE ","DATE ","INVOICE# ");
	fputcsv($newfile, $headers);
	foreach($custlist as $row){
		$vsasql = "select sacust,saship,saordt,sbitem,sbitd1,sbum,sbpric,sbqshp,sbindt,sbinv,hcpcs,revcode,billable from 
				".$lib2.".vsahead
				left outer join ".$lib2.".vsadetl on sacust=sbcust and saord=sbord
				left outer join ".$lib3.".phcrevcd on item=sbitem
				where sacmp=1 and sacust='".$row['PRCUST']."' and satype='O' and sbindt >='".$workdate."' order by saship,saord";
		
		$result2 = db2_exec($conn,$vsasql);
		if($result2){
			
			while($row2=db2_fetch_assoc($result2)){
				$shipto = $row2['SASHIP']; 
				$item =$row2['SBITEM'];
				$descr =$row2['SBITD1'];
				$uom =$row2['SBUM'];
				$itemprice =$row2['SBPRIC']." ";
				$hcpcs =$row2['HCPCS']." ";
				$revenue =$row2['REVCODE']." ";
				$billable =$row2['BILLABLE']." ";
				$invdate =$row2['SBINDT']." ";
				$invnbr =$row2['SBINV']." ";
				$qtyship = $row2['SBQSHP']." ";
				$temp = array($shipto,$item,$descr,$qtyship,$uom,$itemprice,$hcpcs,$revenue,$billable,$invdate,$invnbr);
				fputcsv($newfile, $temp);
			}
			
		}else{
			$log->lwrite("Failed to get detail.");
			$log->lwrite(db2_stmt_errormsg());
		}
	}
	fclose($newfile);
}
db2_close($conn);

?>