<?php

namespace EasyScrumREST\FrontendBundle\Twig\Extension;

use EasyScrumREST\FrontendBundle\Twig\Extension\EasyScrumExtension;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class JavascriptExtension extends EasyScrumExtension
{
    const DELAY = 1000;
    const FADE_IN = 400;
    const FADE_OUT = 800;

    public function getFunctions()
    {
        return array('resetForm'=> new \Twig_Function_Method($this, 'resetForm'),
                     'select2Tagged'=> new \Twig_Function_Method($this, 'select2Tagged'),
                     'showNextElement'=> new \Twig_Function_Method($this, 'showNextElement'),
                     'makeModal' => new \Twig_Function_Method($this, 'makeModal'),
                     'excludedCheckbox' => new \Twig_Function_Method($this, 'excludedCheckbox'),
                     'getSlug' => new \Twig_Function_Method($this, 'getSlug'),
                     'gifLoader' => new \Twig_Function_Method($this, 'gifLoader'),
                     'formFriendlyUrl' => new \Twig_Function_Method($this, 'formFriendlyUrl'),
                     'getEdad' => new \Twig_Function_Method($this, 'getEdad'),
                     'onlyOneCheckboxByClass' => new \Twig_Function_Method($this, 'onlyOneCheckboxByClass'),
                     'goToTop' => new \Twig_Function_Method($this, 'goToTop'),
                     'phoneESValidator' => new \Twig_Function_Method($this, 'phoneESValidator'),
                );
    }

    public function resetForm()
    {
        $script = "function formReset(name){
                        document.getElementById(name).reset();
                    }";

        $this->printScript($script);
    }

    public function showNextElement($selectors, $nextElementSelectors)
    {
         $script = "$('$selectors').change(function(){
            var next = $(this).next('$nextElementSelectors');
            if ($(this).val() && $(this).val() != $(this).attr('next-option')) {
                next.removeClass('hidden');
            } else {
                next.addClass('hidden');
                next.val('');
            }
        });";

         $this->printScript($script);
    }

    public function showAndHide($selector, $print=false, $delay=self::DELAY, $fadeIn=self::FADE_IN, $fadeOut=self::FADE_OUT)
    {
        $script = "$('$selector').fadeIn($fadeIn).delay($delay).fadeOut($fadeOut);";

        if ($print) {
            $this->printScript($script);
        } else {
            return $script;
        }
    }

    public function appendShowAndHide($print=false, $delay=self::DELAY, $fadeIn=self::FADE_IN, $fadeOut=self::FADE_OUT)
    {
        $script = ".fadeIn($fadeIn).delay($delay).fadeOut($fadeOut);";

        if ($print) {
            $this->printScript($script);
        } else {
            return $script;
        }
    }

    public function select2Tagged($selector, $tags, $urlAdd, $urlRemove)
    {
        $urlized = Urlizer::urlize($selector, "");
        $varName = "availableTags$urlized";
        $script = "var $varName = ["
                . join(',', array_map(function ($t)
                {
                    return "\"$t\"";
                }, $tags)) . "];";

        $this->printScript($script);

        $script = "
            $('$selector').select2({tags: $varName});
            $('$selector').on(\"change\", function(e) {
                var elem = $(this);
                if(e.added){
                    $.post(\"$urlAdd\", {tagName: e.added.id});
                } else if (e.removed) {
                    $.post(\"$urlRemove\", {tagName: e.removed.id});
                }
                var callbacks = elem.attr('data-callback');
                if (callbacks) {
                    var array_callbacks = callbacks.split(' ');
                    for(var i=0; i<array_callbacks.length; i++) {
                        var callback = array_callbacks[i];
                        eval(callback);
                    }
                }
            });";

        $this->printScript($script, true);
    }

    public function makeModal($id, $params = null)
    {
        $_options =  array('title' => 'Título modal',
                           'text' => 'Texto modal',
                           'btn-close' => true,
                           'text-btn-close' => 'Cerrar',
                           'btn-accept' => false,
                           'footer' => true,
                           'text-btn-accept' => 'Aceptar');

        if (!empty($params)) {
            $options = array_merge($_options, $params);
        } else {
            $options = $_options;
        }

        if (($options['btn-accept'] == true) && ($options['text-btn-accept'] == 'Aceptar')
                && ($options['text-btn-close'] == 'Cerrar')) {
            $options['text-btn-close'] = 'Cancelar';
        }

        $html = '<div id="'.$id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <span id="myModalLabel" class="title">'.$options['title'].'</span>
                    </div>
                    <div class="modal-body">
                        <p>'.$options['text'].'</p>
                    </div>';
        if ($options['footer']) {
            $html .= '<div class="modal-footer">';
            if ($options['btn-close']) {
                $html .= '<button class="btn iventia-btn iventia-btn-grey" data-dismiss="modal" aria-hidden="true" id="'.$id.'-close">'.$options['text-btn-close'].'</button>';
            }

            if ($options['btn-accept']) {
                $html .= '<button class="btn iventia-btn iventia-btn-grey" id="'.$id.'-accept">'.$options['text-btn-accept'].'</button>';
            }

            $html .= '</div>';
        }

        $html .= '</div></div></div>';
        echo $html;
    }

    public function onlyOneCheckboxByClass($classElements) {
        $script = "$('.$classElements').click(function() {
            checkExclusive$classElements($(this));
        });

        $('.$classElements').each(function(){
            if ($(this).attr('checked') == 'checked')
                checkExclusive$classElements($(this));
        });";
        $this->printScript($script, true);

        $function = "function checkExclusive$classElements(check) {
            if (check.attr('checked') == 'checked') {
                $('.$classElements').attr('disabled', 'disabled');
                check.removeAttr('disabled');
            } else {
                $('.$classElements').removeAttr('disabled');
            }
        }";
        $this->printScript($function);
    }

    public function excludedCheckbox($checkboxElements, $checkboxToExclude)
    {
        $script = "var checkboxes = $('$checkboxElements input:checkbox');
                    $(document).ready(function() {
                        checkboxes_excluded();
                        checkboxes.click(function() {
                            checkboxes_excluded();
                        });
                    });

                    function checkboxes_excluded()
                    {
                        var excluded = $('$checkboxToExclude');
                        if((excluded.is(':checked'))) {
                            checkboxes.each(function(){
                                $(this).attr('disabled', 'disabled');
                            });
                            excluded.removeAttr('disabled');
                        } else {
                            checkboxes.each(function(){
                                $(this).removeAttr('disabled');
                            });
                        }
                    }";

        $this->printScript($script);
    }

    public function getSlug($capitalize=false)
    {
        $script = "function getSlug(text){
            slug=text;
            slug=slug.replace(/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g, 'a');
            slug=slug.replace(/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g, 'e');
            slug=slug.replace(/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g, 'i');
            slug=slug.replace(/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g, 'o');
            slug=slug.replace(/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g, 'u');
            slug=slug.replace(/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g, 'n');
            slug = slug.replace(/[^a-zA-Z0-9\/_|+ -]/, '');
            slug = slug.toLowerCase();
            slug = slug.replace(/^\s+|\s+$/g, '');
            slug =  slug.replace(/[\/_|+ -]+/, '-');";
        if ($capitalize) {
            $script .= "slug = slug.charAt(0).toUpperCase() + slug.slice(1);";
        }

        $script .= "return slug;}";

        $this->printScript($script);
    }

    public function formFriendlyUrl($formname)
    {
        $script = "$('#$formname').submit(function(){
                var frm = $(this);
                var url = '';
                var elements = document.$formname.elements;
                for(i=0; i<elements.length; i++){
                var value = elements[i].value;
                if (value) {
                url += '/'+elements[i].name+'/'+value;
                }
            }
            window.location = frm.attr('action')+url;

            return false;
            });";

        $this->printScript($script, true);
    }

    public function gifLoader($selector, $selectorToChange, $newImg=false)
    {
        $icon = $this->container->get('templating.helper.assets')->getUrl(Image::AJAX_LOADER);
        $script = "$('$selector').submit(function(){";
        if ($newImg) {
            $script .= "$('$selectorToChange').find('img').attr('src', '$icon').attr('width', '150px');";
        } else {
            $script .= "$('$selectorToChange').css('background', \"url('$icon') no-repeat center\");";
        }
        $script .= "});";

        $this->printScript($script);
    }

    public function goToTop()
    {
        $script = 'function goToTop(){
                window.location = "#top"; 
            }';

        $this->printScript($script);
    }

    public function getName()
    {
        return 'javascript_extension';
    }

    public function phoneESValidator()
    {
        $script = "jQuery.validator.addMethod(\"phoneES\", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, \"\").replace(/-/, \"\");
            return phone_number.match(/(6|9)\d{8}$/) || phone_number == '';
        }, '{{\"Introduce un número de teléfono correcto\"|trans}}');";

        $this->printScript($script);
    }
}
