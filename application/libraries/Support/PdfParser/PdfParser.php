<?php
/*
 * With reference to https://github.com/smalot/pdfparser
 *
 * */
namespace Lib\Support\PdfParser;

use \Smalot\PdfParser\Parser;

class PdfParser
{
	
	public function __construct()
    {
        
    }
		
	public function getTextArray($filepath)
	{
		$text = array();

		$pdfParser = new Parser();
		$pdf = $pdfParser->parseFile($filepath);

		$pages = $pdf->getPages();

		foreach ($pages as $page) 
		{
			$pageText = $page->getText();
		    array_push($text, $pageText);
		}

		return array_filter($text);
	}

	/*
	 // eg 1: Parse pdf file and build necessary objects.
	$text = $pdf->getText();
	echo $text;

	// eg 2 : Retrieve all pages from the pdf file.
	$pages = $pdf->getPages();
	foreach ($pages as $page) {
	    echo $page->getText();
	}

	// eg 3: Retrieve all details from the pdf file.
	$details  = $pdf->getDetails();
	// // Loop over each property to extract values (string or array).
	foreach ($details as $property => $value) {
	    if (is_array($value)) {
	        $value = implode(', ', $value);
	    }
	    echo $property . ' => ' . $value . "\n";
	}*/

}