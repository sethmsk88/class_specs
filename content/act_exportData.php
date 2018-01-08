<?php
require_once __DIR__ . '\..\vendor\autoload.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("HRODT Class Specs App")
							 ->setLastModifiedBy("")
							 ->setTitle("Data Export")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("");

$objPHPExcel->getDefaultStyle()
			->getNumberFormat()
			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


$dataFields = array_keys($_POST); // Get posted data fields
$letters = range('A', 'Z'); // Create array of letters from 'A' to 'Z'

// Add column headers
foreach ($dataFields as $dataField) {
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(current($letters) . '1', $dataField);
    next($letters); // Move letters array pointer to next letter
}
reset($letters); // Move letters array pointer back to the first letters

// Get all class specs and cupa codes
$stmt = $conn->prepare("
	select c.*, d.letter
	from hrodt.class_specs c
	left join hrodt.departments d
		on d.id = c.DeptID
	where Active = 1
	order by JobCode
");
$stmt->execute();
$result = $stmt->get_result();

$row_i = 2;
while ($row = $result->fetch_assoc()) {

	// Add data for each column in this row
	foreach ($dataFields as $dataField) {
		$data = $row[$dataField];

		// Add department letter to Job Code
		if ($dataField == 'JobCode') {
			$data .= $row['letter'];
		}

		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue(current($letters) . $row_i, $data);

		next($letters); // Move letters array pointer to next letter
	}
	reset($letters); // Move letters array pointer back to the first letter
	$row_i++;
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Class_Cupa_Codes.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>