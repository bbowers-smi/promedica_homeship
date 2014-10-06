<?php
include_once 'logging.php';
include_once 'Iseries_Connect.php';
include_once 'AccountDetails.php';
include_once 'AccountUtilities.php';

date_default_timezone_set('America/New_York');

$currdate = date('Ymd');
$log = new Logging();
$log->lfile('/home/sftppro/logs/promedlog.log');

$iseries = new Iseries_Connect();
$conn = $iseries->getConnection('S106B0CP','PHPUSER','phpusri7');

$qry = "select oaord,oaship,oasnam,oacont,eausrn,obitem,obitd1,obitd2,obum,obqshp,obqbko,obqord,obpric from r50files.vcohead
left outer join r50files.vcodetl on oaord=obord
left outer join s2kcee.vebuser on oacont=eausri
where oacust like '155H%' and oaordt=".$currdate." and oaorwt='SYS' order by oacust";

$result = db2_exec($conn, $qry);
if($result){
    while($row=db2_fetch_assoc($result)){
        $order[] = new DailySalesRec();
        
        echo "<pre>";
  print_r($row);
  echo "<br />";
  echo "</pre>";
    }  
}
?>