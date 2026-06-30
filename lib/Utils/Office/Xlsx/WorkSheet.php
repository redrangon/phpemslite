<?php

namespace PHPEMS\Lib\Utils\Office\Xlsx;

class WorkSheet
{
    protected Reader $workbook;
    public string $sheetName;
    protected ?array $data = null;
    public int $colCount = 0;
    public int $rowCount = 0;
    protected array $config;

    public function __construct(\SimpleXMLElement $xml, string $sheetName, Reader $workbook) {
        $this->config = $workbook->config;
        $this->sheetName = $sheetName;
        $this->workbook = $workbook;
        $this->parse($xml);
    }

    // returns an array of the data from the sheet
    public function getData(): array {
        return $this->data;
    }

    protected function parse(\SimpleXMLElement $xml): void {
        $this->parseDimensions($xml->dimension);
        $this->parseData($xml->sheetData);
    }

    protected function parseDimensions(\SimpleXMLElement $dimensions): void {
        $range = (string) $dimensions['ref'];
        $cells = explode(':', $range);
        $maxValues = $this->getColumnIndex($cells[1]);
        $this->colCount = $maxValues[0] + 1;
        $this->rowCount = $maxValues[1] + 1;
    }

    protected function parseData(\SimpleXMLElement $sheetData): void {
        $rows = [];
        $curR = 0;
        $lastDataRow = -1;
        foreach ($sheetData->row as $row) {
            $rowNum = (int)$row['r'];
            if($rowNum != ($curR + 1)) {
                $missingRows = $rowNum - ($curR + 1);
                for($i = 0; $i < $missingRows; $i++) {
                    $rows[$curR] = array_pad([], $this->colCount, null);
                    $curR++;
                }
            }
            $curC = 0;
            $rowData = [];
            foreach ($row->c as $c) {
                list($cellIndex,) = $this->getColumnIndex((string) $c['r']);
                if($cellIndex !== $curC) {
                    $missingCols = $cellIndex - $curC;
                    for($i = 0; $i < $missingCols; $i++) {
                        $rowData[$curC] = null;
                        $curC++;
                    }
                }
                $val = $this->parseCellValue($c);
                if(!is_null($val)) {
                    $lastDataRow = $curR;
                }
                $rowData[$curC] = $val;
                $curC++;
            }
            $rows[$curR] = array_pad($rowData, $this->colCount, null);
            $curR++;
        }
        if($this->config['removeTrailingRows']) {
            $this->data = array_slice($rows, 0, $lastDataRow + 1);
            $this->rowCount = count($this->data);
        } else {
            $this->data = $rows;
        }
    }

    protected function getColumnIndex(string $cell = 'A1'): array {
        if (preg_match("/([A-Z]+)(\d+)/", $cell, $matches)) {
            $col = $matches[1];
            $row = $matches[2];
            $colLen = strlen($col);
            $index = 0;

            for ($i = $colLen - 1; $i >= 0; $i--) {
                $index += (ord($col[$i]) - 64) * pow(26, $colLen - $i - 1);
            }
            return [$index - 1, $row - 1];
        }
        throw new \Exception("Invalid cell index");
    }

    protected function parseCellValue(\SimpleXMLElement $cell): mixed {
        // $cell['t'] is the cell type
        switch ((string)$cell["t"]) {
            case "s": // Value is a shared string
                if ((string)$cell->v != '') {
                    $value = $this->workbook->getSharedStrings()[intval($cell->v)];
                } else {
                    $value = '';
                }
                break;
            case "b": // Value is boolean
                $value = (string)$cell->v;
                if ($value == '0') {
                    $value = false;
                } else if ($value == '1') {
                    $value = true;
                } else {
                    $value = (bool)$cell->v;
                }
                break;
            case "inlineStr": // Value is rich text inline
                $value = self::parseRichText($cell->is);
                break;
            case "e": // Value is an error message
                if ((string)$cell->v != '') {
                    $value = (string)$cell->v;
                } else {
                    $value = '';
                }
                break;
            default:
                if(!isset($cell->v)) {
                    return null;
                }
                $value = (string)$cell->v;

                // Check for numeric values
                if (is_numeric($value)) {
                    if ($value == (int)$value) $value = (int)$value;
                    elseif ($value == (float)$value) $value = (float)$value;
                    elseif ($value == (double)$value) $value = (double)$value;
                }
        }
        return $value;
    }

    // returns the text content from a rich text or inline string field
    public static function parseRichText(?\SimpleXMLElement $is = null): string {
        $value = [];
        if (isset($is->t)) {
            $value[] = (string)$is->t;
        } else {
            foreach ($is->r as $run) {
                $value[] = (string)$run->t;
            }
        }
        return implode(' ', $value);
    }
}