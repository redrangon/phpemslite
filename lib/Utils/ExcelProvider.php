<?php

namespace PHPEMS\Lib\Utils;

use Exception;
use PHPEMS\Lib\Utils\Office\Xlsx\Reader;
use PHPEMS\Lib\Utils\Office\Xlsx\Writer;

class ExcelProvider
{
    static public function exportExcel($data, $fileName = ''):bool
    {
        try{
            $writer = new Writer();
            $writer->writeSheet($data,'Sheet1');
            $writer->writeToFile($fileName);
            return true;
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    static public function importExcel($filePath):array
    {
        try {
            $reader = new Reader($filePath);
            $sheets = $reader->getSheetNames();
            $firstSheet = array_key_first($sheets);
            return $reader->getSheetData($firstSheet);
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}