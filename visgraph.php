<?php
$contents = file_get_contents("JVC_85190.vbs");
$lines = explode(PHP_EOL, $contents);

$relations = array();
$attributes = array();
$_SR = array(
    "on", "error", "resume", "next", "dim",
    "function", "then", "to", "len", "else",
    "if", "end", "next", "replace", "and",
    "or", "exp", "abs", "mid", "cint",
    "sin", "atn", "chr", "fix", "tan",
    "sqr", "for", "step", "wscript", "sleep",
    "int", "log", "cos", "asc"
);
$_SL = array("dim", "rem", "for");

foreach($lines as $line) {
    if(substr($line, 0, 1) == "'") {
        continue;
    }

    $_L = "";
    $_R = "";
    $_LR = explode("=", $line);
    if(count($_LR) > 1) {
        $_L = $_LR[0];
        $_R = implode("", array_slice($_LR, 1));
    } else {
        $_L = "";
        $_R = $line;
    }

    $_L = trim($_L);
    $_R = trim($_R);

    $quotes_pos = strpos($_R, '"');
    if($quotes_pos !== false) {
        $_R = trim(substr($_R, 0, $quotes_pos));
    }
    
    if(empty($_L)) {
        $_L = "VAR";
    }

    if(empty($_R)) {
        $_R = "VAR";
    }

    $_LW = array_filter(explode(" ", trim(str_replace(array("(", ")", "*", "/", "-", "+", ",", "<", ">", "."), " ", $_L))));
    $_RW = array_filter(explode(" ", trim(str_replace(array("(", ")", "*", "/", "-", "+", ",", "<", ">", "."), " ", $_R))));

    if(current($_RW) == "function") {
        $_W = next($_RW);
        $_FN = $_W;
        //$relations[] = array("FUNCTION", $_FN);
        $attributes[] = array($_FN, "fontcolor=white,color=red");

        while($_W !== false) {
            $_W = next($_RW);
            if(!empty($_W)) {
                //$relations[] = array("PARAMETER", $_W);
                $relations[] = array($_FN, $_W);
                $attributes[] = array($_W, "fontcolor=white,color=blue");
            }
        }
    }

    foreach($_LW as $lw) {
        foreach($_RW as $rw) {
            if(!ctype_digit($rw) && !in_array(strtolower($rw), $_SR) && !in_array(strtolower($lw), $_SL)) {
                /*
                if($lw != "VAR") {
                    $relations[] = array("VAR", $lw);
                }
                */

                if(($lw . $rw) != "VARVAR") {
                    $relations[] = array($lw, $rw);
                }
            }
        }
    }
}

// add function node
//$attributes[] = array("FUNCTION", "fontcolor=white,color=red");
//$attributes[] = array("PARAMETER", "fontcolor=white,color=blue");
//$attributes[] = array("GLOBAL", "fontcolor=white,color=green");
$attributes[] = array("VAR", "fontcolor=white,color=green");

// make dinetwork
$dinetwork = "";
foreach($relations as $rel) {
    $dinetwork .= sprintf(" %s -> %s;", $rel[0], $rel[1]);
}

$attr_index = array();
foreach($attributes as $attr) {
    if(!in_array($attr[0], $attr_index)) {
        $attr_index[] = $attr[0];
        $dinetwork .= sprintf(" %s [%s]", $attr[0], $attr[1]);
    }
}

echo sprintf("dinetwork {node[shape=circle]; %s }", $dinetwork);
