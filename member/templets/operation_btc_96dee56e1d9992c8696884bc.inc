<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $cfg_soft_lang; ?>" />
<title><?php echo $cfg_webname; ?> - 用户中心 - 充值记录 </title>
<link href="<?php echo $GLOBALS['cfg_templets_skin']; ?>/style/scport.css" rel="stylesheet" media="screen" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo $cfg_templets_skin ?>/js/scport1.js"></script>
<script type="text/javascript" src="templets/js/j.js"></script>
<script type="text/javascript" src="templets/js/load.js"></script>
<script type="text/javascript" src="templets/js/table_function.js"></script>
<script type="text/javascript" src="templets/js/leftmenu.js"></script>
<script type="text/javascript">
	//获得选中文件的文件名
	function GetCheckfolderItem()
	{
		var allSel='';
		if(document.form1.deleteid.value) return document.form1.deleteid.value;
		for(i=0;i<document.form1.deleteid.length;i++)
		{
			if(document.form1.deleteid[i].checked)
			{
				if(allSel=='')
				allSel=document.form1.deleteid[i].value;
				else
				allSel=allSel+","+document.form1.deleteid[i].value;
			}
		}
		return allSel;
	}
	
	function DoSubmit()
	{
		var selid = GetCheckfolderItem();
		if(selid=='') {
			alert("你没选中任何信息！");
			return false;
		}
		if(window.confirm("你确定要删除这些消息么？"))
		{
			location = "operation_btc.php?dopost=del&ids="+selid;
		}
	}
</script>



<link href="<?php echo $GLOBALS['cfg_templets_skin']; ?>/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['cfg_templets_skin']; ?>/css/nav-top.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['cfg_templets_skin']; ?>/css/footer.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['cfg_templets_skin']; ?>/css/yonghu-content.css" rel="stylesheet" type="text/css">
</head>
<body>
	<?php include(DEDEMEMBER."/templets/head_btc.htm"); ?>
	<div class="clear"></div>
	<!--id="yonghu-content" 用户中心-->
	<div id="yonghu-content" class="yonghu-content">
		<?php include(DEDEMEMBER."/templets/menu_btc.php"); ?>
		<div class="yh-right">
			<div class="entrustTen lasttenorder clear">
				<div class="Tentitle">
						<span class="lightblue4">充值记录</span>
				</div>
				<div class="Tenbody">
					<div id="mcpmain" style="width:100%;float:left;padding:0; border:none;">
						<div id="appTab" style="display:none;">
							<ul>
							  <li class="thisTab"><a href="operation_btc.php">充值记录</a></li>
							</ul>
						</div>
						<div id="mainCp" style="width:100%;float:left;padding:0; border:none;">
							<!--内容消息提示 -->
							<h3 class="meTitle" style="display:none;">充值记录</h3>
							<table>							  
								<tr>
									<th width="150">订单号</th>
									<th width="80">币种</th>
									<th width="90">金额</th>
									<th width="80">手续费</th>
									<th width="90">备注</th>
									<th width="90">状态</th>
									<th width="155">时间</th>								  
								</tr>
								<tbody>
									<?php
$atts = array();
$atts['tagname'] = 'datalist';
$blockValue = $this->refObj->GetArcList($atts,$this->refObj,$fields); 
if(is_array($blockValue)){
foreach( $blockValue as $key=>$fields )
{
?>
									<tr>
									  <td><?php echo $fields['id']; ?></td>
									  <td><?php echo $coinarr[$fields['coinid']]?></td>
									  <td><?php if($fields['amount']==0){ echo "未到账"; }else{ echo $fields['amount']/1;} ?></td>
									  <td><?php echo $fields['fee'] ?></td>
									  <td><?php echo lenCat($fields['txid'])?></td>
									  <td><?php echo GetSta($fields['adduser']); ?></td>
									  <td><?php echo date('Y-m-d H:i:s',$fields['rcgtime']);?></td>
									</tr>
									<?php
}
}?>
									<tr>
										<td colspan="7"></td>
									</tr>								  
								</tbody>
								<tfoot>
									<tr>
									  <td colspan="7"><div class="pageList"><?php
$atts = array();
$atts['tagname'] = 'pagelist';
$atts['listitem'] = 'info,index,end,pre,next,pageno';
$atts['listsize'] = '5';
 echo $this->refObj->GetPageList($atts,$this->refObj,$fields); 
?>
 </div></td>
									</tr>
								</tfoot>
							</table>
						</div>						
					</div>
					<!--主操作区域 -->
					
				</div>
			</div>
		</div>
		<div style="clear:both"></div>
	</div>
	<?php include(DEDEMEMBER."/templets/foot_btc.htm"); ?>	


</body>
</html>