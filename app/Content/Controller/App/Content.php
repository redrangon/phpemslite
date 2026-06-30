<?php

namespace PHPEMS\App\Content\Controller\App;

use PHPEMS\App\Content\Service\Model\ContentCategory;
use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Content extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth','Json'];
    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data'
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

    public function Data(): array
    {
        $query = \PHPEMS\App\Content\Service\Model\Content::getQuery()->orderBy('contentsequence','desc')->orderBy('contentid', 'DESC');
        $query->where('contentstatus', 1);
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;

        $catId = $this->request->catId??null;
        if($catId)
        {
            $catTree = ContentCategory::getCategoryTree();
            $children = ContentCategory::getAllDescendantIds($catTree, $catId);
            $children[] = $catId;
            $query->whereIn('contentcatid', $children);
        }

        $data = $query->paginate($page, $limit);

        if($data['total'] > 0) {
            array_walk($data['data'], function (&$item) {
                $item['contentinputtime'] = date('Y-m-d H:i:s', $item['contentinputtime']);
                if($item['contentmodifytime'] > 0) {
                    $item['contentmodifytime'] = date('Y-m-d H:i:s', $item['contentmodifytime']);
                } else {
                    $item['contentmodifytime'] = '';
                }
            });
        }
        return $data;
    }

    public function index():array | Error
    {
        $contentId = $this->request->contentId??null;
        if(!$contentId)return error(['error'=>'参数错误']);
        $content = \PHPEMS\App\Content\Service\Model\Content::find($contentId);
        if(!$content->contentId)return error(['error'=>'新闻不存在']);
        $content->contentview ++;
        $content->save();
        $content->contentInputTime = date('Y-m-d H:i:s',$content->contentInputTime);
        return $content->getRaw();
    }
}