<?php
function vbs_tokenize($line) {
    $tokens = array();

    $symbols = array(
        '^', '-', '*', '/', '\\', '+', '&',
        '>', '<', , '=', '(', ')', ',', '%', ' '
    );
    $chars = str_split($line);
    $token = '';
    foreach($chars as $char) {
        if(in_array($char, $symbols)) {
            $token = trim($token);
            if($token != '') {
                $tokens[] = $token;
            }
            $token = '';
            $tokens[] = $char;
        } else {
            $token .= $char;
        }
    }

    return $tokens;
}
