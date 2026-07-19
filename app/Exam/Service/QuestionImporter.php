<?php

namespace PHPEMS\App\Exam\Service;

use DOMDocument;
use DOMXPath;
use PHPEMS\App\Exam\Service\Model\Question;
use PHPEMS\App\Exam\Service\Model\QuestionType;
use PHPEMS\App\Exam\Service\Model\Relation;
use PHPEMS\Lib\Config\Site\Attach;
use PHPEMS\Lib\Utils\Converter\PanDoc;
use PHPEMS\Lib\Utils\ExcelProvider;

class QuestionImporter
{
    protected array $questionData = [];
    protected array $questionTypes = [];
    protected array $questionTypeNames = [];
    protected string $filePath;
    protected string $fileType;
    protected ?string $docxConvertName = null;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        if(!file_exists($filePath))throw new \Exception('数据文件不存在');
        $this->fileType = pathinfo($filePath, PATHINFO_EXTENSION);
    }

    protected function loadQuestionTypes():void
    {
        if(empty($this->questionTypes)) {
            $questionTypes = QuestionType::getAll();
            $this->questionTypes = array_column($questionTypes, null, 'questid');
            $this->questionTypeNames = array_column($questionTypes, null, 'questype');
            unset($questionTypes);
        }
    }

    protected function parseQuestionType(int|string $typeName):int
    {
        if($this->questionTypes[$typeName]??false)return $this->questionTypes[$typeName]['questid'];
        if($this->questionTypeNames[$typeName]?? false)return $this->questionTypeNames[$typeName]['questid'];
        throw new \Exception("题型{$typeName}不存在");
    }
    protected function importXlsxData():void
    {
        $this->questionData = ExcelProvider::importExcel($this->filePath);
        if(empty($this->questionData))throw new \Exception('数据为空');
    }

    protected function volidateXlsxData():void
    {
        $currentMaterialLineNum = 0;
        $materials = [];
        foreach ($this->questionData as $index => $row) {
            if(!$index)continue;
            $lineNum = $index + 1; // Excel 真实行号（假设第1行是表头）

            $typeName    = trim($row[0] ?? '');
            $stem        = trim($row[1] ?? '');
            $optionsStr  = trim($row[2] ?? '');
            $optionsStr = str_replace(['｜', '——',"\n"], ['|', '-',"<br />"], $optionsStr);
            $answer      = trim($row[3] ?? '');
            $describe      = trim($row[4] ?? '');
            $level = trim($row[5] ?? 1);
            $point = trim($row[6] ?? '');
            $point = str_ireplace('，',',',$point);
            $isMaterial  = trim($row[7] ?? false);
            $isChild = trim($row[8] ?? false);

            // 1. 基础必填项校验
            if (empty($typeName))throw new \Exception("第 {$lineNum} 行：【题型】不能为空。");
            $typeName = $this->parseQuestionType($typeName);
            if(!isset($this->questionTypes[$typeName]))throw new \Exception("第 {$lineNum} 行：【题型】不存在。");
            if (empty($stem))throw new \Exception("第 {$lineNum} 行：【题干】不能为空。");
            if (empty($answer) && !($isMaterial && !$isChild))throw new \Exception("第 {$lineNum} 行：【答案】不能为空。");
            if(!$point && !$isChild)throw new \Exception("第 {$lineNum} 行：【知识点ID】不能为空。");

            // 2. 选项格式校验（单选多选必须有选项，判断题自动生成选项）
            if ($isMaterial) {
                if($isChild)
                {
                    $optionsStr = $this->formatOptionString($typeName, $optionsStr, $lineNum);
                }
                else
                {
                    if ($optionsStr)throw new \Exception("第 {$lineNum} 行：【题帽题】材料不能包含选项。");
                    $hasMaterial = true;
                }
            }
            else
            {
                $hasMaterial = false;
                $optionsStr = $this->formatOptionString($typeName, $optionsStr, $lineNum);
            }
            $materials[] = [
                'questiontype'    => $typeName,
                'question'        => $stem,
                'questionselect'  => $optionsStr,
                'questionanswer'      => $answer,
                'questiondescribe'      => $describe,
                'questionlevel'       => $level,
                'questionhtml'       => [],
                'point'       => $point,
                'isMaterial'  => $isMaterial,
                'isChild'  => $isChild,
            ];
        }
        // 4. 检查末尾是否遗留了未闭合的材料题
        if ($hasMaterial && !$isChild) {
            throw new \Exception("第 {$currentMaterialLineNum} 行：检测到【材料】行，但后面没有跟随任何【子题】。");
        }
        $this->questionData = $materials;
    }

    protected function importXlsxQuestion():void
    {
        $this->importXlsxData();
        $this->volidateXlsxData();
        $parentId = 0;
        foreach ($this->questionData as $index => $row)
        {
            $lineNum = $index + 2;
            $points = explode(',',$row['point']);
            if($row['isMaterial'])
            {
                if($row['isChild'])
                {
                    if($parentId)
                    {
                        $data = [
                            'questiontype'    => $row['questiontype'],
                            'question'        => $row['question'],
                            'questionselect'  => str_ireplace('|--|', "\n", $row['questionselect']),
                            'questionanswer'      => $row['questionanswer'],
                            'questiondescribe'      => $row['questiondescribe'],
                            'questionlevel'       => $row['questionlevel'],
                            'questionhtml'       => explode('|--|',$row['questionselect']),
                            'questionparent'       => $parentId,
                            'questionisparent'       => 0,
                        ];
                        $model = Question::fillWithInit($data);
                        if(!$model->save())throw new \Exception("第 {$lineNum} 行：子题保存失败。");;
                    }
                    else throw new \Exception("第 {$lineNum} 行：【子题】行，但前面没有跟随任何【材料】行。");
                }
                else
                {
                    if($parentId)
                    {
                        $number = Question::getQuery()->where('questionparent', $parentId)->count();
                        Question::getQuery()->where('questionid', $parentId)->update([
                            'questionchildnumber'       => $number,
                        ]);
                        $parentId = 0;
                    }
                    $data = [
                        'questiontype'    => $row['questiontype'],
                        'question'        => $row['question'],
                        'questionselect'  => str_ireplace('|--|', "\n", $row['questionselect']),
                        'questionanswer'      => $row['questionanswer'],
                        'questiondescribe'      => $row['questiondescribe'],
                        'questionlevel'       => $row['questionlevel'],
                        'questionhtml'       => explode('|--|',$row['questionselect']),
                        'questionparent'       => 0,
                        'questionisparent'       => 1,
                    ];
                    $model = Question::fillWithInit($data);
                    if(!$model->save())throw new \Exception("第 {$lineNum} 行：题帽题保存失败。");;
                    $parentId = $model->questionId;
                    if(!empty($points))
                    {
                        foreach($points as $point)
                        {
                            $relation = Relation::fillWithInit([
                                'qkquestionid' => $model->questionId,
                                'qkpointid' => $point,
                                'qkstatus' => 1,
                            ]);
                            $relation->save();
                        }
                    }
                }
            }
            else
            {
                if($parentId)
                {
                    $number = Question::getQuery()->where('questionparent', $parentId)->count();
                    Question::getQuery()->where('questionid', $parentId)->update([
                        'questionchildnumber'       => $number,
                    ]);
                    $parentId = 0;
                }
                $data = [
                    'questiontype'    => $row['questiontype'],
                    'question'        => $row['question'],
                    'questionselect'  => str_ireplace('|--|', "\n", $row['questionselect']),
                    'questionanswer'      => $row['questionanswer'],
                    'questiondescribe'      => $row['questiondescribe'],
                    'questionlevel'       => $row['questionlevel'],
                    'questionhtml'       => explode('|--|',$row['questionselect']),
                    'questionparent'       => 0,
                    'questionisparent'       => 0,
                ];
                $model = Question::fillWithInit($data);
                if(!$model->save())throw new \Exception("第 {$lineNum} 行：普通试题保存失败。");;
                if(!empty($points))
                {
                    foreach($points as $point)
                    {
                        $relation = Relation::fillWithInit([
                            'qkquestionid' => $model->questionId,
                            'qkpointid' => $point,
                            'qkstatus' => 1,
                        ]);
                        $relation->save();
                    }
                }
            }
        }
        if($parentId)
        {
            $number = Question::getQuery()->where('questionparent', $parentId)->count();
            Question::getQuery()->where('questionid', $parentId)->update([
                'questionchildnumber'       => $number,
            ]);
        }
    }

    /**
     * @param string $typeName
     * @param string $optionsStr
     * @param int|string $lineNum
     * @return string
     * @throws \Exception
     */
    protected function formatOptionString(string $typeName, string $optionsStr, int|string $lineNum): string
    {
        if (!$this->questionTypes[$typeName]['questsort']) {
            if ($this->questionTypes[$typeName]['questchoice'] <= 3) {
                if (!$optionsStr) throw new \Exception("第 {$lineNum} 行：【选项】不能为空。");
                if (!str_contains($optionsStr, '|--|')) throw new \Exception("第 {$lineNum} 行：【选项】格式错误，请使用 |--| 分隔。");
            } elseif ($this->questionTypes[$typeName]['questchoice'] == 4) {
                $optionsStr = "正确|--|错误";
            }
        }
        return $optionsStr;
    }

    protected function matchQuestionLevel(int | string $level):int
    {
        return match ($level) {
            '易' => 1,
            '容易' => 1,
            '简单' => 1,
            '1' => 1,
            1 => 1,
            '中' => 2,
            '中等' => 2,
            '2' => 2,
            2 => 2,
            '难' => 3,
            '困难' => 3,
            '3' => 3,
            3 => 3,
            default => 1,
        };
    }


    /**
     * 解析包含试题的HTML内容
     * @param string $htmlContent HTML字符串
     * @return array 解析出的试题信息数组
     */
    protected function parseConvertedHtml(string $htmlContent): array
    {
        $questions = [];
        $currentQuestion = null;
        $expectedQuestionNumber = 1;
        $isParsingOptions = false;
        $inAnalysis = false;  // 解析/分析区域锁，防止误识别选项或属性

        // 题冒题组状态管理
        $isInQuestionGroup = false;
        $currentGroup = null;

        // 预处理 HTML，剥离 style/script 标签
        $htmlContent = preg_replace('/<(style|script)[^>]*>.*?<\/\1>/is', '', $htmlContent);

        $dom = new DOMDocument();
        @$dom->loadHTML(
            $htmlContent,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        $xpath = new DOMXPath($dom);
        $paragraphs = $xpath->query('//p');

        foreach ($paragraphs as $p) {
            // 获取 innerHTML 用于保留图片等标签
            $paragraphHtml = $this->getOuterHtml($p);
            $innerHtml = $this->getInnerHtml($p);
            $text = trim($p->textContent);

            // 跳过完全空白的段落（包括只有空白字符的）
            if (empty($text) && empty(trim(strip_tags($innerHtml))))
            {
                if (strpos($innerHtml, '<img') === false) {
                    continue;
                }
            }
            // 1. 识别题冒题组的开始
            if (preg_match('/【题冒题开始】/u', $paragraphHtml)) {
                if ($isInQuestionGroup) {
                    throw new \Exception("试题解析失败：检测到【题冒题开始】标签嵌套！请在第 {$expectedQuestionNumber} 题附近检查。");
                }
                if ($currentQuestion !== null) {
                    $questions[] = $currentQuestion;
                    $currentQuestion = null;
                }
                $isInQuestionGroup = true;
                $materialText = trim(preg_replace('/【题冒题开始】/u', '', $innerHtml));
                $currentGroup = [
                    'questiontype'   => '',
                    'point'          => '',
                    'material'       => !empty($materialText) ? $materialText . "\n" : '',
                    'sub_questions'  => []
                ];
                $inAnalysis = false;   // 重置解析锁
                continue;
            }

            // 2. 识别题冒题组的结束
            if (preg_match('/【题冒题结束】/u', $text)) {
                if (!$isInQuestionGroup) {
                    throw new \Exception("试题解析失败：检测到孤立的【题冒题结束】标签！");
                }
                if ($currentQuestion !== null) {
                    $currentGroup['sub_questions'][] = $currentQuestion;
                    $currentQuestion = null;
                }
                $questions[] = $currentGroup;
                $isInQuestionGroup = false;
                $currentGroup = null;
                $inAnalysis = false;
                continue;
            }

            // ★★★ 3. 判断是否为新的题目开始（必须在 $inAnalysis 拦截之前）★★★
            // 题号匹配优先级最高，新题目开始时自动退出解析模式
            if (preg_match('/^(\d+)[．.、]/u', $text, $matches)) {
                $currentQuestionNumber = (int)$matches[1];

                if ($currentQuestionNumber !== $expectedQuestionNumber) {
                    throw new \Exception("试题解析失败：检测到题号不连续！期望 [{$expectedQuestionNumber}]，实际 [{$currentQuestionNumber}]。");
                }

                // 保存上一题
                if ($currentQuestion !== null) {
                    if ($isInQuestionGroup) {
                        $currentGroup['sub_questions'][] = $currentQuestion;
                    } else {
                        $questions[] = $currentQuestion;
                    }
                }

                // 创建新题目，自动退出解析模式
                $currentQuestion = [
                    'questiontype'     => '',
                    'point'            => '',
                    'question'         => '',
                    'questionselect'   => [],
                    'questionanswer'   => '',
                    'questiondescribe' => '',
                    'questionlevel'    => '',
                ];
                // 题干使用 $paragraphHtml 保留全部标签
                $currentQuestion['question'] = $this->removeQuestionNumber($paragraphHtml, $matches[0]);
                $isParsingOptions = false;
                $inAnalysis = false;  // ★ 新题开始，自动退出解析模式
                $expectedQuestionNumber++;
                continue;
            }

            // 4. 题冒题组内的父级属性解析
            if ($isInQuestionGroup && $currentQuestion === null) {
                // 父级材料区域内理论上不会出现解析，但保留锁重置
                if (strpos($text, '题型：') === 0) {
                    if (empty($currentGroup['questiontype'])) {
                        $currentGroup['questiontype'] = $this->parseQuestionType(str_replace('题型：', '', trim($text)));
                    }
                }
                elseif (strpos($text, '知识点ID：') === 0) {
                    $currentGroup['point'] = $this->parseAndValidatePointIds($text);
                }
                elseif (strpos($text, '难易程度：') === 0) {
                    $currentGroup['questionlevel'] = $this->matchQuestionLevel(str_replace('难易程度：', '', trim($text)));
                }
                else {
                    // 材料内容使用 innerHtml 保留格式
                    $currentGroup['material'] .= $paragraphHtml . "\n";
                }
                continue;
            }

            // 前置拦截：如果还没有当前题目对象，跳过
            if ($currentQuestion === null) continue;

            // ★★★ 5. 如果当前处于解析/分析区域，所有内容直接追加到 questiondescribe ★★★
            // 此拦截在题号匹配之后，确保新题目不会被追加到解析中
            if ($inAnalysis) {
                $currentQuestion['questiondescribe'] .= $paragraphHtml . "\n";
                continue;
            }

            // 6. 识别特定字段（子题或普通题级别）
            if (strpos($text, '题型：') === 0) {
                $currentQuestion['questiontype'] = $this->parseQuestionType(str_replace('题型：', '', trim($text)));
                $isParsingOptions = false;
            }
            elseif (strpos($text, '知识点ID：') === 0) {
                $isParsingOptions = false;
                $currentQuestion['point'] = $this->parseAndValidatePointIds($text);
            }
            elseif (strpos($text, '答案：') === 0) {
                $currentQuestion['questionanswer'] = str_replace('答案：', '', $text);
                $isParsingOptions = false;
            }
            elseif (strpos($text, '难易程度：') === 0) {
                $currentQuestion['questionlevel'] = $this->matchQuestionLevel(str_replace('难易程度：', '', trim($text)));
                $isParsingOptions = false;
            }
            elseif (strpos($text, '试题解析：') === 0) {
                // 进入解析区域，锁定，后续不再尝试匹配选项或属性
                $isParsingOptions = false;
                $inAnalysis = true;
                $currentQuestion['questiondescribe'] .= $paragraphHtml . "\n";
                continue;
            }
            elseif (strpos($text, '【分析】') === 0 || strpos($text, '【解答】') === 0 || strpos($text, '【点评】') === 0) {
                // 同样锁定解析区域（部分文档可能没有"试题解析："标题行）
                $isParsingOptions = false;
                $inAnalysis = true;
                $currentQuestion['questiondescribe'] .= $paragraphHtml . "\n";
                continue;
            }
            // 7. 选项识别：只要是以字母开头的选项行，都视为新选项
            elseif (preg_match('/^[A-Z][．.、]/u', $text)) {
                $isParsingOptions = true;
                $cleanText = trim(strip_tags($text));

                // 处理一行包含多个选项的情况（如：A．xxx B．xxx）
                if (preg_match('/[A-Z][．.、].*[A-Z][．.、]/u', $cleanText)) {
                    $splitOptions = preg_split('/(?=[A-Z][．.、])/u', $cleanText, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($splitOptions as $opt) {
                        $currentQuestion['questionselect'][] = trim($opt);
                    }
                } else {
                    $currentQuestion['questionselect'][] = $cleanText;
                }
            }
            // 8. 处理选项内容跨行（只有在选项状态下且不以字母开头时才追加到上一个选项）
            elseif ($isParsingOptions && !empty($currentQuestion['questionselect'])) {
                $lastIndex = count($currentQuestion['questionselect']) - 1;
                $currentQuestion['questionselect'][$lastIndex] .= ' ' . trim(strip_tags($text));
            }
            // 9. 其他普通文本归入题干（如①②③④开头的行、图片等）
            else {
                // 如果选项还未开始，这些内容应该是题干的一部分
                $currentQuestion['question'] .= "\n" . $paragraphHtml;
            }
        }

        // 10. 循环结束后，保存最后一道题
        if ($currentQuestion !== null) {
            if ($isInQuestionGroup) {
                $currentGroup['sub_questions'][] = $currentQuestion;
            } else {
                $questions[] = $currentQuestion;
            }
        }

        // 检查题冒题标签是否闭合
        if ($isInQuestionGroup) {
            throw new \Exception("试题解析失败：文档解析完毕，但【题冒题开始】标签未闭合！");
        }

        // 完整性检测
        $this->validateDocxQuestions($questions);

        return $questions;
    }

    /**
     * 获取节点的完整 HTML（包含外层标签）
     */
    private function getOuterHtml(\DOMNode $node): string
    {
        return $node->ownerDocument->saveHTML($node);
    }

    /**
     * 从段落HTML中移除题号，保留标签结构
     */
    private function removeQuestionNumber(string $paragraphHtml, string $numberText): string
    {
        // 匹配 <p> 或 <p 属性> 开头，后跟题号的情况
        $pattern = '/(<p[^>]*>)\s*' . preg_quote($numberText, '/') . '\s*/u';

        if (preg_match($pattern, $paragraphHtml)) {
            return preg_replace($pattern, '$1', $paragraphHtml);
        }

        // 保护性处理：如果没有匹配到 <p> 标签
        return preg_replace('/^\s*' . preg_quote($numberText, '/') . '\s*/u', '', $paragraphHtml);
    }

    /**
     * 获取节点的 innerHTML
     */
    private function getInnerHtml(\DOMNode $node): string
    {
        $innerHtml = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMElement) {
                $innerHtml .= $child->ownerDocument->saveHTML($child);
            } elseif ($child instanceof \DOMText) {
                $innerHtml .= htmlspecialchars($child->textContent, ENT_QUOTES, 'UTF-8');
            } else {
                $innerHtml .= $child->textContent;
            }
        }
        return html_entity_decode($innerHtml, ENT_QUOTES, 'UTF-8');
    }

    /**
     * 解析并校验知识点ID
     */
    private function parseAndValidatePointIds(string $text): string
    {
        if (!preg_match('/知识点ID[：:]\s*(.*)/u', $text, $matches)) {
            return '';
        }
        $value = trim($matches[1]);
        $value = str_replace(['，', '、', ' '], ',', $value);
        $value = trim(preg_replace('/,+/', ',', $value), ',');
        if (empty($value)) return '';

        $ids = explode(',', $value);
        $cleanIds = [];
        foreach ($ids as $id) {
            if (!ctype_digit($id)) {
                throw new \Exception("试题解析失败：检测到非法的知识点ID [{$id}]！必须为纯数字。");
            }
            $cleanIds[] = (string)intval($id);
        }
        return implode(',', $cleanIds);
    }

    /**
     * 完整性检测
     */
    private function validateDocxQuestions(array $questions): void
    {
        $globalQuestionNumber = 1;
        foreach ($questions as $index => $item) {
            // 判断是否为题冒题组（通过 sub_questions 键判断）
            if (array_key_exists('sub_questions', $item)) {
                // === 题冒题父级校验 ===
                if (empty(trim($item['questiontype']))) {
                    throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber}的题冒题组缺少【题型】（如：材料题、阅读理解），请检查Word排版。");
                }
                if (empty(trim($item['material']))) {
                    throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber}的题冒题组缺少【材料内容】，请检查Word排版。");
                }
                if (empty(trim($item['point']))) {
                    throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber}的题冒题组缺少【知识点ID】，请检查Word排版。");
                }

                // === 题冒题子题校验 ===
                if (empty($item['sub_questions'])) {
                    throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber} 的题冒题组内没有任何子题，请检查Word排版。");
                }
                foreach ($item['sub_questions'] as $subIndex => $subItem) {
                    $subQNum = $subIndex + 1;
                    if (empty(trim($subItem['questiontype']))) {
                        throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber} 的题冒题组内的第 {$subQNum} 道子题缺少【题型】，请检查Word排版。");
                    }
                    if (empty(trim($subItem['question']))) {
                        throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber} 的题冒题组内的第 {$subQNum} 道子题缺少【题干】，请检查Word排版。");
                    }
                    if (empty(trim($subItem['questionanswer']))) {
                        throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber} 的题冒题组内的第 {$subQNum} 道子题缺少【答案】，请检查Word排版。");
                    }
                    // 【注意】：子题不需要校验 point，直接跳过
                    $globalQuestionNumber ++;
                }
            } else {
                // === 普通试题校验 ===
                if (empty(trim($item['questiontype']))) {
                    throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber}的普通试题缺少【题型】，请检查Word排版。");
                }
                if (empty(trim($item['question']))) {
                    throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber}的普通试题缺少【题干】，请检查Word排版。");
                }
                if (empty(trim($item['point']))) {
                    throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber}的普通试题缺少【知识点ID】，请检查Word排版。");
                }
                if (empty(trim($item['questionanswer']))) {
                    throw new \Exception("试题完整性校验失败：序号 {$globalQuestionNumber}的普通试题缺少【答案】，请检查Word排版。");
                }
                $globalQuestionNumber ++;
            }
        }
    }

    protected function importDocxQuestion():void
    {
        $converter = new PanDoc();
        $this->docxConvertName = bin2hex(random_bytes(16));
        $outPath = DI(Attach::class)->convertSavePath.'/'.$this->docxConvertName.'.html';
        $converter->convert($this->filePath,$outPath);
        $content = file_get_contents($outPath);
        unlink($outPath);
        $content = $this->parseConvertedHtml($content);
        $lineNum = 1;
        foreach($content as $row)
        {
            $points = explode(',',$row['point']);
            if($row['material']??false) {
                $data = [
                    'questiontype' => $row['questiontype'],
                    'question' => $row['material'],
                    'questionselect' => '',
                    'questionanswer' => '',
                    'questiondescribe' => '',
                    'questionlevel' => $row['questionlevel'],
                    'questionhtml' => '',
                    'questionparent' => 0,
                    'questionisparent' => 1,
                    'questionchildnumber' => count($row['sub_questions']),
                ];
                $model = Question::fillWithInit($data);
                if (!$model->save()) throw new \Exception("第 {$lineNum} 行：题帽题保存失败。");;
                $parentId = $model->questionId;
                foreach ($row['sub_questions'] as $subIndex => $subRow) {
                    $data = [
                        'questiontype' => $subRow['questiontype'],
                        'question' => $subRow['question'],
                        'questionselect' => implode("\n", $subRow['questionselect']),
                        'questionanswer' => $subRow['questionanswer'],
                        'questiondescribe' => $subRow['questiondescribe'],
                        'questionlevel' => $subRow['questionlevel'],
                        'questionhtml' => $subRow['questionselect'],
                        'questionparent' => $parentId,
                    ];
                    $subModel = Question::fillWithInit($data);
                    if (!$subModel->save()) throw new \Exception("序号 {$lineNum} 试题保存失败。");
                    $lineNum ++;
                }
                if (!empty($points)) {
                    foreach ($points as $point) {
                        $relation = Relation::fillWithInit([
                            'qkquestionid' => $model->questionId,
                            'qkpointid' => $point,
                            'qkstatus' => 1,
                        ]);
                        $relation->save();
                    }
                }
            }
            else
            {
                $data = [
                    'questiontype'    => $row['questiontype'],
                    'question'        => $row['question'],
                    'questionselect' => implode("\n", $row['questionselect']),
                    'questionanswer'      => $row['questionanswer'],
                    'questiondescribe'      => $row['questiondescribe'],
                    'questionlevel'       => $row['questionlevel'],
                    'questionhtml'       => $row['questionselect'],
                    'questionparent'       => 0,
                    'questionisparent'       => 0,
                ];
                $model = Question::fillWithInit($data);
                if(!$model->save())throw new \Exception("序号 {$lineNum} 普通试题保存失败。");;
                if(!empty($points))
                {
                    foreach($points as $point)
                    {
                        $relation = Relation::fillWithInit([
                            'qkquestionid' => $model->questionId,
                            'qkpointid' => $point,
                            'qkstatus' => 1,
                        ]);
                        $relation->save();
                    }
                }
                $lineNum ++;
            }
        }
    }

    protected function cleanFailConvertFiles():void
    {
        if($this->docxConvertName)
        {
            $convertPath = DI(Attach::class)->convertSavePath.'/'.$this->docxConvertName;
            if (is_dir($convertPath)) {
                $files = glob($convertPath . '/*');
                foreach ($files as $file) if (is_file($file)) @unlink($file);
                @rmdir($convertPath);
            }
            $this->docxConvertName = null;
        }
    }

    /**
     * @throws \Exception
     */
    public function importQuestion():void
    {
        try {
            $this->loadQuestionTypes();

            match ($this->fileType) {
                'xlsx' => $this->importXlsxQuestion(),
                'docx' => $this->importDocxQuestion(),
                'json' => $this->importJSONQuestion(),
                default => throw new \Exception('不支持的文件格式'),
            };
        } catch (\Exception $e) {
            $this->cleanFailConvertFiles();
            throw new \Exception($e->getMessage());
        } finally {
            unlink($this->filePath);
        }
    }

    protected function importJSONQuestion():void
    {
        $JSON = file_get_contents($this->filePath);
        $JSON = json_decode($JSON,true,512,JSON_THROW_ON_ERROR);
        if(empty($JSON))throw new \Exception('JSON文件格式错误');
        foreach($JSON as $index => $question)
        {
            if(!$question['questiontype'])throw new \Exception('第'.($index + 1).'题题型不能为空');
            if (!$question['question'])throw new \Exception('第'.($index + 1).'题题干不能为空');
            if(!$question['questionlevel'])throw new \Exception('第'.($index + 1).'题难度不能为空');
            if(!$question['questionpoint'])throw new \Exception('第'.($index + 1).'题知识点ID不能为空');
            if($question['children']??false){
                if(empty($question['children']))throw new \Exception('第'.($index + 1).'题子题不能为空');
                foreach($question['children'] as $childIndex => $childQuestion)
                {
                    if(!$childQuestion['questiontype'])throw new \Exception('第'.($index + 1).'题子题'.($childIndex + 1).'题题型不能为空');
                    if (!$childQuestion['question'])throw new \Exception('第'.($index + 1).'题子题'.($childIndex + 1).'题题干不能为空');
                    if(!$childQuestion['questionanswer'])throw new \Exception('第'.($index + 1).'题子题'.($childIndex + 1).'题答案不能为空');
                    $questionType = $this->parseQuestionType($childQuestion['questiontype']);
                    if(!$this->questionTypes[$questionType]['questsort'])
                    {
                        if($this->questionTypes[$questionType]['questchoice'] <= 3)
                        {
                            if(!is_array($childQuestion['questionselect']??null) && empty($childQuestion['questionselect']))
                            {
                                throw new \Exception('第'.($index + 1).'题子题'.($childIndex + 1).'题选项不能为空');
                            }
                        }
                    }
                }
            }
            else
            {
                if(!$question['questionanswer'])throw new \Exception('第'.($index + 1).'题答案不能为空');
                $questionType = $this->parseQuestionType($question['questiontype']);
                if(!$this->questionTypes[$questionType]['questsort'])
                {
                    if($this->questionTypes[$questionType]['questchoice'] <= 3)
                    {
                        if(!is_array($question['questionselect']??null) && empty($question['questionselect']))
                        {
                            throw new \Exception('第'.($index + 1).'题选项不能为空');
                        }
                    }
                }
            }
        }
        foreach ($JSON as $question)
        {
            $data = [
                'questiontype' => $this->parseQuestionType($question['questiontype']),
                'question' => $question['question'],
                'questionlevel'       => $this->matchQuestionLevel($question['questionlevel']),
                'questionhtml'       => $question['questionselect']??[],
            ];
            if($question['children']??false)
            {
                $data['questionisparent'] = 1;
                $data['questionchildnumber'] = count($question['children']);
                $model = Question::fillWithInit($data);
                $model->save();
                $parentId = $model->questionId;
                foreach($question['children'] as $childQuestion)
                {
                    $childData = [
                        'questiontype' => $this->parseQuestionType($childQuestion['questiontype']),
                        'question' => $childQuestion['question'],
                        'questionlevel'       => $this->matchQuestionLevel($childQuestion['questionlevel']),
                    ];
                    if(!$this->questionTypes[$childData['questiontype']]['questsort'] && $this->questionTypes[$childData['questiontype']]['questchoice'] <= 4)
                    {
                        if($this->questionTypes[$childData['questiontype']]['questchoice'] == 4)
                        {
                            $childData['questionhtml'] = ['正确','错误'];
                        }
                        else
                        {
                            $childData['questionhtml'] = $childQuestion['questionselect'];
                        }
                    }
                    $childData['questionparent'] = $parentId;
                    $childData['questionselect'] = implode("\n",$childQuestion['questionselect']);
                    $childData['questionanswer'] = $childQuestion['questionanswer'];
                    $childData['questiondescribe'] = $childQuestion['questiondescribe']??null;
                    $model = Question::fillWithInit($childData);
                    $model->save();
                }
            }
            else
            {
                if(!$this->questionTypes[$data['questiontype']]['questsort'] && $this->questionTypes[$data['questiontype']]['questchoice'] <= 4)
                {
                    if($this->questionTypes[$data['questiontype']]['questchoice'] == 4)
                    {
                        $data['questionhtml'] = ['正确','错误'];
                    }
                    else
                    {
                        $data['questionhtml'] = $question['questionselect'];
                    }
                }
                $data['questionselect'] = implode("\n",$question['questionselect']);
                $data['questionanswer'] = $question['questionanswer'];
                $data['questiondescribe'] = $question['questiondescribe']??null;
                $model = Question::fillWithInit($data);
                $model->save();
                $parentId = $model->questionId;
            }
            unset($model);
            $points = explode(",",trim($question['questionpoint']));
            if(!empty($points))
            {
                foreach ($points as $point) {
                    $point = intval($point);
                    if($point)
                    {
                        Relation::fillWithInit([
                            'qkquestionid' => $parentId,
                            'qkpointid' => $point,
                            'qkstatus' => 1
                        ])->save();
                    }
                }
            }
        }
    }
}