<?php

namespace PHPEMS\App\Cert\Controller\Master;

use PHPEMS\App\Cert\Service\CertService;
use PHPEMS\App\Cert\Service\Model\CertMember;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Cert extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add',
            'member' => 'Member'
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

    public function Member():array|Error
    {
        $query = CertService::getCertQueryWithMember()->orderBy('cemid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        if($search) {
            if($search['cemceid']??false) {
                $query->where('cemceid', $search['cemceid']);
            }
            if($search['cemsn']??false) {
                $query->where('cemsn', 'like', '%'.$search['cemsn'].'%');
            }
        }
        $data = $query->paginate($page, $limit);
        if($data['total'] > 0) {
            array_walk($data['data'], function (&$item) {
                $item['cemtime'] = $item['cemtime'] > 0 ? date('Y-m-d H:i:s', $item['cemtime']) : '';
                $item['cemexpirytime'] = $item['cemexpirytime'] > 0 ? date('Y-m-d', $item['cemexpirytime']) : '';
            });
        }
        return $data;
    }

    /**
     * 添加证书
     */
    public function Add():array|Error
    {
        $data = [
            'cetitle' => $this->request->cetitle??'',
            'ceicon' => $this->request->ceicon??'',
            'cethumb' => $this->request->cethumb??'',
            'cedays' => $this->request->cedays??0,
            'cetime' => $this->request->cetime?strtotime($this->request->cetime):0,
            'cetpl' => $this->request->cetpl??'',
            'cetags' => $this->request->cetags??'',
            'cedescribe' => $this->request->cedescribe??'',
            'cetext' => $this->request->cetext??'',
        ];
        
        $model = \PHPEMS\App\Cert\Service\Model\Cert::fillWithInit($data);
        $result = $model->toValidate();
        if($result !== true)return $result;
        if(!$model->save()) return error(['error' => '添加失败']);
        return ['msg' => '添加成功'];
    }

    /**
     * 修改证书
     */
    public function Modify():array|Error
    {
        $ceid = $this->request->ceid;
        $model = \PHPEMS\App\Cert\Service\Model\Cert::find($ceid);
        if(!$model->ceid) return error(['error' => '记录不存在']);
        
        $data = [
            'cetitle' => $this->request->cetitle??null,
            'ceicon' => $this->request->ceicon??null,
            'cethumb' => $this->request->cethumb??null,
            'cedays' => $this->request->cedays??null,
            'cetime' => $this->request->cetime?strtotime($this->request->cetime):null,
            'cetpl' => $this->request->cetpl??null,
            'cetags' => $this->request->cetags??null,
            'cedescribe' => $this->request->cedescribe??null,
            'cetext' => $this->request->cetext??null,
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
     * 删除证书
     */
    public function delete():array|Error
    {
        $ids = $this->request->ids;
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        
        // 删除证书
        \PHPEMS\App\Cert\Service\Model\Cert::getQuery()->whereIn('ceid', $ids)->delete();

        // 同时删除相关的计划证书关联
        CertMember::getQuery()->whereIn('cemceid', $ids)->delete();
        
        return ['msg' => '删除成功'];
    }

    /**
     * 获取证书列表
     */
    public function Data(): array
    {
        $query = \PHPEMS\App\Cert\Service\Model\Cert::getQuery()->orderBy('ceid', 'DESC');
        
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        
        if($search) {
            if($search['keyword']??false) {
                $query->where('cetitle', 'like', '%'.$search['keyword'].'%');
            }
            if($search['cebasic']??false) {
                $query->where('cebasic', $search['cebasic']);
            }
        }
        
        $data = $query->paginate($page, $limit);
        
        if($data['total'] > 0) {
            array_walk($data['data'], function (&$item) {
                $item['cetime'] = $item['cetime'] > 0 ? date('Y-m-d H:i:s', $item['cetime']) : '';
            });
        }
        
        return $data;
    }

    /**
     * 获取单个证书
     */
    public function Index(): Error|array
    {
        $certId = $this->request->certid??null;
        if($certId) {
            $data = \PHPEMS\App\Cert\Service\Model\Cert::find($certId)->getRaw();
            return $data ?? [];
        }
        return [];
    }
}