<?php
include_once 'Iseries_Connect.php';
include_once 'AccountDetails.php';
include_once 'logging.php';
/** 
 * @author bbowers
 * 
 */
class AccountUtilities {
	
	/**
	 */
	function __construct() {
		
		
	}
	
	public function getAccountInfo($billid){
		$iseries = new Iseries_Connect();
		$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
		
		if($conn){
			$acct = new AccountDetails();
			
			$custsql = "select rmchfr,rmloc,rmfob,rmsmno,rmshtr,rmsvia,rmrout1,rmrout2,rmrout3,rmrout4,rmrout5,
			rmrout6,rmrout7,rmstop1,rmstop2,rmstop3,rmstop4,rmstop5,rmstop6,rmstop7
			from r50files.varcust where rmcmp = 1 and rmcust = '".$billid."'";
			$custstmt = db2_prepare($conn,$custsql);
			if($custstmt){
				$custrs = db2_execute($custstmt);
				while($row = db2_fetch_assoc($custstmt)){
						
					$acct->setSalesmanNumber($row['RMSMNO']);
					$acct->setChargeFreight($row['RMCHFR']);
					$acct->setDftInvLoc($row['RMLOC']);
					$acct->setFobPoint($row['RMFOB']);
					$acct->setShippingTerms($row['RMSHTR']);
					$acct->setShipViaCode($row['RMSVIA']);
					$routes[1] = ($row['RMROUT1']);
					$routes[2] = ($row['RMROUT2']);
					$routes[3] = ($row['RMROUT3']);
					$routes[4] = ($row['RMROUT4']);
					$routes[5] = ($row['RMROUT5']);
					$routes[6] = ($row['RMROUT6']);
					$routes[7] = ($row['RMROUT7']);
						
					$stops[1] = ($row['RMSTOP1']);
					$stops[2] = ($row['RMSTOP2']);
					$stops[3] = ($row['RMSTOP3']);
					$stops[4] = ($row['RMSTOP4']);
					$stops[5] = ($row['RMSTOP5']);
					$stops[6] = ($row['RMSTOP6']);
					$stops[7] = ($row['RMSTOP7']);
			
					$acct->setRoutes($routes);
					$acct->setStops($stops);
				}
		}else{
			echo db2_stmt_errormsg();
		}
		}
		db2_close($conn);
		return $acct;
	}
	
	public function getAcctId($checkid){
		$iseries = new Iseries_Connect();
		$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
		$custid = "";
		if($conn){
			$sql = "select PRCUST from OURLIB.PRHMSH where PRCMP = 1 and PRSHIP = '".$checkid."'";
			
			$result = db2_exec($conn,$sql);
			if($result){
			while($row=db2_fetch_assoc($result)){
				$custid = $row['PRCUST'];
			
			}
			}else{
				echo "No results returned.<br />";
			}
		}
		
		db2_close($conn);
		return $custid;
	}
	
	public function getBaseShiptoVal($billto){
		$iseries = new Iseries_Connect();
		$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
		
		if($conn){
			$sql = "select rvtxst,rvtxcn,rvcnty from r50files.varship where rvcmp = 1 and rvcust = '".$billto."' and rvship = '1'";
			$result = db2_exec($conn,$sql);
			if($result){
				while($row=db2_fetch_assoc($result)){
					$rs = array($row['RVTXST'],$row['RVTXCN'],$row['RVCNTY']);	
				}
			}
		}
		db2_close($conn);
		return $rs;
	}
	
	public function getShiptoUsrDefined($billto){
		$log = new Logging();
		$log->lfile('/home/sftppro/logs/promedlog.log');
		$iseries = new Iseries_Connect();
		$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
		$rs = "";
		if($conn){
			$sql = "select svmhch,svmor$,svhfee,svfrml,svalbo from r50modsdta.svshpudef where svcmp = 1 and svcust = '".$billto."' and svship = '1'";
			$result = db2_exec($conn,$sql);
			if($result){
				while($row=db2_fetch_assoc($result)){
					$rs = array($row['SVMHCH'],$row['SVMOR$'],$row['SVHFEE'],$row['SVFRML'],$row['SVALBO']);
				}
			}
		}
		db2_close($conn);
		
		$log->lclose();
		return $rs;
		
	}
	
	public function getShiptozip($cust,$ship){
		$iseries = new Iseries_Connect();
		$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
		$rs = "";
		if($conn){
			$sql = "select rvmship from r50files.varship where rvcmp = 1 and rvcust = '".$cust."' and rvship = '".$ship."'";
			$result = db2_exec($conn,$sql);
			if($result){
				$rs = db2_fetch_assoc($result);
			}
			return $rs;
		}
	}
	
	public function getOfficeAddress($billto){
		$log = new Logging();
		$log->lfile('/home/sftppro/logs/promedlog.log');
		$iseries = new Iseries_Connect();
		$conn = $iseries->getConnection('S106B0CP','WS03','LAUREN16');
		if($conn){
			$sql = "select rvadd1,rvadd2,rvadd3,rvcity,rvst1,rvmzip from r50files.varship where rvcust='".$billto."' and rvship='1'";
			$result = db2_exec($conn,$sql);
			
			if($result){
			
			$log->lclose();
			 return $addresses = db2_fetch_assoc($result);
			}
		}else{
			$log->lwrite(db2_conn_errormsg());
			$log->lclose();
		}
		
	}
}

?>