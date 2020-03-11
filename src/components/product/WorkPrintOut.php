<?php session_start();?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
a{text-decoration:none;color:#000000;}
a:hover{text-decoration:underline;color:#0000FF;}
sup{font-size:60%}
sup.hud{font-size:60%;font-weight:normal;}
.topborder {
	border-top:2px solid #000000;
	border-bottom:1px solid #000000;
	border-right:1px solid #000000;
}
.rightborder {
	border-right:2px solid #000000;
	border-bottom:1px solid #000000;1px
}
.leftborder {
	border-left:2px solid #000000;
	border-bottom:1px solid #000000;
	border-right:1px solid #000000;
}
.bottomborder {
	border-bottom:2px solid #000000;
}
.topleftborder {
	border-top:2px solid #000000;
	border-left:2px solid #000000;
	border-bottom:1px solid #000000;
	border-right:1px solid #000000;
}
.toprightborder {
	border-top:2px solid #000000;
	border-right:2px solid #000000;
	border-bottom:1px solid #000000;
}
.bottomleftborder {
	border-bottom:2px solid #000000;
	border-left:2px solid #000000;
}
.bottomrightborder {
	border-bottom:2px solid #000000;
	border-right:2px solid #000000;
}
.noteborder {
	border-left:2px solid #000000;
	border-right:1px solid #000000;
	border-bottom:1px dotted #000000;
}
.textborder {
	padding:5px 2em;
	border-left:2px solid #000000;
	border-right:2px solid #000000;
	border-bottom:1px dotted #000000;
}
.textborder_left {
	padding:5px 1em;
	border-left:2px solid #000000;
	border-bottom:1px dotted #000000;
	border-right:none;
}
.textborder_right {
	padding:0;
	border-right:2px solid #000000;
	border-bottom:1px dotted #000000;
	border-left:none;
	text-align:right;
}
.normalborder {
	border-bottom:1px solid #000000;
	border-right:1px solid #000000;
}
.pc{
	font-size:150%;
	font-weight:bold;
}

td{padding:5px 4px;margin:0;font-size:14px;}
th{font-size:11px;font-weight:normal;padding:0;margin:0;}
h1, h2, h3, h4, h5, h6{margin:0;padding:0;text-align:center;display:inline;}
h1{font-size:250%;}
h2{font-size:120%;}
h3{font-size:80%;}
h4{font-size:70%;font-weight:normal;}
h5{font-size:60%;font-weight:normal;}
h6{font-size:65%;}
p{font-size:60%;}
p.notes{white-space:pre-line;font-size:90%;}
table{margin:0;padding:0;clear:both;float:left;}
.rightf85{font-size:70%;float:right}
span.f70{font-size:85%;}
-->
</style>

<?php 
$_jwn = $_GET["jwn"];

if(isset($_GET['print'])){
	echo "
<!-- start of popup javascript -->
<script language=JavaScript type=text/javascript>
window.onload = function() {
	window.open('http://192.168.0.171/genWOPDFAfterCheck.php?jwn=$_jwn','','width=100,height=100')
	window.print()
	self.close()
	return;
}	
</script>";
}else{
 echo "
<!-- start of popup javascript -->
<script language=JavaScript type=text/javascript>
	function printPage(){
    window.print()
	self.close()
  }
</script>";
} 
?>
<?php
include("lang/thai.php");
include("lib/database.php");
include("lib/datetime.php");
require 'lib/session.php';

//URI processing
$fullURI = $_SERVER['REQUEST_URI'];
$pageURI = getBasicPageURI($fullURI);
if(check_session($pageURI) != 30){
	echo "No Permission";
	exit;
}
$rmy = numberToRoman(date("Y"));

$mysqli = jtsdbconnect();

//Shipping needing barcode
$sql = "SELECT `customer_code` FROM customer WHERE `display`='Y' AND `subPack`='Y'";
debugSQL($sql, false);
$result = $mysqli->query($sql);
$subPackingCust = $result->fetch_array();
WHILE ($record = $result->fetch_array()){
	array_push($subPackingCust, $record[0]);
}
//$subPackingCust = array('MAX', 'MEU', 'MAXXIS', 'DICR', 'DICREU', 'SUMITOMO', 'VTR', 'VTREU');


$sql = "SELECT * FROM `product` JOIN `work` JOIN `order` ON `product`.ptn = `work`.ptn AND `work`.jon = `order`.jon AND `work`.jwn = $_jwn GROUP BY `work`.product_code LIMIT 1";
debugSQL($sql, false);
$result = $mysqli->query($sql);
$r = $result->fetch_array();

if(isset($_GET['print'])){//put a x2 x3 on side of pcns when there are things waiting print
	$sql = "SELECT * FROM `tracking` JOIN `work` ON `tracking`.jwn = `work`.jwn WHERE `tracking`.station = 'PRINT' AND `tracking`.start_datetime LIKE '0000%' AND ptn = $r[ptn]";
//	$mysqli = jtsdbconnect();
	$result = $mysqli->query($sql);
	$dups = $result->num_rows;
	if($dups > 1)
	{
		$dups = "<sup>x$dups</sup>";
	} else {
		$dups = "";
	
		if(in_array($r["print_type"], array("PW260R6C", "ROTARY")) && $r[pattern_spec] != '0' && $r[pattern_spec] != ""){
			// find by pattern match
			$sql = "SELECT `work`.jwn, `work`.pcns, pattern_spec FROM `tracking` JOIN `work` ON `tracking`.jwn = `work`.jwn JOIN `product` ON `product`.ptn = `work`.ptn
				WHERE `tracking`.station = 'PRINT' AND `tracking`.start_datetime LIKE '0000%' AND `work`.product_code = '$r[product_code]' 	
				AND `product`.pattern_spec = '$r[pattern_spec]' AND `work`.work_progress IN ('WORK', 'REWORK')";
			debugSQL($sql, $debug);
			$result = $mysqli->query($sql);
			$prpt = $result->num_rows;
			if($prpt > 0){
				$q = $result->fetch_array();
				$pattern = "<sup>*</sup>";
				$queue = "*Pattern [$q[2]] total in queue [$prpt] = $q[1] $q[0]";
			}
		}
	}
}

$sql = "SELECT sum(stock_out) FROM `work` WHERE `work`.ptn = '$r[ptn]' AND work.stock_status LIKE 'ALLOCATE%'";
//$mysqli = jtsdbconnect();
debugSQL($sql, false);
$result = $mysqli->query($sql);
$z = $result->fetch_array();

$sql = "SELECT count(jwn) AS sc FROM `work` WHERE ptn = '$r[ptn]' AND work_type = '#00F'";
debugSQL($sql, false);
$result = $mysqli->query($sql);
$sample = $result->fetch_array();

$sql = "SELECT max(jwn) , count(jwn) , max(order_due), sum(order_qty) FROM `work` WHERE ptn = '$r[ptn]' AND jwn != '$_jwn' AND work_progress != 'VOID'";
debugSQL($sql, false);
$result = $mysqli->query($sql);
$h = $result->fetch_array();

if($h[0] != null){
	$sql = "SELECT date(end_datetime) FROM tracking WHERE jwn = '$h[0]' AND station = 'PRINT' ORDER BY seq DESC";
	debugSQL($sql, false);
	$result = $mysqli->query($sql);
	$t = $result->fetch_array();
	if(in_array($r[print_type], array("PW260R6C", "ROTARY", "PWS-450", "ZEBRA110XI4"))){
		$yearsBackTrack = 2;
		$r[tSize] = "<span class=rightf85>(".round($r[product_length]/3.175)."T)</span>";
	}else{
		$yearsBackTrack = 4;
	// showing dates of last job
	}
	if($result->num_rows != 0){
		if($t[0] != '0000-00-00'){
			$hlp = "Printed [".$t[0]."]";
			if($t[0] > date('Y-m-d', strtotime('-2 day'))){$recheckstock = "<h2>สต็อก CHECK</h2>&nbsp;<br/>";}
		} else {
			$hlp = 'In Queue';
		}
	} else {
		$hlp = "Due On [".$h[2]."]";
	}
	
	$h[3] = number_format($h[3]);
	$lastprint = "Last Job $h[0] $hlp";
	$printsummary = ", PAST:$h[1], TOTAL:$h[3]";
	
//	if(in_array($r[print_type], array("PW260R6C", "ROTARY", "PWS-450", "ZEBRA110XI4"))){
		$sql = "SELECT year(`order_due`), count(jwn) , sum(order_qty), max(order_qty), min(order_qty)
			FROM `work` WHERE ptn = '$r[ptn]' AND jwn != '$_jwn' AND work_progress != 'VOID' AND year(`order_due`) >= year(now()) - $yearsBackTrack
			GROUP BY year(`order_due`) ORDER BY year(`order_due`) DESC";
		debugSQL($sql, false);
		$result = $mysqli->query($sql);
		$h = $result->fetch_all();
		$first = 1; // presentation issue
		foreach($h as $v){
			if($v[2] > 1000000){ $v[2] = round(($v[2]/1000000),2)."M";}
			else {$v[2] = number_format($v[2]);}
			$v[3] = number_format($v[3]); $v[4] = number_format($v[4]); $v[5] = number_format($v[5]);
			if($pastdetail != ""){ $pastdetail = $pastdetail."<br/>";}
			$pastdetail = $pastdetail."$v[0] W:$v[1] X:$v[3] N:$v[4] &Sigma;:$v[2]&nbsp;";
		}
} else{
	$lastprint = "&nbsp;";
	$pastdetail = "New Product<br/>First Print";
}
// head up display under PCNS
if(in_array($r["product_code"], array("MIT", "SB", "DICL", "DICR", "DICREU", "LION"))){
	$erhud = "<br/><sup class='hud'>$r[ext_ref]</sup>"; //external customer reference head up display
}

if($r["paper_id"] != 0){
	$sql = "SELECT `material_code` FROM `material` WHERE `id` = $r[paper_id]";
	$result = $mysqli->query($sql);
	$p = $result->fetch_array();
	$r["paper_code"] = $p["material_code"];
}

//select code
$r['unit'] = strtoupper($r['unit']);
$sql = "SELECT * FROM `code` WHERE `code_type` = 'PRINT_UNIT' AND `code` = '$r[unit]'";
$result = $mysqli->query($sql);
$u = $result->fetch_array();
$r['unit'] = $u['code_label_thai'];

//select inventory
/*$sql = "SELECT * FROM `storage` WHERE `uid` = '$r[ptn]' AND `storage_reference` = '$r[jwn]'";
$result = $mysqli->query($sql);
$st = $result->fetch_array();
if($result->num_rows != 0){
	$r['stock'] = $st['stock_qty'];
}*/
$r['realStockBal'] = $r['stock'];
$r['remainStock'] = $r['stock'] - $z[0];
$r['stock'] = $r['stock_out'];//st['stock_qty'];

/*
 * business rules below 
 */
 
$r["order_date"] = id2d($r["order_datetime"]);
$d = substr($r["order_date"], 8, 2);
$m = substr($r["order_date"], 5, 2);
$y = substr($r["order_date"], 0, 4);
$r["order_date"] = "$d/$m/$y";
$r["order_due"] = id2d($r["order_due"]);
$d = substr($r["order_due"], 8, 2);
$m = substr($r["order_due"], 5, 2);
$y = substr($r["order_due"], 0, 4);
$r["order_due"] = "$d/$m/$y";

if($r["work_progress"] != "NEW" && $r["work_progress"] != "CHECK"){ 		$r["validator"] = "<h2>COPY</h2>";}
if($r["work_progress"] == "DONE"){ 	$r["validator"] = "<h2>DONE</h2>";}else if($r["work_progress"] == "VOID"){ 	$r["validator"] = "<h2>VOID</h2>";}
if($r["product_code"] == "LLIT"){$stdr = $r[order_qty]*0.05;$lower=$r[order_qty]-$stdr;$upper=$r[order_qty]+$stdr;$r["work_note"] = "*LLIT 5% Range:[$lower ~ $upper]";}


if(in_array($r[print_type], array("PW260R6C", "ROTARY"))){
	if($r["color01"] == ""){$r["screen01"]=$r["dpi01"]="&nbsp;";}
	if($r["color02"] == ""){$r["screen02"]=$r["dpi02"]="&nbsp;";}
	if($r["color03"] == ""){$r["screen03"]=$r["dpi03"]="&nbsp;";}
	if($r["color04"] == ""){$r["screen04"]=$r["dpi04"]="&nbsp;";}
	if($r["color05"] == ""){$r["screen05"]=$r["dpi05"]="&nbsp;";}
	if($r["color06"] == ""){$r["screen06"]=$r["dpi06"]="&nbsp;";}
	if($r["color07"] == ""){$r["screen07"]=$r["dpi07"]="&nbsp;";}
	if($r["color08"] == ""){$r["screen08"]=$r["dpi08"]="&nbsp;";}
	if($r["color09"] == ""){$r["screen09"]=$r["dpi09"]="&nbsp;";}
	if($r["color10"] == ""){$r["screen10"]=$r["dpi10"]="&nbsp;";}
	if($r["color11"] == ""){$r["screen11"]=$r["dpi11"]="&nbsp;";}
	if($r["color12"] == ""){$r["screen12"]=$r["dpi12"]="&nbsp;";}
}else{
	$r[degree01] = ceil($r[degree01]/15);	
	$r[degree02] = ceil($r[degree02]/15);	
	$r[degree03] = ceil($r[degree03]/15);
	$r[degree04] = ceil($r[degree04]/15);
	$r[degree05] = ceil($r[degree05]/15);
	$r[degree06] = ceil($r[degree06]/15);
	$r[degree07] = ceil($r[degree07]/15);
	$r[degree08] = ceil($r[degree08]/15);
	$r[degree09] = ceil($r[degree09]/15);
	$r[degree10] = ceil($r[degree10]/15);
	$r[degree11] = ceil($r[degree11]/15);
	$r[degree12] = ceil($r[degree12]/15);
if($r["color01"] == ""){$r["screen01"]=$r["dpi01"]="&nbsp;";}else if($r[degree01]!=0){$r[film01] ="<span class=rightf85>($r[lpi01]/$r[degree01]&deg;)</span>";}
if($r["color02"] == ""){$r["screen02"]=$r["dpi02"]="&nbsp;";}else if($r[degree02]!=0){$r[film02] ="<span class=rightf85>($r[lpi02]/$r[degree02]&deg;)</span>";}
if($r["color03"] == ""){$r["screen03"]=$r["dpi03"]="&nbsp;";}else if($r[degree03]!=0){$r[film03] ="<span class=rightf85>($r[lpi03]/$r[degree03]&deg;)</span>";}
if($r["color04"] == ""){$r["screen04"]=$r["dpi04"]="&nbsp;";}else if($r[degree04]!=0){$r[film04] ="<span class=rightf85>($r[lpi04]/$r[degree04]&deg;)</span>";}
if($r["color05"] == ""){$r["screen05"]=$r["dpi05"]="&nbsp;";}else if($r[degree05]!=0){$r[film05] ="<span class=rightf85>($r[lpi05]/$r[degree05]&deg;)</span>";}
if($r["color06"] == ""){$r["screen06"]=$r["dpi06"]="&nbsp;";}else if($r[degree06]!=0){$r[film06] ="<span class=rightf85>($r[lpi06]/$r[degree06]&deg;)</span>";}
if($r["color07"] == ""){$r["screen07"]=$r["dpi07"]="&nbsp;";}else if($r[degree07]!=0){$r[film07] ="<span class=rightf85>($r[lpi07]/$r[degree07]&deg;)</span>";}
if($r["color08"] == ""){$r["screen08"]=$r["dpi08"]="&nbsp;";}else if($r[degree08]!=0){$r[film08] ="<span class=rightf85>($r[lpi08]/$r[degree08]&deg;)</span>";}
if($r["color09"] == ""){$r["screen09"]=$r["dpi09"]="&nbsp;";}else if($r[degree09]!=0){$r[film09] ="<span class=rightf85>($r[lpi09]/$r[degree09]&deg;)</span>";}
if($r["color10"] == ""){$r["screen10"]=$r["dpi10"]="&nbsp;";}else if($r[degree10]!=0){$r[film10] ="<span class=rightf85>($r[lpi10]/$r[degree10]&deg;)</span>";}
if($r["color11"] == ""){$r["screen11"]=$r["dpi11"]="&nbsp;";}else if($r[degree11]!=0){$r[film11] ="<span class=rightf85>($r[lpi11]/$r[degree11]&deg;)</span>";}
if($r["color12"] == ""){$r["screen12"]=$r["dpi12"]="&nbsp;";}else if($r[degree12]!=0){$r[film12] ="<span class=rightf85>($r[lpi12]/$r[degree12]&deg;)</span>";}
}

if($r["EAN"] == "N/A"){$r["EAN"] = "";}else{ $r[EAN] = "EAN:".$r[EAN]."&nbsp;&nbsp;&nbsp;";}
if($r["ext_ref"] != ""){ $r[EAN] = $r[EAN]."REF:".$r[ext_ref];}
if($r[pattern_spec] != '0' || $r[pattern_spec] != ""){$r[pattern_spec] = "&nbsp;";}

$htmlBR = array("<br>", "<BR>", "<Br>", "<BR/>");
$r[note] = str_replace($htmlBR, " ", $r[note]);

$is_roll = $r["qty_unit"];
if($is_roll == "Roll"){
	$r["p_qty"] = $r["order_qty"];
	$r["pt_qty"] = $r["order_qty"];
}else{
	if($r["stock"] != 0){
	
		if($r["stock"] < $r["order_qty"]){ // when stock less than ordered amount
			$r["net_qty"] = $r["work_qty"] - $r["stock"];
			//	$r["p_qty"] = $r["net_qty"];
			$r["p_qty"] = CEIL($r["net_qty"] / $r["cuts"] / $r["prints"]);
			$r["pt_qty"] = CEIL($r["p_qty"] * $r["cuts"] * $r["prints"]);	
		}else {								//when stock more than ordered amount
			$r["net_qty"] = 0;
			$r["p_qty"] = 0;
			$r["pt_qty"] = 0;
			$stockAlert = "<h2>STOCK: $r[realStockBal]</h2>&nbsp;<br/>";
		}
	} else {
		$r["p_qty"] = CEIL($r["work_qty"] / $r["cuts"] / $r["prints"]);
		$r["pt_qty"] = CEIL($r["p_qty"] * $r["cuts"] * $r["prints"]);
		$r["net_qty"] = $r["work_qty"];
		
	}
	$r["imagePrints"] = $r["pt_qty"]*$r[colors];
	$r["p_qty"] = NUMBER_FORMAT($r["p_qty"], 0);
	$r["pt_qty"] = NUMBER_FORMAT($r["pt_qty"], 0);
	//calcuate in meters
	if(in_array($r["print_type"], array("PW260R6C", "ROTARY", "PWS-450", "ZEBRA110XI4", "EXTERNAL"))){	
		
		$baseline = ", [100$r[unit]=".number_format(100 / 1000 * $r[product_length] / $r["cuts"] / $r["prints"], 2, '.', '')."M]";
		$r["pt_qty"] = NUMBER_FORMAT(CEIL($r["net_qty"] / $r["prints"]));
		if($r["work_length"] > 0 && $r["stock"] == 0){
			$r[p_qty] = $r["work_length"]." M";
			$r["print_length"]= ", LEN: $r[p_qty]";
		}else{
			if($r[pack] == 'PACK'){ 		// not shipping in rolls, calcuate standard way
				$rtemp = CEIL($r["net_qty"] / $r["prints"] * $r["product_length"] / 1000);
				$r["print_length"]= ", LEN: ".NUMBER_FORMAT($rtemp)." M";
				$r[p_qty] = NUMBER_FORMAT($rtemp)." M";
			} else {						//shipping/order by roll
				if($r[pack] == 'ROLL'){$rtemp = CEIL(($r[order_qty] - $r[stock]) * $r[pack_size] / $r[prints] / $r[cuts] * $r[product_length] / 1000);$r[unit] = "ม้วน";}
				else if($r[pack] == 'ROLL-M1R'){$rtemp = CEIL(($r[order_qty] - $r[stock]) * $r[pack_size]); $r[unit] = "ม้วน";}
				else if($r[pack] == 'ROLL-M2R'){$rtemp = CEIL(($r[order_qty] - $r[stock]) * $r[pack_size] / 2);$r[unit] = "ม้วน";}
				else if($r[pack] == 'ROLL-M3R'){$rtemp = CEIL(($r[order_qty] - $r[stock]) * $r[pack_size] / 3);$r[unit] = "ม้วน";}
				else if($r[pack] == 'ROLL-M4R'){$rtemp = CEIL(($r[order_qty] - $r[stock]) * $r[pack_size] / 4);$r[unit] = "ม้วน";}
				else if($r[pack] == 'PCS-2R'){$rtemp = CEIL(CEIL(($r[order_qty] - $r[stock]) / (2 * $r[pack_size])) * 2 * $r[pack_size] / $r[prints] / $r[cuts] * $r[product_length] / 1000);}
				else if($r[pack] == 'PCS-3R'){$rtemp = CEIL(CEIL(($r[order_qty] - $r[stock]) / (3 * $r[pack_size])) * 3 * $r[pack_size] / $r[prints] / $r[cuts] * $r[product_length] / 1000);}
				else if($r[pack] == 'PCS-4R'){$rtemp = CEIL(CEIL(($r[order_qty] - $r[stock]) / (4 * $r[pack_size])) * 4 * $r[pack_size] / $r[prints] / $r[cuts] * $r[product_length] / 1000);}
				else if($r[pack] == 'ROLL-P2R'){$rtemp = CEIL(CEIL(($r[order_qty] - $r[stock]) / 2) * 2 * $r[pack_size] / $r[prints] / $r[cuts] * $r[product_length] / 1000);$r[unit] = "ม้วน";}
				else if($r[pack] == 'ROLL-P3R'){$rtemp = CEIL(CEIL(($r[order_qty] - $r[stock]) / 3) * 3 * $r[pack_size] / $r[prints] / $r[cuts] * $r[product_length] / 1000);$r[unit] = "ม้วน";}
				else if($r[pack] == 'ROLL-P4R'){$rtemp = CEIL(CEIL(($r[order_qty] - $r[stock]) / 4) * 4 * $r[pack_size] / $r[prints] / $r[cuts] * $r[product_length] / 1000);$r[unit] = "ม้วน";}
				$r[work_qty] = NUMBER_FORMAT($rtemp)." M";
				$r[net_qty] = NUMBER_FORMAT($rtemp)." M";
				$r["print_length"]= ", LEN: ".NUMBER_FORMAT($rtemp)." M, $r[pack]";
				$printsummary = $printsummary.", $r[pack]";
				$r[p_qty] = NUMBER_FORMAT($rtemp)." M";
			}
		}
		if(isset($_GET['print'])){ //update print length when new print is issued
			$sql = "UPDATE `work` SET `print_length` = $rtemp WHERE `jwn` = $_jwn";
			$mysqli->query($sql);
		}
	} else {
		$r["print_length"] = "";
	}
	$r["order_qty"] = NUMBER_FORMAT($r["order_qty"], 0);
	
}

//special DICR testing products for stock printing
/*if(in_array($r["ptn"], array(7543,8929,8930,8928,8941,8951,8946,8948,8947,8977,8979,8976,8983,8981,8982,8984,8985,8987,8986,8991,9068,9070,9077,9073,9075,9076,9074,9072,9080,9081,9079,9078,9082,9215,9216,9644,9739,9749,9750,9751,9795,10161,10162,10163,10164,10237,10296,10295,10363,10360,10362,10361,10358,10357,10375,10374,10405,10406,10456,10458,10459,10460,10553,10557,10556,11123,11219,11218,11376,11375,11387,11586,11690,11693,11860,12221,12307,12308,12599,12610,12612,12613,12797,12808,13080,13087,13374,17263))){
	$r["pattern_spec"] = $r["pattern_spec"]."**";
}
*/
$mysqli->close();

if($r["colors"] > 7){ echo "
<style type=\"text/css\">
.dResize60 td,.dynResize th{font-size:60%;}
.dResize80 td,.dynResize th{font-size:75%;}
</style>";
}

echo "
<title>Work#$r[jwn]</title>
</head>
<body>";

if(in_array($r[print_type], array("PWS-450", "PLATFORM", "CONSOLE"))){
	$hashSign = "#";
echo "<span style=\"position:absolute;left:520px;top:15px;font-size:80%;\">&Sigma;IP:".$r[imagePrints]."</span>";
}
echo "<span style=\"position:absolute;left:600px;top:550px;font-size:75%\">[ ]ตัวอย่าง___pcs</span>";

switch($r[work_type]){
	case "#F00": //red
		$bgcolor = "#FDD";
		echo "<span style=\"position:absolute;left:600px;top:470px;font-size:120%;\">*ด่วน*<br/>*急單*</span>";
		break;
	case "#00F": //blue
		$bgcolor = "#CFF";
		echo "<span style=\"position:absolute;left:600px;top:470px;font-size:120%;\">*ตัวอย่าง*<br/>*樣單$sample[sc]*</span>";
		$redoMsg = "<span class=rightf85>[  ]สี/色[  ]ฟิล์ม</span>";
		break;
	case "#0F0": // green
		$bgcolor = "#AFA";
		echo "<span style=\"position:absolute;left:600px;top:470px;font-size:120%;\">*BLOCK*<br/>*製版*</span>";
		$redoMsg = "<span class=rightf85>[  ]สี/色[  ]ฟิล์ม</span>";
		break;
	case "#FF0": // yellow
	case "#F80": // yellow ///	$bgcolor = "#FF0";
		$bgcolor = "#FF6";
		echo "<span style=\"position:absolute;left:600px;top:470px;font-size:120%;\">*REWORK*<br/>*修理單*</span>";
		echo "<span style=\"position:absolute;left:600px;top:520px;font-size:80%\">Stock:[  ]OK [  ]NG</span>";
		break;
	default:
		$bgcolor = "#FFF";
		break;
}

echo "
<table width=\"750\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=$bgcolor>
  <tr>
    <th colspan=\"2\" rowspan=2>รหัสสินค้า</th>
    <th colspan=\"3\" rowspan=2 align=\"left\" class=pc>$r[pcns] &nbsp;$dups$pattern$erhud</th>
    <td colspan=\"2\"><a href=# onClick=printPage()><h2>ใบปฏิบัติงาน</h2></a></td>
    <th align=\"right\">เลขที่</th>
    <td colspan=2  align=center height=25><h2>$r[jwn]</h2></td>
  </tr>
  <tr>
    <th colspan=\"2\" align=\"left\"><IMG SRC=\"lib/code128.php?codetype=Code39&text=PN$r[ptn]&size=12\"></th>
    <th align=\"right\">วันที่</th>
    <td align=center colspan=2 height=25>$r[order_date]</td>
  </tr>
  <tr>
    <th colspan=\"2\" class=\"topleftborder\" align=\"center\">รายการสินค้า</th>
    <td colspan=\"3	\" class=\"topborder\" height=50px>$r[product_name]</td>
    <th align=\"center\" class=\"topborder\">จำนวน</th>
    <td class=\"topborder\" align=center><h2>$r[order_qty]</h2>$r[unit]</td>
    <td class=\"topborder\" align=center>วันที่ส่งของ</td>
    <td colspan=\"2\" class=\"toprightborder\" align=center><b>$r[order_due]</b></td>
  </tr>
  <tr>
    <th colspan=\"2\" class=\"leftborder\" align=\"center\">ชนิดกระดาษ</th>
    <td colspan=\"3\" class=\"normalborder\"><span class=f70>$r[paper_code]</a></td>
    <th class=\"normalborder\">จำนวนกระดาษ</th>
    <td class=\"normalborder\" align=center><b>$r[p_qty]</b></td>
    <th class=\"normalborder\">PO.NO.</th>
    <td colspan=\"2\" class=\"rightborder\" align=center>$r[order_number]</td>
  </tr>
  <tr>
    <th class=\"leftborder\" width=5%>ทำบล็อก</th>
    <th class=\"normalborder\" width=5%>เลขที่สี</th>
    <th class=\"normalborder\" width=15%>รหัสของบล็อก</th>
    <th class=\"normalborder\" width=15%>ผ้าบล็อก</th>
    <th class=\"normalborder\" width=5%>เลขที่สี</th>
    <th class=\"normalborder\" width=15%>รหัสของบล็อก</th>
    <th class=\"normalborder\" width=20%>ผ้าบล็อก</th>
    <th class=\"normalborder\" width=8%>รหัสบล็อกใหม่</th>
    <th class=\"normalborder\" width=8%>รหัสบล็อกใหม่</th>
    <th class=\"rightborder\" width=4%>หาบล็อก</th>
  </tr>
  <tr class=dResize60>
    <td rowspan=\"6\" class=\"leftborder\">&nbsp;</th>
    <th class=\"normalborder\" align=\"center\">1</th>
    <td class=\"normalborder\" align=\"center\">$r[screen01] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi01] &nbsp;</td>
    <th class=\"normalborder\" align=\"center\">7</th>
    <td class=\"normalborder\" align=\"center\">$r[screen07] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi07] &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td rowspan=\"6\" class=\"rightborder\">&nbsp;</td>
  </tr>
  <tr class=dResize60>
    <th class=\"normalborder\" align=\"center\">2</th>
    <td class=\"normalborder\" align=\"center\">$r[screen02] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi02] &nbsp;</td>
    <th class=\"normalborder\" align=\"center\">8</th>
    <td class=\"normalborder\" align=\"center\">$r[screen08] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi08] &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
  </tr>
  <tr class=dResize60>
    <th class=\"normalborder\" align=\"center\">3</th>
    <td class=\"normalborder\" align=\"center\">$r[screen03] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi03] &nbsp;</td>
    <th class=\"normalborder\" align=\"center\">9</th>
    <td class=\"normalborder\" align=\"center\">$r[screen09] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi09] &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
  </tr>
  <tr class=dResize60>
    <th class=\"normalborder\">4</th>
    <td class=\"normalborder\" align=\"center\">$r[screen04] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi04] &nbsp;</td>
    <th class=\"normalborder\">10</th>
    <td class=\"normalborder\" align=\"center\">$r[screen10] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi10] &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
  </tr>
  <tr class=dResize60>
    <th class=\"normalborder\">5</th>
    <td class=\"normalborder\" align=\"center\">$r[screen05] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi05] &nbsp;</td>
    <th class=\"normalborder\">11</th>
    <td class=\"normalborder\" align=\"center\">$r[screen11] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi11] &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
  </tr>
  <tr class=dResize60>
    <th class=\"normalborder\">6</th>
    <td class=\"normalborder\" align=\"center\">$r[screen06] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi06] &nbsp;</td>
    <th class=\"normalborder\">12</th>
    <td class=\"normalborder\" align=\"center\">$r[screen12] &nbsp;</td>
    <td class=\"normalborder\" align=\"center\">$r[dpi12] &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
  </tr>
  <tr>
    <th rowspan=\"2\" class=\"leftborder\">การตัด</th>
    <td class=\"normalborder\" align=\"center\">&nbsp;</td>
    <th colspan=\"2\" class=\"normalborder\" align=\"center\">รายละเอียดในการตัด</th>
    <th class=\"normalborder\">$r[unit]</th>
    <th class=\"normalborder\">จำนวนชิ้น</th>
    <th class=\"normalborder\">วันที่</th>
    <th class=\"normalborder\">เวลา</th>
    <th colspan=\"2\" class=\"rightborder\">ลงชื่อ</th>
  </tr>
  <tr>
    <td class=\"normalborder\" align=\"center\">$r[cuts]&nbsp;</td>
    <td colspan=\"2\" class=\"normalborder\" align=\"center\">
			$r[product_width]*$r[product_length] $r[tSize]</td>
    <td class=\"normalborder\" align=\"center\"><b>$r[prints]&nbsp;</b></td>
    <td class=\"normalborder\" align=\"center\"><b>$r[pt_qty]&nbsp;</b></td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td colspan=\"2\" class=\"rightborder\">&nbsp;</td>
  </tr>
</table>
<table width=\"750\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=$bgcolor>
  <tr>
    <th class=\"leftborder\">ลำดับ / (สีที่กำหนด) / รหัสสี</th>
    <th class=\"normalborder\">ผู้ตรวจสี Q.C</th>
    <th class=\"normalborder\">NO</th>
    <th class=\"normalborder\">สูญเสีย</th>
    <th class=\"normalborder\">คงเหลือ</th>
    <th class=\"normalborder\">เวลา</th>
    <th class=\"rightborder\">ลงชื่อ</th>
  </tr>
  <tr class=dResize80>
    <td class=\"leftborder\">1. $r[color01] $r[film01] <span class=rightf85>$hashSign$r[dpi01]</span>$redoMsg</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>
  <tr class=dResize80>
    <td class=\"leftborder\">2. $r[color02] $r[film02] <span class=rightf85>$hashSign$r[dpi02]</span>$redoMsg</td>
    <td class=\"normalborder\">$rrrrr &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>
  <tr class=dResize80>
    <td class=\"leftborder\">3. $r[color03] $r[film03] <span class=rightf85>$hashSign$r[dpi03]</span>$redoMsg</td>
    <td class=\"normalborder\">$rrrrr &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>
  <tr class=dResize80>
    <td class=\"leftborder\">4. $r[color04] $r[film04] <span class=rightf85>$hashSign$r[dpi04]</span>$redoMsg</td>
    <td class=\"normalborder\">$rrrrr &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>
  <tr class=dResize80>
    <td class=\"leftborder\">5. $r[color05] $r[film05] <span class=rightf85>$hashSign$r[dpi05]</span>$redoMsg</td>
    <td class=\"normalborder\">$rrrrr &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>
  <tr class=dResize80>
    <td class=\"leftborder\">6. $r[color06] $r[film06] <span class=rightf85>$hashSign$r[dpi06]</span>$redoMsg</td>
    <td class=\"normalborder\">$rrrrr &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>";
if($r["colors"] >= 7){echo "
  <tr class=dResize80>
    <td class=\"leftborder\">7. $r[color07] $r[film07] <span class=rightf85>$hashSign$r[dpi07]</span>$redoMsg</td>
    <td class=\"normalborder\">$rrrrr &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>";
}
if($r["colors"] >= 8){echo "
  <tr class=dResize80>
    <td class=\"leftborder\">8. $r[color08] $r[film08] <span class=rightf85>$hashSign$r[dpi08]</span>$redoMsg</td>
    <td class=\"normalborder\">$rrrrr &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>";
}
if($r["colors"] >= 9){echo "
  <tr class=dResize80>
    <td class=\"leftborder\">9. $r[color09] $r[film09] <span class=rightf85>$hashSign$r[dpi09]</span>$redoMsg</td>
    <td class=\"normalborder\">$rrrrr &nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>";
}
if($r["colors"] >= 10){echo "
  <tr class=dResize80>
    <td class=\"leftborder\">10. $r[color10] $r[film10] <span class=rightf85>$hashSign$r[dpi10]</span>$redoMsg</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>";
}
if($r["colors"] >= 11){echo "
  <tr class=dResize80>
    <td class=\"leftborder\">11. $r[color11] $r[film11] <span class=rightf85>$hashSign$r[dpi11]</span>$redoMsg</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>";
}
if($r["colors"] > 11){echo "
  <tr class=dResize80>
    <td class=\"leftborder\" width=40%>12. $r[color12] $r[film12] <span class=rightf85>$hashSign$r[dpi12]</span>$redoMsg</td>
    <td class=\"normalborder\" width=10%>&nbsp;</td>
    <td class=\"normalborder\" width=5%>&nbsp;</td>
    <td class=\"normalborder\" width=10%>&nbsp;</td>
    <td class=\"normalborder\" width=10%>&nbsp;</td>
    <td class=\"normalborder\" width=10%>&nbsp;</td>
    <td class=\"rightborder\" width=15%>&nbsp;</td>
  </tr>";
}
echo " 
</table>
<table width=\"750\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=$bgcolor>
  <tr>
    <th class=\"leftborder\">เคลือบ</th>
    <td class=\"normalborder\" align=center>$r[laminate]</td>
    <th class=\"normalborder\">ชนิดผ้าเคลือบ</th>
    <td class=\"normalborder\" align=center>$r[tape_code]&nbsp;</td>
    <th class=\"normalborder\">สูญเสีย</th>
    <th class=\"normalborder\">$r[fail]&nbsp;</th>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>
  <tr>
    <th class=\"leftborder\">ไดคัท</th>
    <td class=\"normalborder\" align=center>$r[dicut]&nbsp;</td>
    <th class=\"normalborder\">รหัสไดคัท</th>
    <td class=\"normalborder\" align=center>$r[dicut_plate]&nbsp;</td>
    <th class=\"normalborder\">สูญเสีย</th>
    <th class=\"normalborder\">$r[fail]&nbsp;</th>
    <td class=\"normalborder\">&nbsp;</td>
    <td class=\"rightborder\">&nbsp;</td>
  </tr>
  <tr>
    <th class=\"leftborder\" width=10%>จำนวนที่ผลิต</th>
    <td class=\"normalborder\" align=center width=15%>$r[net_qty]&nbsp;</td>
    <th class=\"normalborder\" width=10%>สูญเสียรวม</th>
    <th class=\"normalborder\" width=20%>$r[fail]&nbsp;</th>
    <th class=\"normalborder\" width=10%>รหัสตระกร้า</th>
    <td class=\"normalborder\" width=10%>$r[cell]&nbsp;</td>
    <td class=\"normalborder\" width=10%>&nbsp;</td>
    <td class=\"rightborder\" width=15%>&nbsp;</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"noteborder\"><p class=\"notes\">หมายเหตุ : $lastprint</p></td>
    <th class=\"normalborder\">ยอดเข้าสต็อก</th>
    <th class=\"normalborder\">&nbsp;</th>
    <th class=\"normalborder\">สต็อกคงเหลือ</th>
    <td class=\"rightborder\">&nbsp;$r[stock] / $r[remainStock]</td>
  </tr>
  <tr>
    <td colspan=\"5\" class=\"textborder_left\"><p class=\"notes\">TYPE:$r[print_type]$printsummary $baseline</p></td>
    <td colspan=\"3\" class=\"textborder_right\">WO#<IMG SRC=\"lib/code128.php?codetype=Code39&text=WO$r[jwn]&size=12\">
    </td>
    </tr>
  <tr class=dResize80>
    <td colspan=\"4\" class=\"textborder_left\"><p class=\"notes\">$r[note] &nbsp;</p></td>
    <td colspan=\"4\" class=\"textborder_right\"><p class=\"notes\">$stockAlert $recheckstock $pastdetail</p></td>
 </td>
  </tr>
  <tr>
    <td colspan=\"5\" class=\"textborder_left\">$r[EAN] &nbsp; $r[pattern_spec] &nbsp;$r[work_note]</td>";
if($r[work_progress] != 'DONE' && $r[work_progress] != 'VOID'){
	echo "<td colspan=\"3\" class=\"textborder_right\">PW<IMG SRC=\"lib/code128.php?codetype=Code39&text=$r[passcode]&size=12\"></td>";
} else {
	echo "<td colspan=\"3\" class=\"textborder_right\">บาร์โค้ดไม่สามารถใช้ได้</td>";
}
echo "
  </tr>
  <tr>
    <td class=\"bottomleftborder\" colspan=3 height=50px>ผู้สั่งงาน  &nbsp;<h2>$r[entry]</h2></td>
    <td colspan=\"2\" class=\"bottomborder\">ผู้อนุมัติ  &nbsp;<h2>$r[verify]</h2></td>
    <td colspan=\"3\" class=\"bottomrightborder\">ผู้ตรวจสอบ &nbsp;$r[validator]</td>
        <br></td>
  </tr>";
echo "
  <tr>
	<td>
  ";
// if($r[work_progress] != 'DONE' && $r[work_progress] != 'VOID' && in_array($r[product_code], $subPackingCust)){
// 	echo "<IMG SRC=\"lib/qrcode.php?qrtext=$r[pcns]\">";
// } else
  if($r[work_progress] != 'DONE' && $r[work_progress] != 'VOID' && in_array($r[print_type], array("CONSOLE", "PLATFORM", "PWS-450"))){
 	$paperCode = htmlentities($r[paper_code]);
	echo "<IMG SRC=\"lib/qrcode.php?json=1&type=work&ptn=$r[ptn]&pcns=$r[pcns]&passcode=$r[passcode]&jwn=$r[jwn]&order_qty=$r[order_qty]&stock=$r[stock]&cuts=$r[cuts]&prints=$r[prints]&printSize=$r[product_width]x$r[product_length]&print_qty=$r[p_qty]&print_tqty=$r[pt_qty]&paperCode=$paperCode\">";
}
	echo "&nbsp;</td>
    <td align=\"center\" colspan=\"6\" style=vertical-align:top;>
      <h5>$r[jwn] $_SESSION[username] &copy $rmy chihmingliao</h5>
      <h3>JUIH TAY CO., LTD. ".date("d/m/Y H:i")." </h3>
      <h4>PL-F-01</h4>
      <h5>18-07-2559 REV.02</h5><br/>
     	$queue
    </td> 
	<td><IMG SRC=\"lib/qrcode.php?xml=1&type=work&jwn=WO$r[jwn]&passcode=$r[passcode]&ptn=PN$r[ptn]\"></td>
   </tr>";
echo"
</table>
</body>
</html>";
//include("product_po.php");
?>
