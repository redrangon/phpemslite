<?php

namespace PHPEMS\App\Cert\Controller\App;

use Exception;
use PHPEMS\App\Cert\Service\CertMaker;
use PHPEMS\App\Cert\Service\CertService;
use PHPEMS\App\Cert\Service\Model\CertMember;
use PHPEMS\App\Member\Service\Model\Member;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Cert extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'image' => 'CertImage',
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

    public function CertImage():array|Error
    {
        $cemId = $this->request->cemId??null;
        if(!$cemId)return error(['error' => '证书不存在']);
        $certMember = CertMember::find($cemId);
        if(!$certMember->cemId || !$certMember->cemstatus)return error(['error' => '证书不存在']);
        $certImage = CertMaker::create($certMember->cemId);
        $cert = \PHPEMS\App\Cert\Service\Model\Cert::find($certMember->cemceid);
        if(!$certImage->isCached($cert->cetpl))
        {
            $member = Member::findByPassport($certMember->cempassport);
            $setting = $cert->cetags;
            $info = [
                'name' => $member->mname,
                'passport' => $member->mpassport,
                'pubtime' => $certMember->cemtime,
                'certsn' => $certMember->cemsn
            ];
            $searchKeys = array_map(function($key) {
                return "\{$key\}";
            } , array_keys($info));
            $replaceMap = array_combine($searchKeys, $info);
            $result = strtr($setting, $replaceMap);
            $imgSettings = array();
            $settings = explode("\n",$setting);
            foreach($settings as $setting)
            {
                $setting = explode(',',trim($setting));
                $imgSettings[] = $setting;
            }
            try{
                $certImage->render($cert->cetpl,$imgSettings);
            }catch (Exception $e){
                return error(['error' => $e->getMessage()]);
            }
        }
        return ['image' => $certImage->getCertImage()];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        $query = CertService::getCertQueryWithMember()->orderBy('cemid', 'DESC');
        $user = $this->request->getUser();
        $query->where('cempassport', $user->userpassport)->where('cemstatus', 1);
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

    public function Index(): Error|array
    {
        $cemId = $this->request->cemId??null;
        if($cemId) {
            $data = CertMember::find($cemId)->getRaw();
            if($data) {
                $data['cemtime'] = $data['cemtime'] > 0 ? date('Y-m-d H:i:s', $data['cemtime']) : '';
                $data['cemexpirytime'] = $data['cemexpirytime'] > 0 ? date('Y-m-d H:i:s', $data['cemexpirytime']) : '';
            }
            return $data ?? [];
        }
        return [];
    }
}
