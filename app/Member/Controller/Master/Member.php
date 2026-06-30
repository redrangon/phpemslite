<?php

namespace PHPEMS\App\Member\Controller\Master;

use PHPEMS\Lib\Config\Site\Site;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\ExcelProvider;
use PHPEMS\Lib\Utils\FileProvider;
use PHPEMS\Lib\Utils\Formatter;
use PHPEMS\Lib\Utils\Office\Xlsx\Reader;

class Member extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add',
            'import' => 'Import'
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

    public function Import():array|Error
    {
        $file = $this->request->file??null;
        if(empty($file))return error(['error' => '请选择文件']);
        if($file['member']??false)
        {
            if(file_exists($file['member']))
            {
                try {
                    $memberData = ExcelProvider::importExcel($file['member']);
                    $zip = false;
                    if(isset($file['zip']) && file_exists($file['zip']))
                    {
                        $zip = new \ZipArchive();
                        $openRes = $zip->open($file['zip']);
                        $path = (new Site())->tmpDir.bin2hex(random_bytes(32));
                        if(!is_dir($path))mkdir($path);
                        if ($openRes === TRUE) {
                            $zip->extractTo($path);
                            $zip->close();
                        }
                    }
                    foreach($memberData as $indexKey => $data)
                    {
                        if($indexKey)
                        {
                            $imgs = array(
                                'mpassportimg' => '',
                                'mphoto' => 'zp'
                            );
                            $passport = $data[0];
                            $passport = Formatter::formatPassport($passport);
                            $args = array();
                            $args['mpassport'] = $passport['passport'];
                            $args['mname'] = (string) $data[1];
                            $args['msex'] = $passport['sex'];
                            $args['mbirthday'] = (string) $passport['birthday'];
                            $args['mphone'] = (string) $data[4];
                            $args['maddress'] = (string) $data[5];
                            $args['medu'] = (string) $data[6];
                            $args['munit'] = (string) $data[7];
                            $args['mcompany'] = (string) $data[8];
                            $args['mteam'] = (string) $data[9];
                            $args['mjob'] = (string) $data[10];
                            $args['mjobtitle'] = (string) $data[11];
                            $args['mpolitic'] = (string) $data[12];
                            $args['mjobtime'] = (string) $data[13];
                            if($zip)
                            {
                                foreach($imgs as $key => $img)
                                {
                                    foreach(glob($path.'/'.$passport['passport'].$img.".*") as $p)
                                    {
                                        $args[$key] = (new FileProvider())->transfer($p);
                                    }
                                }
                            }
                            $member = \PHPEMS\App\Member\Service\Model\Member::findByPassport($passport['passport']);
                            if(!$member->mid)
                            {
                                $member = \PHPEMS\App\Member\Service\Model\Member::fillWithInit($args);
                                $result = $member->toValidate();
                                if($result !== true)return $result;
                            }
                            else
                            {
                                foreach($args as $key => $value)
                                {
                                    if($member->$key != $value)
                                    {
                                        $member->$key = $value;
                                    }
                                }
                            }
                            $member->save();
                        }
                    }
                    return [];
                } catch (\Exception $e) {
                    return error(['error' => '数据读取错误']);
                }
            }
            else return error(['error' => '文件不存在']);
        }
        else return error(['error' => '未选择文件']);
    }

    /**
     * 添加操作
     */
    public function Add():array|Error
    {
        $data = [
            'mname' => $this->request->mname??'',
            'mphoto' => $this->request->mphoto??'',
            'mpassport' => $this->request->mpassport??'',
            'mpassportimg' => $this->request->mpassportimg??'',
            'msex' => $this->request->msex??'',
            'mbirthday' => $this->request->mbirthday??'',
            'mpolitic' => $this->request->mpolitic??'',
            'medu' => $this->request->medu??'',
            'munit' => $this->request->munit??'',
            'mcompany' => $this->request->mcompany??'',
            'mjobtime' => $this->request->mjobtime??'',
            'mjob' => $this->request->mjob??'',
            'mjobtitle' => $this->request->mjobtitle??'',
            'mteam' => $this->request->mteam??'',
            'mtext' => $this->request->mtext??'',
            'mresume' => $this->request->mresume??'',
            'mtime' => TIME,
        ];

        $model = \PHPEMS\App\Member\Service\Model\Member::fillWithInit($data);
        if(!$model->save()) return error(['error' => '添加失败']);
        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function Modify():array|Error
    {
        $mid = $this->request->mid;
        $model = \PHPEMS\App\Member\Service\Model\Member::find($mid);
        if(!$model->mid) return error(['error' => '记录不存在']);
        $data = [
            'mname' => $this->request->mname??null,
            'mphoto' => $this->request->mphoto??null,
            'mpassport' => $this->request->mpassport??null,
            'mpassportimg' => $this->request->mpassportimg??null,
            'msex' => $this->request->msex??null,
            'mbirthday' => $this->request->mbirthday??null,
            'mpolitic' => $this->request->mpolitic??null,
            'medu' => $this->request->medu??null,
            'munit' => $this->request->munit??null,
            'mcompany' => $this->request->mcompany??null,
            'mjobtime' => $this->request->mjobtime??null,
            'mjob' => $this->request->mjob??null,
            'mjobtitle' => $this->request->mjobtitle??null,
            'mteam' => $this->request->mteam??null,
            'mtext' => $this->request->mtext??null,
            'mresume' => $this->request->mresume??null,
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
    public function Delete():array|Error
    {
        $ids = $this->request->ids;
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        \PHPEMS\App\Member\Service\Model\Member::getQuery()->whereIn('mid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        $query = \PHPEMS\App\Member\Service\Model\Member::getQuery()->orderBy('mid', 'DESC');

        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;

        if($search) {
            if($search['mname']??false) {
                $query->where('mname', 'like', '%'.$search['mname'].'%');
            }
            if($search['mpassport']??false) {
                $query->where('mpassport', 'like', '%'.$search['mpassport'].'%');
            }
            if($search['msex']??false) {
                $query->where('msex', $search['msex']);
            }
            if($search['medu']??false) {
                $query->where('medu', $search['medu']);
            }
            if($search['munit']??false) {
                $query->where('munit', 'like', '%'.$search['munit'].'%');
            }
            if($search['mcompany']??false) {
                $query->where('mcompany', 'like', '%'.$search['mcompany'].'%');
            }
            if($search['mjob']??false) {
                $query->where('mjob', 'like', '%'.$search['mjob'].'%');
            }
            if($search['mteam']??false) {
                $query->where('mteam', $search['mteam']);
            }
        }

        $data = $query->paginate($page, $limit);

        if($data['total'] > 0) {
            array_walk($data['data'], function (&$item) {
                $item['mtime'] = $item['mtime'] > 0 ? date('Y-m-d H:i:s', $item['mtime']) : '';
                $item['mbirthday'] = $item['mbirthday'] ?: '';
                $item['mjobtime'] = $item['mjobtime'] ?: '';
            });
        }

        return $data;
    }

    /**
     * 获取单条数据
     */
    public function Index(): Error|array
    {
        $mid = $this->request->mid??null;
        $passport = $this->request->passport??null;
        if($mid) {
            $data = \PHPEMS\App\Member\Service\Model\Member::find($mid)->getRaw();
        }
        elseif($passport){
            $data = \PHPEMS\App\Member\Service\Model\Member::getQuery()->where('mpassport',$passport)->first();
        }
        if($data) {
            // 格式化时间
            $data['mtime'] = $data['mtime'] > 0 ? date('Y-m-d H:i:s', $data['mtime']) : '';
            $data['mbirthday'] = $data['mbirthday'] ?: '';
            $data['mjobtime'] = $data['mjobtime'] ?: '';
            return $data;
        }

        return [];
    }
}
