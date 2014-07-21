<?php

namespace EasyScrumREST\FrontendBundle\Twig\Extension;
use Twig_Environment as Environment;

use EasyScrumREST\FrontendBundle\Twig\Extension\EasyScrumRESTExtension;

class DrawingboardExtension extends EasyScrumExtension
{

    protected $twig;
    protected $assetFunction;

    public function initRuntime(Environment $twig)
    {
        $this->twig = $twig;
    }

    protected function asset($asset)
    {
        if (empty($this->assetFunction)) {
             $this->assetFunction = $this->twig->getFunction('asset')->getCallable();
        }
        return call_user_func($this->assetFunction, $asset);
    }

    public function getFunctions()
    {
        return array('initDrawingboard' => new \Twig_Function_Method($this, 'initDrawingboard'));
    }

    public function initDrawingboard($id, $options = null, $html_options = null)
    {

        $out = $this->_loadInitCSS();
        $out .= $this->_loadInitJS();
        $out .= '<div id="'.$id.'"';
        if(!empty($html_options)){
            foreach($html_options as $k => $v){
                $out .= $k."=\"".$v."\" ";
            }
        }
        $out .= '></div>';
        $out .= $this->_loadDrawingboardJS($id, $options);        
        return $out;
    }

    private function _loadInitCSS(){
        return '<link rel="stylesheet" href="'.$this->asset('bundles/backend/css/drawingboard/drawingboard.min.css').'">';
    }

    private function _loadInitJS(){
        //return '<script src="'.$this->asset('bundles/backend/js/drawingboard/drawingboard.min.js').'"></script>';
        $out = '<script src="'.$this->asset('bundles/backend/js/drawingboard/drawingboard.js').'"></script>';
        $out .= '<script src="'.$this->asset('bundles/backend/js/drawingboard/board.js').'"></script>';
        $out .= '<script src="'.$this->asset('bundles/backend/js/drawingboard/control.js').'"></script>';
        $out .= '<script src="'.$this->asset('bundles/backend/js/drawingboard/color.js').'"></script>';
        $out .= '<script src="'.$this->asset('bundles/backend/js/drawingboard/drawingmode.js').'"></script>';
        $out .= '<script src="'.$this->asset('bundles/backend/js/drawingboard/navigation.js').'"></script>';
        $out .= '<script src="'.$this->asset('bundles/backend/js/drawingboard/size.js').'"></script>';
        $out .= '<script src="'.$this->asset('bundles/backend/js/drawingboard/download.js').'"></script>';
        $out .= '<script src="'.$this->asset('bundles/backend/js/drawingboard/utils.js').'"></script>';
        return $out;
    }

    private function _loadDrawingboardJS($id, $options = null){

        $_default = array('controls' => array('Color', 
                                              'Size' => array('type' => 'dropdown'),
                                              //'DrawingMode' => array('filler' => false),
                                              'Navigation'),
                          'size' => 2,
                          'webStorage' => false);

        if(is_array($options)){
            $_options = array_merge($options, $_default);
        }else{
            $_options = $_default;
        }

        $out = '<script>var simpleBoard = new DrawingBoard.Board(\''.$id.'\', {';
        $c1 = '';
        foreach ($_options as $k1 => $v1) {//primer nivel
            $out .= $c1.$k1.':';
            if(is_array($v1)){
                $c2 = '';
                $out .= '[';
                foreach ($v1 as $k2 => $v2) {//segundo nivel
                    $out .= $c2;
                    if(is_numeric($k2)){
                        $out .= '\''.$v2.'\'';
                    }else{
                        if(is_array($v2)){
                            $out .= '{\''.$k2.'\':';
                            $c3 = '';
                            $out .= '{';
                            foreach ($v2 as $k3 => $v3) {//tercer nivel
                                $out .= $c3;
                                $out .= '\''.$k3.'\':\''.$v3.'\'';    
                                $c3 = ',';
                            }
                            $out .= '}';
                        }else{
                            $out .= '{\''.$v2.'\':';
                        }
                        $out .= '}';
                    }
                    $c2 = ',';
                }
                $out .= ']';
            }else{
                $out .= '\''.$v1.'\'';
            }
            $c1 = ',';
        }

        $out .='});</script>';
        return $out;
    }

    public function getName()
    {
        return 'drawingboard_extension';
    }
    
}
