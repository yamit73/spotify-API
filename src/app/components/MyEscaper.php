<?php
namespace App\Components;

use Phalcon\Escaper;

class MyEscaper
{
    public function sanitize($data)
    {
        $escaper=new Escaper();
        $res=array();
        foreach ($data as $key => $val) {
            $res[$key]=$escaper->escapeHtml($val);
        }
        return $res;
    }
}
