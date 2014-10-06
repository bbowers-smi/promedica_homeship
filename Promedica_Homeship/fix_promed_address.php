<?php
include_once 'Iseries_Connect.php';

date_default_timezone_set('America/New_York');

set_time_limit(45);
//Set library variable here

$weblib = "S2KCEE";//S2KCEE

$currdate = date('Ymd');
$currtime = strftime('%l%M%S',time());

//Insert new Shipto records
$iseries = new Iseries_Connect();
$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');

$sql = "select eacust,eaship,eaadd1,eaadd2,eaadd3 from s2kcee.vebcuship where eacust='155HS41' and eaadd1 not like 'VISIT%'";

$result = db2_exec($conn,$sql);

while($row=db2_fetch_assoc($result)){
	$updsql = "update s2kcee.vebcuship set eaadd1='VISITING NURSE SERVICE',eaadd2='".$row['EAADD1']."' where eacust='155HS41' and eaship='".$row['EASHIP']."'";

	$updrs = db2_exec($conn,$updsql);
	if(!$updrs){
		echo "Failed to update shipto ".$row['EASHIP'];
		echo db2_stmt_errormsg();
	}
}
db2_close($conn);

?>