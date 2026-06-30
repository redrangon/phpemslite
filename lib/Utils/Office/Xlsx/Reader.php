<?php

namespace PHPEMS\Lib\Utils\Office\Xlsx;

class Reader
{
    protected array $sheets = [];
    protected array $sharedStrings = [];
    protected ?array $sheetInfo = null;
    protected ?\ZipArchive $zip = null;
    public array $config = [
        'removeTrailingRows' => true
    ];

    // XML schemas
    const SCHEMA_OFFICEDOCUMENT = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument';
    const SCHEMA_RELATIONSHIP = 'http://schemas.openxmlformats.org/package/2006/relationships';
    const SCHEMA_OFFICEDOCUMENT_RELATIONSHIP = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    const SCHEMA_SHAREDSTRINGS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings';
    const SCHEMA_WORKSHEETRELATION = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet';

    public function __construct(string $filePath, array $config = []) {
        $this->config = array_merge($this->config, $config);
        $this->zip = new \ZipArchive();
        $status = $this->zip->open($filePath);
        if($status === true) {
            $this->parse();
        } else {
            throw new \Exception("Failed to open $filePath with zip error code: $status");
        }
    }

    // get a file from the zip
    protected function getEntryData(string $name): string {
        $data = $this->zip->getFromName($name);
        if($data === false) {
            throw new \Exception("File $name does not exist in the Excel file");
        }
        return $data;
    }

    // extract the shared string and the list of sheets
    protected function parse(): void {
        $sheets = [];
        $relationshipsXML = simplexml_load_string($this->getEntryData("_rels/.rels"));
        foreach($relationshipsXML->Relationship as $rel) {
            if($rel['Type'] == self::SCHEMA_OFFICEDOCUMENT) {
                $workbookDir = dirname($rel['Target']) . '/';
                $workbookXML = simplexml_load_string($this->getEntryData($rel['Target']));
                foreach($workbookXML->sheets->sheet as $sheet) {
                    $r = $sheet->attributes('r', true);
                    $sheets[(string)$r->id] = [
                        'sheetId' => (int)$sheet['sheetId'],
                        'name' => (string)$sheet['name']
                    ];
                }
                $workbookRelationsXML = simplexml_load_string($this->getEntryData($workbookDir . '_rels/' . basename($rel['Target']) . '.rels'));
                foreach($workbookRelationsXML->Relationship as $wrel) {
                    switch($wrel['Type']) {
                        case self::SCHEMA_WORKSHEETRELATION:
                            $sheets[(string)$wrel['Id']]['path'] = $workbookDir . (string)$wrel['Target'];
                            break;
                        case self::SCHEMA_SHAREDSTRINGS:
                            $sharedStringsXML = simplexml_load_string($this->getEntryData($workbookDir . (string)$wrel['Target']));
                            foreach($sharedStringsXML->si as $val) {
                                if(isset($val->t)) {
                                    $this->sharedStrings[] = (string)$val->t;
                                } elseif(isset($val->r)) {
                                    $this->sharedStrings[] = WorkSheet::parseRichText($val);
                                }
                            }
                            break;
                    }
                }
            }
        }
        $this->sheetInfo = [];
        foreach($sheets as $rid => $info) {
            $this->sheetInfo[$info['name']] = [
                'sheetId' => $info['sheetId'],
                'rid' => $rid,
                'path' => $info['path']
            ];
        }
    }

    // returns an array of sheet names, indexed by sheetId
    public function getSheetNames(): array {
        $res = [];
        foreach($this->sheetInfo as $sheetName => $info) {
            $res[$info['sheetId']] = $sheetName;
        }
        return $res;
    }

    public function getSheetCount(): int {
        return count($this->sheetInfo);
    }

    // get shared strings array
    public function getSharedStrings(): array {
        return $this->sharedStrings;
    }

    /**
     * 获取工作表数据
     * @param string|int $sheetNameOrId 工作表名称（如 'Sheet1'）或工作表 ID（从 0 开始的整数）
     * @return array 二维数组，包含工作表的所有单元格数据
     * @throws \Exception 当工作表不存在时抛出异常
     */
    public function getSheetData(string|int $sheetNameOrId): array {
        $sheet = $this->getSheet($sheetNameOrId);
        return $sheet->getData();
    }

    // instantiates a sheet object (if needed) and returns the sheet object
    public function getSheet(string|int $sheet): WorkSheet {
        if(is_numeric($sheet)) {
            $sheet = $this->getSheetNameById($sheet);
        } elseif(!is_string($sheet)) {
            throw new \Exception("Sheet must be a string or a sheet Id");
        }
        if(!array_key_exists($sheet, $this->sheets)) {
            $this->sheets[$sheet] = new WorkSheet($this->getSheetXML($sheet), $sheet, $this);
        }
        return $this->sheets[$sheet];
    }

    public function getSheetNameById(int $sheetId): string
    {
        foreach($this->sheetInfo as $sheetName => $sheetInfo) {
            if($sheetInfo['sheetId'] === $sheetId) {
                return $sheetName;
            }
        }
        throw new \Exception("Sheet ID $sheetId does not exist in the Excel file");
    }

    protected function getSheetXML(string $name): \SimpleXMLElement {
        return simplexml_load_string($this->getEntryData($this->sheetInfo[$name]['path']));
    }

    // converts an Excel date field (a number) to a unix timestamp (granularity: seconds)
    public static function toUnixTimeStamp(mixed $excelDateTime): mixed {
        if(!is_numeric($excelDateTime)) {
            return $excelDateTime;
        }
        $d = floor($excelDateTime); // seconds since 1900
        $t = $excelDateTime - $d;
        return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
    }
}