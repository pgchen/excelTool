<?php

//导出
function create_excel($title,$content)
{
	header("Pragma:public");
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Content-Type:application/force-download");
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Type:application/octet-stream");
	header("Content-Type:application/download");
	header("Content-Disposition: attachment; filename=".$title."_".date("Y-m-d").".xls");
	header("Content-Transfer-Encoding:binary");

	echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>
			<head>
				<meta http-equiv='expires' content='Mon, 06 Jan 1999 00:00:01 GMT'>
				<meta http-equiv=Content-Type content='text/html; charset=utf-8'>
				<!--[if gte mso 9]><xml>
				<x:ExcelWorkbook>
				<x:ExcelWorksheets>
					<x:ExcelWorksheet>
					<x:Name></x:Name>
					<x:WorksheetOptions>
						<x:DisplayGridlines/>
					</x:WorksheetOptions>
					</x:ExcelWorksheet>
				</x:ExcelWorksheets>
				</x:ExcelWorkbook>
				</xml><![endif]-->
			</head>
			<body>
			<table>";
		echo $content;
		echo "</table></body></html>";
}

function create_excel2($title,$content)
{
	$str = "<meta http-equiv=Content-Type content='text/html; charset=utf-8'>
			<table border=1>".$content."</table>";
	$file_path = './';
	! is_dir($file_path) && mkdir($file_path,'0777');
	if(! is_dir($file_path)) {
		echo 1;
		return;
	}

	$file_name = date('YmdHis').'.xls';
	$tmp = @file_put_contents($file_path.$file_name,$str);
	if($tmp)
	{
		echo $file_path.$file_name;
	}else
	{
		echo 2;
	}
}
?>