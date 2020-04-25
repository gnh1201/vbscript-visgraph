<?php
$contents = file_get_contents("JVC_85190.vbs");
$lines = explode("\n", $contents);

$stop_words = array(
    "on", "error", "resume", "next", "dim",
    "function", "then", "to", "len", "else",
    "if", "end", "next", "replace", "and",
    "or", "exp", "abs", "mid", "cint",
    "sin", "atn", "chr", "fix", "tan",
    "sqr", "for", "step", "wscript", "sleep",
    "int", "log", "cos", "asc"
);

$stop_opcodes = array("rem");
$stop_chars = array(" ", "=", "(", ")", "*", "/", "-", "+", ",", "<", ">", ".", "\r");

// (y, b, w, f) = (line, block, expression, is_function)
$evals = array("executeglobal");
$blocks = array("main");
$m = array(
	array(0, 0, "main", 1)
);

$y = 0;
$b = 0;
$w = "";
$f = 0;

foreach($lines as $line) {
	// cleaning the line
	$doublequote = strpos($line, "\"");
	if($doublequote !== false) {
		$line = substr($line, 0, $doublequote);
	}
	$line = strtolower($line);

	// increase index of line 
	$y++;

	// skip when comment
    if(substr($line, 0, 1) == "'") {
        continue;
    }

	// get tokenized expressions
	$words = array_filter(explode(" ", str_replace($stop_chars, " ", $line)));

	// get opcode
	$opcode = current($words);

	// skip when comment
    if(in_array($opcode, $stop_opcodes)) {
        continue;
    }

	// when function
	if($opcode == "function") {
		$f++;
		$b++;
		$blocks[$b] = $words[1];
	}
	
	// when end of function
	if($opcode == "end" && in_array("function", $words)) {
		$f--;
	}

	// retrive words
	foreach($words as $word) {
		if(!in_array($word, $stop_words)) {
			// skip when digit
			if(ctype_digit($word)) {
				continue;
			}
			
			// (y, b, w) = (line, block, expression, is_function)
			$m[] = array($y, ($f > 0 ? $b : 0), $word, $f);
		}
	}
}

// ...
$dinetwork = "";

// ...
foreach($blocks as $blockname) {
	$dinetwork .= sprintf(" %s [%s]", $blockname, "fontcolor=white,color=red");
}

// ...
foreach($evals as $evalname) {
	$dinetwork .= sprintf(" %s [%s]", $evalname, "fontcolor=white,color=green");
}

// ...
$m2 = array();

// ...
foreach($m as $r) {
	if(!array_key_exists($r[2], $m2)) {
		$m2[$r[2]] = array();
	}

	if($r[3] == 0 && in_array($r[2], $blocks)) {
		$m2[$r[2]][] = 0;
	}

	$m2[$r[2]][] = $r[0];
}

// 

// ...
foreach($m2 as $k2=>$r2) {
	foreach($m2 as $_k2=>$_r2) {
		$a0 = array_intersect($r2, $_r2);
		if(count($a0) > 0 && $k2 != $_k2) {
			$dinetwork .= " {$k2} -> {$_k2};";
		}
	}
}

// ...
echo sprintf("dinetwork {node[shape=circle]; %s }", $dinetwork);
