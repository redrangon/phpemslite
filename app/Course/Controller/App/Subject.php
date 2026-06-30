<?php

namespace PHPEMS\App\Course\Controller\App;

use PHPEMS\App\Course\Service\Model\CourseCategory;
use PHPEMS\App\Course\Service\Model\CourseMember;
use PHPEMS\App\Course\Service\Model\CoursePrice;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\App\User\Service\Model\UserExpense;
use PHPEMS\App\User\Service\Model\UserMoney;
use PHPEMS\App\Course\Service\Model\Course;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Subject extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'detail' => 'Detail',
            'price' => 'Price',
            'buy' => 'Buy'
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

    public function Buy(): array | Error
    {
        $priceId = $this->request->priceId??null;
        if($priceId)
        {
            $price = CoursePrice::find($priceId);
            if($price->cpId)
            {
                $user = $this->request->getUser();
                $userMoney = UserMoney::findByPassport($user->userpassport);
                if($userMoney->umamount >= $price->cpamount)
                {
                    try{
                        $course = CourseSubject::find($price->cpcsid);
                        if(!$course->csId)throw new \Exception('课程记录不存在');
                        CourseMember::getDB()->transaction(function() use ($price, $user,$course){
                            UserMoney::getQuery()->update([
                                'umamount' => function() use ($price){
                                    return "umamount - {$price->cpamount}";
                                }
                            ]);
                            UserExpense::getQuery()->insert([
                                'ueamount' => $price->cpamount,
                                'uepassport' => $user->userpassport,
                                'uetime' => TIME,
                                'uedescribe' => '购买课程'.$course->cstitle,
                            ]);
                            $courseMember = CourseMember::findByPassportAndSubjectId($user->userpassport, $course->csId);
                            if(!$courseMember->cmId)
                            {
                                $courseMember = CourseMember::fillWithInit([
                                    'cmcsid' => $course->csId,
                                    'cmpassport' => $user->userpassport,
                                    'cmstarttime' => TIME,
                                    'cmendtime' => TIME + 86400 * $price->cpdays,
                                ]);
                                $courseMember->save();
                            }
                            else
                            {
                                $courseMember->cmendtime = max(TIME,$courseMember->cmendtime) + 86400 * $price->cpdays;
                                $courseMember->save();
                            }
                        });
                        return ['msg' => '开通成功'];
                    }
                    catch (\Exception $e)
                    {
                        return error(['error' => '扣款失败，请稍后尝试！'.$e->getMessage()]);
                    }
                }
                else return error(['error' => '金额不足']);
            }
            return error(['error' => '价格记录不存在']);
        }
        return error(['error' => '记录不存在']);
    }

    public function Price(): array
    {
        $csId = $this->request->csId??null;
        if($csId)
        {
            $data = CourseSubject::find($csId);
            if($data->csId)
            {
                return CoursePrice::findByCsId($csId);
            }
        }
        return [];
    }

    public function Detail(): array | Error
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $csId = $this->request->csId??null;
        if($csId)
        {
            $query = Course::getQuery()
                ->select(['courseid', 'coursetitle', 'coursemodule', 'coursecsid','coursedirid'])
                ->orderBy('coursesequence', 'desc')
                ->orderBy('courseid', 'DESC');
            return $query->where('coursecsid', $csId)->get();
        }
        return $data??[];
    }
    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = CourseSubject::getQuery()->orderBy('cssequence','desc')->orderBy('csid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $catId = $this->request->catId ?? null;
        if($catId)
        {
            $catTree = CourseCategory::getCategoryTree();
            $children = CourseCategory::getAllDescendantIds($catTree, $catId);
            $children[] = $catId;
            $query->whereIn('cscatid', $children);
        }
        $data = $query->paginate($page, $limit);
        if($data['total'] > 0)
        {
            array_walk($data['data'], function (&$item){
                $item['cstime'] = date('Y-m-d', $item['cstime']);
            });
        }
        return $data;
    }

    public function Index(): Error|array
    {
        $csId = $this->request->csId??null;
        if($csId)
        {
            $data = CourseSubject::find($csId)->getRaw();
            $data['cstime'] = date('Y-m-d', $data['cstime']);
            $user = $this->request->getUser();
            $courseMember = CourseMember::findByPassportAndSubjectIdWithTime($user->userpassport, $csId);
            if($courseMember->cmid)
            {
                $data['isPurchased'] = 1;
            }
        }
        return $data??[];
    }
}
