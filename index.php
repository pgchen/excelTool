<?php
set_time_limit(0);
require "create_excel.php";
require "db.php";

function importReturn()
	{
		$str = "<tr>";
		$str .= "<th>订单号</th>";
		$str .= "</tr>";
		$str .= importList();
		return $str;
	}

function importList()
{
	$str = "";
	$db = new db();
	$sql = "SELECT distinct orderNo FROM bfq_order ";
	$db->query($sql);
	$res = $db->getArray();
	foreach($res as $k=>$v)
	{
		$orderNo = $v['orderNo'];
		$str .= "<tr>";
		$str .= "<td style='vnd.ms-excel.numberformat: @'>{$orderNo}</td>";
		$str .= "</tr>";
	}
	return $str;
}

$str = importReturn();
create_excel2('file', $str);

echo 'ok';

