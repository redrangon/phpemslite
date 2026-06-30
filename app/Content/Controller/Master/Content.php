<?php

namespace PHPEMS\App\Content\Controller\Master;

use PHPEMS\App\Content\Service\Model\ContentCategory;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Content extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add'
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [];
        return $flows[$action]??static::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    /**
     * 添加操作
     */
    public function Add():array|Error
    {
        // 获取请求数据
        $user = $this->request->getUser();
        $data = [
            'contentcatid' => $this->request->contentcatid??0,
            'contentmoduleid' => $this->request->contentmoduleid??0,
            'contentuserid' => $user->userid,
            'contentusername' => $user->username??'',
            'contentmodifier' => '',
            'contenttitle' => $this->request->contenttitle??null,
            'contenttags' => $this->request->contenttags??'',
            'contentkeywords' => $this->request->contentkeywords??'',
            'contentthumb' => $this->request->contentthumb??'',
            'contentlink' => $this->request->contentlink??'',
            'contentinputtime' => TIME,
            'contentmodifytime' => 0,
            'contentsequence' => $this->request->contentsequence??0,
            'contentdescribe' => $this->request->contentdescribe??'',
            'contentinfo' => $this->request->contentinfo??'',
            'contentstatus' => $this->request->contentstatus??0,
            'contenttext' => $this->request->contenttext??'',
            'contentview' => 0
        ];
        
        $model = \PHPEMS\App\Content\Service\Model\Content::fillWithInit($data);
        if(!$model->save()) return error(['error' => '添加失败']);
        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function modify():array|Error
    {
        $contentId = $this->request->contentid;
        $model = \PHPEMS\App\Content\Service\Model\Content::find($contentId);
        if(!$model->contentid) return error(['error' => '记录不存在']);
        
        $data = [
            'contentcatid' => $this->request->contentcatid??null,
            'contentmoduleid' => $this->request->contentmoduleid??null,
            'contentmodifier' => $this->request->contentmodifier??null,
            'contenttitle' => $this->request->contenttitle??null,
            'contenttags' => $this->request->contenttags??null,
            'contentkeywords' => $this->request->contentkeywords??null,
            'contentthumb' => $this->request->contentthumb??null,
            'contentlink' => $this->request->contentlink??null,
            'contentmodifytime' => TIME,
            'contentsequence' => $this->request->contentsequence??null,
            'contentdescribe' => $this->request->contentdescribe??null,
            'contentinfo' => $this->request->contentinfo??null,
            'contentstatus' => $this->request->contentstatus??null,
            'contenttemplate' => $this->request->contenttemplate??null,
            'contenttext' => $this->request->contenttext??null,
            'contentview' => $this->request->contentview??null
        ];
        
        $data = array_filter($data, function ($item) {
            return !is_null($item);
        });
        
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        
        if(!$model->save()) return error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    /**
     * 删除操作
     */
    public function delete():array|Error
    {
        $ids = $this->request->ids;
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        \PHPEMS\App\Content\Service\Model\Content::getQuery()->whereIn('contentid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        $query = \PHPEMS\App\Content\Service\Model\Content::getQuery()->orderBy('contentsequence','desc')->orderBy('contentid', 'DESC');
        
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        
        if($search) {
            if($search['keyword']??false) {
                $query->where('contenttitle', 'like', '%'.$search['keyword'].'%');
            }
            if($search['contentcatid']??false) {
                $catTree = ContentCategory::getCategoryTree();
                $children = ContentCategory::getAllDescendantIds($catTree, $search['contentcatid']);
                $children[] = $search['contentcatid'];
                $query->whereIn('contentcatid', $children);
            }
            if($search['contentstatus']??false) {
                $query->where('contentstatus', $search['contentstatus']);
            }
        }
        
        $data = $query->paginate($page, $limit);
        
        if($data['total'] > 0) {
            $cats = [];
            foreach ($data['data'] as $item) {
                $cats[] = $item['contentcatid'];
            }
            $cats = array_unique($cats);
            $categories = ContentCategory::getQuery()->whereIn('catid', $cats)->get();
            $cats = [];
            foreach ($categories as $category) {
                $cats[$category['catid']] = $category['catname'];
            }
            
            array_walk($data['data'], function (&$item) use ($cats) {
                $item['catname'] = $cats[$item['contentcatid']] ?? '';
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

    /**
     * 获取单条数据
     */
    public function Index(): Error|array
    {
        $contentId = $this->request->contentid??null;
        if($contentId) {
            $data = \PHPEMS\App\Content\Service\Model\Content::find($contentId)->getRaw();
            return $data ?? [];
        }
        return [];
    }
}