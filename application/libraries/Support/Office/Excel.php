<?php
/*
 * With reference to https://github.com/PHPOffice/PHPExcel
 *
 * */
namespace Lib\Support\Office;

use \PHPExcel_IOFactory;
use \PHPExcel_Cell;
use \PHPExcel;

class Excel
{
	
	public function __construct()
    {
       
    }
	
	public function reader( String $flie, String $type = 'Excel2007' )
	{
		if ( empty($flie) || !file_exists($flie) )
		{
			throw new Exception("Failed to read file");
		}
		
		$objReader = PHPExcel_IOFactory::createReader($type);
		$objPHPExcel = $objReader->load($flie);
		
		$objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();	// 总行数
		
		$highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);	// 总列数
        
        $results = array();
		
		for ( $i = 1; $i <= $highestRow; $i++ )
		{
			$strs = array();
			
			for ( $col = 0; $col < $highestColumnIndex; $col++ )
            {
                $strs[$col] = $objWorksheet->getCellByColumnAndRow($col, $i)->getValue();
            }

			array_push($results, $strs);
		}
		
		return $results;	
	}
	
	public function writer( $data = array(), String $flieName ='Excel', String $type = 'Excel2007',  String $savePath = 'download')
	{
		$objPHPExcel = new PHPExcel();
		
		// $objPHPExcel->getActiveSheet()->setCellValue('D1', PHPExcel_Shared_Date::PHPToExcel(time()));
		$objPHPExcel->getActiveSheet()->fromArray($data);
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $type);

		$savePath = $savePath.'/'. $flieName. '.xlsx';
		$objWriter->save($savePath);

		return $flieName. '.xlsx';
	}
	
}