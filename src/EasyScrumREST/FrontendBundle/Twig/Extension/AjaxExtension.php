<?php

namespace EasyScrumREST\FrontendBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use EasyScrumREST\FrontendBundle\Twig\Extension\EasyScrumExtension;

class AjaxExtension extends EasyScrumExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array('ajaxUpdateContent'=> new \Twig_Function_Method($this, 'updateContent'),
                     'ajaxDynamicForm' => new \Twig_Function_Method($this, 'dynamicForm'));
    }

    public function dynamicForm($parentSelector, $selector)
    {
        $this->ajaxCallFromHeader("$('$parentSelector').on('submit', '$selector', function(e) {");
    }

    private function ajaxCallFromHeader($jqueryHeader)
    {
        $script = $jqueryHeader . $this->getBodyAjaxRequest() . $this->getJqueryClose();

        $this->printScript($script);
    }

    public function updateContent()
    {
        $script = "function updateContent(url, element) {
            $.get(url, function(content){
                $('#'+element).html(content);
            });
        }";

        $this->printScript($script);
    }

    private function getBodyAjaxRequest()
    {
        $js = new JavascriptExtension($this->container);

        return "e.preventDefault();
                var form = $(this);
                var loader = form.find('.form-notifications img.loader');
                loader.show();
                $.ajax({url: form.attr('action'), type: form.attr('method'), data: form.serialize()}).done(function(res){
                    loader.hide();
                    if (res.ok == false) {
                        form.find('.form-notifications p.text-error'){$js->appendShowAndHide()}
                    }
                    else {
                        var callbacks = form.attr('data-callback');
                        if (callbacks) {
                            var array_callbacks = callbacks.split(' ');
                            for(var i=0; i<array_callbacks.length; i++) {
                                var callback = array_callbacks[i];
                                eval(callback);
                            }
                        }
                        form.find('.form-notifications p.text-success'){$js->appendShowAndHide()}
                    }
                });";
    }

    public function getName()
    {
        return 'ajax_extension';
    }
}
