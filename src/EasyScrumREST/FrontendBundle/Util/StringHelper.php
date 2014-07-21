<?php

namespace EasyScrumREST\FrontendBundle\Util;

class StringHelper
{
    public static function getQueryArrayFromArray($params=null)
    {
        $get="";
        if ($params) {
            $get="?";
            foreach ($params as $key => $value) {
                if (!is_string($value) && isset($value)) {
                    foreach ($value as $childKey => $childValue) {
                        $get.="&".$key.'['.$childKey.']='.$childValue;
                    }
                } else {
                    reset($params);
                    if ($key === key($params)) {
                        $get.=$key."=".$value;
                    } else {
                        $get.="&".$key."=".$value;
                    }
                }
            }
        }

        return $get;
    }

}