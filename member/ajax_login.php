<?php
/**
 * @version        $Id: ajax_login.php 1 8:38 2013年7月9日Z
 */
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
$userArray=array(  
    'username' => "", 
	'userid' => "", 
	'coin' => "", 
	'mvip' => "",
	'FeePer' => "",
	'TXFeePer' => "",
	'msg' => "", 
	//分母交易币余额
	'denobalance' => "",
	//分子交易币余额
	'numbalance' => "",
	//我的挂单
	'myorderlist' => "",
	//我的交易记录
	'mydeallist' => ""
    );
$json_string = json_encode($userArray);  

if($myurl == '') 
{
	echo $json_string;
	exit('');
}

$coinid=preg_replace("#[^0-9-]#", "", $coinid);
$moneyid=preg_replace("#[^0-9-]#", "", $moneyid);


$uid  = $cfg_ml->M_LoginID;


//读取币种
$dsql->SetQuery("SELECT * FROM #@__btctype WHERE coinsign=1 ORDER BY id");
$dsql->Execute();
while($rcoint = $dsql->GetObject())
{
	$coinarr[$rcoint->id] =  $rcoint->cointype ;
}



//读取入账订单
$dsql->SetQuery("SELECT id,coinid,txid,amount FROM #@__btcrecharge WHERE userid='".$cfg_ml->M_ID."' AND checked=1 AND adduser=0 AND amount>0 AND dealmark=1 ORDER BY id");
$dsql->Execute();
while($rcg = $dsql->GetObject())
{
	$rsup = $dsql->ExecuteNoneQuery("Update #@__btcrecharge Set adduser=1 Where id = '".$rcg->id."'"); 
	$rcoin = $dsql->ExecuteNoneQuery2("Update #@__btccoin Set c_deposit=c_deposit+".$rcg->amount.",edittime = '". time() ."' where userid = '".$cfg_ml->M_ID."' AND coinid = '".$rcg->coinid."'"); 
	if($rcoin==0){
		$query = "Insert Into `#@__btccoin`(userid,coinid,cointype,c_deposit,c_freeze,edittime) Values('".$cfg_ml->M_ID."','".$rcg->coinid."','".$coinarr[$rcg->coinid]."','".$rcg->amount."','0','". time() ."')";
		$rnew = $dsql->ExecuteNoneQuery2($query);
	}
}


$arrcoin = $cfg_arrcoin;

//分母交易币余额
if(moneyid!=""){
	$sql = "Select c_deposit,c_freeze From #@__btccoin where coinid = ".$moneyid." AND userid='".$cfg_ml->M_ID."' ;";
	$rcoin = $dsql->GetOne($sql);
	$denobalance = $rcoin['c_deposit']?rtrimandformat(floor($rcoin['c_deposit']*10000)/10000, 10):"0";
}

//分子交易币余额
if($coinid!=""){
	$sql = "Select c_deposit,c_freeze From #@__btccoin where coinid = ".$coinid." AND userid='".$cfg_ml->M_ID."' ;";
	$rcoin = $dsql->GetOne($sql);
	$numbalance = $rcoin['c_deposit']?rtrimandformat(floor($rcoin['c_deposit']*10000)/10000, 10):"0";
}


//我的挂单
if($coinid!="" && moneyid!=""){
	$addsql="AND coinid='".$coinid."' AND moneyid='".$moneyid."'";
	$dsql->SetQuery("SELECT oid,uprice,btccount,dealtype FROM #@__btcorder WHERE userid=".$cfg_ml->M_ID." $addsql ORDER BY ordertime DESC");
	$dsql->Execute();
	while($rord = $dsql->GetObject())
	{
		$orderarr[] = array(  
	    'id' => $rord->oid,
		'dealtype' => $rord->dealtype, 
		'uprice' => rtrimandformat($rord->uprice/1), 
		'btccount' => rtrimandformat($rord->btccount/1)
	    );
	}
}

//我的交易记录
if($coinid!="" && $moneyid!=""){
	$dsql->SetQuery("SELECT dealtype, btccount, uprice, tprice FROM #@__btcdeal WHERE (buserid = '".$cfg_ml->M_ID."' OR suserid = '".$cfg_ml->M_ID."') and coinid = ".$coinid." and moneyid = ".$moneyid." ORDER BY dealtime DESC");
	$dsql->Execute();
	while($rord = $dsql->GetObject()){
		$dealarr[] = array(
		'dealtype' => $rord->dealtype,
		'btccount' => rtrimandformat($rord->btccount/1),
		'uprice' => rtrimandformat($rord->uprice/1),
		'tprice' => rtrimandformat($rord->tprice/1)
		);
	}
}

$credit = $cfg_ml->M_Credit;
$money  = $cfg_ml->M_Money;
$uid  = $cfg_ml->M_LoginID;
$profit = $money-$credit+$cfg_ml->M_NowPay;
$userArray=array(  
    'username' => $cfg_ml->M_LoginID, 
	'userid' => $cfg_ml->M_ID, 
	'coin' => $arrcoin, 
	'mvip' => $cfg_ml->M_Vip,
	'FeePer' => $cfg_ml->M_FeePer,
	'TXFeePer' => $cfg_ml->M_TXFeePer,
	'msg' => $msg, 
	//分母交易币余额
	'denobalance' => $denobalance,
	//分子交易币余额
	'numbalance' => $numbalance,
	//我的挂单
	'myorderlist' => $orderarr,
	//我的交易记录
	'mydeallist' => $dealarr
    );

$json_string = json_encode($userArray);  
echo $json_string;



/**
 *  加密函数
 *
 * @access    public
 * @param     string  $string  字符串
 * @param     string  $action  操作 EN加密
 * @return    string
 */
function mchStrCode($string,$action='DECODE')
{
    //$key    = substr(md5($_SERVER["HTTP_USER_AGENT"].$GLOBALS['cfg_cookie_encode']),8,18);
    $key    = "a87856749ae10f3c53";
	$string    = $action == 'ENCODE' ? $string : base64_decode($string);
    $len    = strlen($key);
    $code    = '';
    for($i=0; $i < strlen($string); $i++)
    {
        $k        = $i % $len;
        $code  .= $string[$i] ^ $key[$k];
    }
    $code = $action == 'DECODE' ? $code : base64_encode($code);
    return $code;
}
?>

