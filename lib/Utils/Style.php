<?php

namespace PHPEMS\Lib\Utils;

class Style
{
    protected $cssFiles = array();

    protected $jsFiles = array();

    protected $defaultCss = array(
        'desktop' => [
            [
                'path' => 'resources/styles/layui/css/layui.css',
                'weight' => 10
            ],
            [
                'path' => 'resources/styles/phpems/desktop/css/home.css',
                'weight' => 10
            ]
        ],
        'phone' => [
            [
                'path' => 'resources/styles/layui/css/layui.css',
                'weight' => 10
            ]
        ]
    );
    protected $defaultJs = array(
        'desktop' => [
            [
                'path' => 'resources/styles/layui/layui.js',
                'weight' => 10
            ]
        ],
        'phone' => [
            [
                'path' => 'resources/styles/layui/layui.js',
                'weight' => 10
            ]
        ]
    );

    /**
     * 浏览器类型: desktop | mobile
     * @var string
     */
    private $UA = 'desktop';

    public function __construct(string $UA = null)
    {
        if(!$UA)$UA = (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false) ? 'phone' : 'desktop';
        $this->setUA($UA);
    }

    public function setUA(string $UA)
    {
        $this->UA = $UA == 'phone'?:'desktop';
    }

    /**
     * @param $file = ['path' => '','weight' => 10]
     * @return void
     */
    public function addCssFile(array $file)
    {
        $this->cssFiles[] = $file;
    }
    /**
     * @param $file = ['path' => '','weight' => 10]
     * @return void
     */
    public function addJsFile(array $file)
    {
        $this->jsFiles[] = $file;
    }

    public function exportCss()
    {
        $cssFiles = array_merge($this->cssFiles, $this->defaultCss[$this->UA]);
        usort($cssFiles, function($a, $b) {
            return $a['weight'] <=> $b['weight'];
        });
        $css = '';
        foreach ($cssFiles as $file) {
            $css .= '<link rel="stylesheet" href="' . $file['path'] . '" />' . "\n";
        }
        return $css;
    }

    public function exportJs()
    {
        $jsFiles = array_merge($this->jsFiles, $this->defaultJs[$this->UA]);
        usort($jsFiles, function($a, $b) {
            return $a['weight'] <=> $b['weight'];
        });
        $js = '';
        foreach ($jsFiles as $file) {
            $js .= '<script type="text/javascript" src="' . $file['path'] . '"></script>' . "\n";
        }
        return $js;
    }
}