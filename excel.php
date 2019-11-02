<?php

require 'vendor/autoload.php';

$i = 0;
$final = array();
$inputFileType = 'Xlsx';
$inputFileName = 'task.xlsx';
$words_to_removed = array('with', 'With', 'bank', 'Bank', 'flash', 'Flash');

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$spreadsheet = $reader->load($inputFileName);
$sheet = $spreadsheet->getActiveSheet()->toArray();

foreach ($sheet as $row) {
    $old_title = $row[1];
    if ($old_title != 'Title') {
        $product_title = $row[1];
        foreach ($words_to_removed as $word) {
            $product_title = str_replace($word, '', $product_title);
        }
        preg_match("/(\d+) GB/", $product_title, $matches);
        if (preg_match("/(\d+) GB/", $product_title)) {
            $pos = stripos($product_title, $matches[0]) . ' ';
            $edit = str_replace(' ', '', $matches[0]) . ' ';
            $len = strlen($matches[0]);
            $product_title = substr_replace($product_title, $edit, $pos, $len);
        }

        array_push($final, array(++$i, $old_title, $product_title));
    }
}

$output = fopen("php://output", 'w') or die("Can't open php://output");
header("Content-Type:application/csv");
header("Content-Disposition:attachment;filename=PHP_Task_Solution.csv");
fputcsv($output, array('Product Id', 'Title', 'New Title'));
foreach ($final as $product) {
    fputcsv($output, $product);
}
fclose($output) or die("Can't close php://output");
