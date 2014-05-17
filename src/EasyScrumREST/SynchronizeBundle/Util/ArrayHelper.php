<?php

namespace EasyScrumREST\SynchronizeBundle\Util;

use EasyScrumREST\ImageBundle\Util\ImageHelper;
abstract class ArrayHelper
{
    public static function flattMultilevelEntityArray($array)
    {
        $newArray=array();
        foreach ($array as $nextArray) {
            $aux=array();
            foreach ($nextArray as $key=>$value) {
                if(is_array($value)){
                    foreach ($value as $field=>$val){
                        if ($field == 'incisors') {
                            $aux[$field] = ImageHelper::fromPngToOctet($val);
                        } else {
                            if(is_array($val)){
                                foreach ($val as $subfield=>$subval){
                                    if ($field=='quadrants') {
                                        foreach ($subval as $quadrantfield=>$quadrantval) {
                                            if ($quadrantfield == 'image') {
                                                $aux['quadrant'.($subfield+1)] = ImageHelper::fromPngToOctet($quadrantval);
                                            }
                                        }
                                    } else {
                                        $aux[$field.'_'.$subfield] = $subval;
                                    }
                                }
                            } else {
                                $aux[$field] = $val;
                            }
                        }
                    }
                } else {
                    $aux[$key] = $value;
                }
            }

            $newArray[] = $aux;
        }

        return $newArray;
    }
}