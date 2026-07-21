<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\ExamPaper;
use PHPEMS\App\Exam\Service\Model\QuestionType;
use PHPEMS\Lib\Config\Site\Site;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Tpl\TplProvider;
use PHPEMS\Lib\Utils\Converter\PanDoc;

class Paper extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'all' => 'All',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add',
            'paper' => 'Paper',
            'question' => 'Question',
            'export' => 'Export',
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [];
        return $flows[$action]??self::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    public function Export():void
    {
        $id = $this->request->paperid??null;
        $paper = ExamPaper::find($id);
        $paper->examQuestions = json_decode($paper->examQuestions,true);
        $questions = ExamService::drawSelfPaper($paper);
        $paperHtml = '';
        $questionTypes = array_column(QuestionType::getAll(),'questype','questid');
        $questionIndex = 1;
        foreach($questions as $questionType => $row){
            $paperHtml .= "<h5 style=\"font-size: 14pt;\">$questionTypes[$questionType]</h5>\n";
            if($row['questions']??false)
            {
                foreach($row['questions'] as $question){
                    $paperHtml .= "<p>第{$questionIndex}题</p><div>{$question['question']}</div>\n";
                    foreach($question['questionhtml'] as $selector){
                        $paperHtml .= "<div>{$selector}</div>\n";
                    }
                    $questionIndex++;
                }
            }
            if($row['rowsQuestions']??false)
            {
                foreach($row['rowsQuestions'] as $rows){
                    $paperHtml .= "<div>{$question['question']}</div>\n";
                    foreach($rows['children'] as $question){
                        $paperHtml .= "<p>第{$questionIndex}题 【{$questionTypes[$question['questiontype']]}】</p><div>{$question['question']}</div>\n";
                        foreach($question['questionhtml'] as $selector){
                            $paperHtml .= "<div>{$selector}</div>\n";
                        }
                        $questionIndex++;
                    }
                }
            }
        }
        $paperHtml = <<<EOT
        <!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
          <meta charset="utf-8" />
          <meta name="generator" content="pandoc" />
          <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
          <title>81bf204717057ab7fd6fb9b0591b2546</title>
          <style>
            /* Default styles provided by pandoc.
            ** See https://pandoc.org/MANUAL.html#variables-for-html for config info.
            */
            html {
                color: #1a1a1a;
                background-color: #fdfdfd;
            }
            body {
                margin: 0 auto;
              max-width: 36em;
              padding-left: 50px;
              padding-right: 50px;
              padding-top: 50px;
              padding-bottom: 50px;
              hyphens: auto;
              overflow-wrap: break-word;
              text-rendering: optimizeLegibility;
              font-kerning: normal;
            }
            @media (max-width: 600px) {
                body {
                    font-size: 0.9em;
                padding: 12px;
              }
              h1 {
                    font-size: 1.8em;
              }
            }
            @media print {
                html {
                    background-color: white;
              }
              body {
                    background-color: transparent;
                color: black;
                font-size: 12pt;
              }
              p, h2, h3 {
                    orphans: 3;
                    widows: 3;
                }
              h2, h3, h4 {
                    page-break-after: avoid;
              }
            }
            p {
                margin: 1em 0;
            }
            a {
                color: #1a1a1a;
            }
            a:visited {
                color: #1a1a1a;
            }
            img {
                max-width: 100%;
            }
            svg {
                height: auto;
                max-width: 100%;
            }
            h1, h2, h3, h4, h5, h6 {
                margin-top: 1.4em;
            }
            h5, h6 {
                font-size: 1em;
              font-style: italic;
            }
            h6 {
                font-weight: normal;
            }
            ol, ul {
                padding-left: 1.7em;
              margin-top: 1em;
            }
            li > ol, li > ul {
                margin-top: 0;
            }
            blockquote {
                margin: 1em 0 1em 1.7em;
              padding-left: 1em;
              border-left: 2px solid #e6e6e6;
              color: #606060;
            }
            code {
                white-space: pre-wrap;
              font-family: Menlo, Monaco, Consolas, 'Lucida Console', monospace;
              font-size: 85%;
              margin: 0;
              hyphens: manual;
            }
            pre {
                margin: 1em 0;
              overflow: auto;
            }
            pre code {
                padding: 0;
                overflow: visible;
                overflow-wrap: normal;
            }
            .sourceCode {
                background-color: transparent;
             overflow: visible;
            }
            hr {
                border: none;
                border-top: 1px solid #1a1a1a;
              height: 1px;
              margin: 1em 0;
            }
            table {
                margin: 1em 0;
              border-collapse: collapse;
              width: 100%;
              overflow-x: auto;
              display: block;
              font-variant-numeric: lining-nums tabular-nums;
            }
            table caption {
                margin-bottom: 0.75em;
            }
            tbody {
                margin-top: 0.5em;
              border-top: 1px solid #1a1a1a;
              border-bottom: 1px solid #1a1a1a;
            }
            th {
                border-top: 1px solid #1a1a1a;
              padding: 0.25em 0.5em 0.25em 0.5em;
            }
            td {
                padding: 0.125em 0.5em 0.25em 0.5em;
            }
            header {
                margin-bottom: 4em;
              text-align: center;
            }
            #TOC li {
              list-style: none;
            }
            #TOC ul {
        padding-left: 1.3em;
        }
        #TOC > ul {
        padding-left: 0;
            }
            #TOC a:not(:hover) {
              text-decoration: none;
            }
            span.smallcaps{font-variant: small-caps;}
            div.columns{display: flex; gap: min(4vw, 1.5em);}
            div.column{flex: auto; overflow-x: auto;}
            div.hanging-indent{margin-left: 1.5em; text-indent: -1.5em;}
            /* The extra [class] is a hack that increases specificity enough to
               override a similar rule in reveal.js */
            ul.task-list[class]{list-style: none;}
            ul.task-list li input[type="checkbox"] {
            font-size: inherit;
              width: 0.8em;
              margin: 0 0.8em 0.2em -1.6em;
              vertical-align: middle;
            }
          </style>
        </head>
        <body>
        $paperHtml
        </body>
        </html>
        EOT;
        $converter = new PanDoc();
        $content = $converter->convertToString($paperHtml,'docx',[
            'base_url' => 'http://127.0.0.1/phpemsvue/',
            'title' => $paper->exam
        ]);
        file_put_contents("php://output", $content);
    }

    /**
     * 添加操作
     */
    public function Add():array | Error
    {
        // 获取请求数据
        $user = $this->request->getUser();
        $data = [
            'exam' => $this->request->exam??null,
            'exampassmark' => $this->request->exampassmark??null,
            'examquestions' => $this->request->examquestions??'',
            'examscalemodel' => $this->request->examscalemodel??0,
            'examsetting' => $this->request->examsetting??null,
            'examsubject' => $this->request->examsubject??null,
            'examtotalscore' => $this->request->examtotalscore??100,
            'examtotaltime' => $this->request->examtotaltime??60,
            'examtype' => $this->request->examtype??null,
            'examtime' => TIME,
            'examauthor' => $user->username??null
        ];

        // TODO: 实现添加逻辑
        // 示例：
        $model = ExamPaper::fillWithInit($data);
        $model->examsetting = is_array($data['examsetting'])?json_encode($data['examsetting'],JSON_UNESCAPED_UNICODE):'';
        $model->examquestions = is_array($data['examquestions'])?json_encode($data['examquestions'],JSON_UNESCAPED_UNICODE):'';
        $result = $model->toValidate();
        if($result !== true)return $result;
        if(!$model->save()) return error(['error' => '添加失败']);
        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function modify():array|Error
    {
        // TODO: 实现修改逻辑
        // 示例：
        $id = $this->request->examid;
        $model = ExamPaper::find($id);
        if(!$model->examid) return error(['error' => '记录不存在']);
        $data = [
            'exam' => $this->request->exam??null,
            'exampassmark' => $this->request->exampassmark??null,
            'examquestions' => $this->request->examquestions??null,
            'examscalemodel' => $this->request->examscalemodel??null,
            'examsetting' => $this->request->examsetting??null,
            'examsubject' => $this->request->examsubject??null,
            'examtotalscore' => $this->request->examtotalscore??null,
            'examtotaltime' => $this->request->examtotaltime??null,
            'examscore' => $this->request->examscore??null,
        ];
        $data = array_filter($data, function($item){
            return !is_null($item);
        });
        foreach ($data as $key => $value) {
            if(is_array($value)){
                $model->$key = json_encode($value,JSON_UNESCAPED_UNICODE);
            }
            else $model->$key = $value;
        }
        if(!$model->save()) return error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    /**
     * 删除操作
     */
    public function Delete():array|Error
    {
        // TODO: 实现删除逻辑
        // 示例：
        $ids = $this->request->ids;
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        ExamPaper::getQuery()->whereIn('examid', $ids)->delete();

        return ['msg' => '删除成功'];
    }

    public function Question():array | Error
    {
        $paperId = $this->request->paperid;
        $paper = ExamPaper::find($paperId);
        if(!$paper->examid || ($paper->examtype !== 2))
        {
            return error(['error' => '错误的试卷']);
        }
        $examquestions = json_decode($paper->examquestions,true);
        if(!$examquestions)return error(['error' => '错误的试卷']);
        $examscore = json_decode($paper->examscore,true);
        $examsetting = json_decode($paper->examsetting,true);
        $questions = array();
        foreach($examquestions as $key => $p)
        {
            if($p['questions'])
            {
                $questions[$key]['questions'] = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->select(['questionid','question','questiontype'])
                    ->whereIn('questionid', $p['questions'])->get();
                foreach($p['questions'] as $id)
                {
                    $examscore[$id] = $examscore[$id]??$examsetting['questionTypes'][$key]['score'];
                }
            }
            if($p['rowsquestions'])
            {
                $questions[$key]['rowsquestions'] = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->select(['questionid','question','questiontype'])
                    ->where('questionisparent',1)
                    ->whereIn('questionid', $p['rowsquestions'])->get();
                foreach ($questions[$key]['rowsquestions'] as $id => $row)
                {
                    $children = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->select(['questionid','question','questiontype'])
                        ->orderBy('questionsequence','ASC')->orderBy('questionid','DESC')
                        ->where('questionparent',$row['questionid'])
                        ->get();
                    $questions[$key]['rowsquestions'][$id]['data'] = $children;
                    foreach ($children as $child)
                    {
                        $examscore[$child['questionid']] = $examscore[$child['questionid']]??$examsetting['questionTypes'][$key]['score'];
                    }
                }
            }
        }
        return [
            'examscore' => $examscore,
            'questions' => $questions,
        ];
    }

    public function All():array|Error
    {
        $subjectId = $this->request->subjectid;
        $results = ExamPaper::getQuery()->select(['examid', 'exam'])
            ->where('examsubject', $subjectId)
            ->get();
        $papers = [];
        foreach ($results as $result) {
            $papers[$result['examid']] = $result['exam'];
        }
        return $papers;
    }

    public function Paper():array
    {
        $paperId = $this->request->paperid;
        $paper = ExamPaper::find($paperId);
        $paper->examsetting = json_decode($paper->examsetting,true);
        $paper->examquestions = json_decode($paper->examquestions,true);
        return $paper->getRaw();
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = ExamService::getPaperQueryWithSubject()->select(['examid', 'exam','examtotalscore','examsubject','examtype','examtime','subject']);
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        if($search) {
            if($search['keyword']??false)$query->where("exam", "like", "%{$search['keyword']}%");
            if($search['subjectid']??false)$query->where("examsubject", $search['subjectid']);
        }
        $data = $query->paginate($page, $limit);
        array_walk($data['data'], function (&$item) {
            $item['examtime'] = date('Y-m-d',$item['examtime']);
        });
        return $data;
    }

    public function Index(): Error|array
    {
        return [];
    }
}
