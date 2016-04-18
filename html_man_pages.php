<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>HTML Man pages</title>
<style type="text/css">
pre span.b { font-weight: bold; }
pre span.u { text-decoration: underline; }
pre span.e { font-weight: bold; text-decoration: underline; }
pre a { text-decoration: none; }
</style>
</head>
<body>
<?php
function make_shell_safe($string)
{ return substr(preg_replace('/[^a-zA-Z0-9-_\.]/',' ',$string),0,32);
}
$string = make_shell_safe($_GET['page']);
$string = trim(str_replace('  ',' ',$string));
$string = split(' ',$string);
$page = array_shift($string);
$section = array_shift($string);
$output=trim(shell_exec('export MAN_KEEP_FORMATTING=1; export LANG=en_US.UTF8; man '.$section.' '.$page.' 2>&1'));
$output=mb_convert_encoding(htmlspecialchars($output), 'HTML-ENTITIES', "UTF-8");
function replace_formatting($matches)
{ if ($matches[3]) return chr(8).'e'.$matches[2].chr(8).'/e';
  else if ($matches[1]==$matches[2]) return chr(8).'b'.$matches[2].chr(8).'/b';
  else if ($matches[1]=='_') return chr(8).'u'.$matches[2].chr(8).'/u';
  else if ($matches[1]=='+') return $matches[2];
}
$output=preg_replace_callback('/([^&]|&[^;]+;)'.chr(8).'([^&]|&[^;]+;)('.chr(8).'([^&]|&[^;]+;))?/','replace_formatting',$output);
$output=str_replace(chr(8).'/b'.chr(8).'b','',$output);
$output=str_replace(chr(8).'/u'.chr(8).'u','',$output);
$output=str_replace(chr(8).'/e'.chr(8).'e','',$output);
function replace_formatting_2($matches)
{ if ($matches[1]) return '</span>';
  else return '<span class="'.$matches[2].'">';
}
$output=preg_replace_callback('/'.chr(8).'(\/)?([bue])/','replace_formatting_2',$output);
function relative_link($matches)
{ return '<a href="?page='.strtolower($matches[2]).'+'.$matches[4].'">'.$matches[0].'</a>';
}
$output=preg_replace_callback('/(<span[^>]*>)?([a-zA-Z0-9-_\.]+)(<\/span>)?\((\d[^\s)]*)\)/','relative_link',$output);
function absolute_link($matches)
{ return '&lt;<a href="'.str_replace(' ','',$matches[3]).'">'.$matches[3].'</a>&gt;';
}
$output=preg_replace_callback('/(&lt;|&lang;)(URL:)?((https?:\/\/[^<"&]+)|(ftp:\/\/[^<"&]+))(&gt;|&rang;)/','absolute_link',$output);
function email_link($matches)
{ return '&lt;<a href="mailto:'.str_replace(' ','',$matches[2]).'">'.$matches[2].'</a>&gt;';
}
$output=preg_replace_callback('/(&lt;|&lang;)([a-zA-Z0-9_\-\.]+@[a-zA-Z0-9\-\.]+)(&gt;|&rang;)/','email_link',$output);
?>
<h1>HTML man pages</h1>
<form action="">
<p><input name="page" type="text" value="<?php echo trim($page.' '.$section); ?>"/> <input type="submit" value="Go"/></p>
</form>
<hr/>
<pre><?php echo $output; ?></pre>
<hr/>
<p><a href="http://validator.w3.org/check?uri=referer">Valid XHTML 1.0 Strict</a></p>
</body>
</html>
