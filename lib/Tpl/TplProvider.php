<?php

namespace PHPEMS\Lib\Tpl;

use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\DI\DI;

class TplProvider
{
    public $tpl;

    private function __construct($tpl)
    {
        $this->tpl = $tpl;
    }
    public function render($tpl, $data = [])
    {
        $this->tpl->display($tpl, $data);
    }

    public static function Create():self
    {
        $route = DI(RequestInterface::class)->getRoute();
        switch ($route[0])
        {
            case 'plugins':
                $tplDir = PEPATH . '/plugins/' . ucfirst($route[1]) .'/Tpls/' . ucfirst($route[2]). '/';
                $comDir = PEPATH . '/storage/templates/plugins/' . ucfirst($route[1]) . '/' . ucfirst($route[2]). '/';
                $config = [
                    'template_dir' => $tplDir,
                    'compile_dir' => $comDir
                ];
                break;

            default:
                $tplDir = PEPATH . '/app/' . ucfirst($route[0]) . '/Tpls/' . ucfirst($route[1]). '/';
                $comDir = PEPATH . '/storage/templates/app/' . ucfirst($route[0]) . '/' . ucfirst($route[1]). '/';
                $config = [
                    'template_dir' => $tplDir,
                    'compile_dir' => $comDir
                ];
                break;
        }
        $tpl = Tpl::getInstance($config);
        return new self($tpl);
    }
}