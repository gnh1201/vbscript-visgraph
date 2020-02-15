<?php
function vbs_tokenize() {
    $exps = array();

    $symbols = array(
        '^', '-', '*', '/', '\\', '+', '&',
        '>', '<', , '=', '(', ')', ',', '%', ' '
    );
    $chars = str_split($str);
    $exp = '';
    foreach($chars as $char) {
        if(in_array($char, $symbols)) {
            $exp = trim($exp);
            if($exp != '') {
                $exps[] = $exp;
            }
            $exp = '';
            $exps[] = $char;
        } else {
            $exp .= $char;
        }
    }

    return $exps;
}
