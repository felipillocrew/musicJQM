<?php

class html {
    function textarea($text, $class = '', $name = 'nota', $rows = 8, $cols = 70) {
        $text = "<textarea class=\"$class\" rows=\"$rows\" cols=\"$cols\" name=\"$name\" id=\"$name\">$text</textarea>";
        return $text;
    }

    function a($text, $url, $js = '', $class = '') {
        if ($url != '')
            $url = 'href="' . $url . '"';
        if ($class != '')
            $class = 'class="' . $class . '"';
        $a = '<a ' . $class . ' ' . $url . ' ' . $js . '>' . $text . '</a>';
        return $a;
    }

    function nbsp($text) {
        return str_replace(' ', '&nbsp;', $text);
    }
    
    function htmlpage($head, $body) {
        $html = "<html>
				$head
				$body
				</html>";
        return $html;
    }

    function body($content, $class = '') {
        $body = "<body class=\"$class\">
				$content
			   </body>";
        return $body;
    }

    function head($js = '', $css = '', $titulo = 'FelipilloCrew', $icon = '', $meta = '') {
        $head = "<head>
				$meta
				$js
				$css
				$icon
				<title>$titulo</title>
			   </head>";
        return $head;
    }

    function urlscript($url) {
        $script = "<script type=\"text/javascript\" src=\"$url\"></script>\n";
        return $script;
    }

    function urlcss($url, $media = '') {
        $css = "<link rel=\"stylesheet\" href=\"$url\" type=\"text/css\" media=\"$media\" />\n";
        return $css;
    }

    function icon($url) {
        $icon = "<link rel=\"shortcut icon\" href=\"$url\" type=\"image/x-icon\" />";
        return $icon;
    }

    function meta() {
        return '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">';
    }

    function script($js) {
        $script = "<script type=\"text/javascript\">
			$js</script>";
        return $script;
    }

    function style($css) {
        $style = "<style type=\"text/css\">
			$css</style>";
        return $style;
    }

    function combostring($name, $nombrevalor, $values, $id = 0, $js = "", $addlist = "") {
        $combo = "<select name=\"$name\" id=\"$name\" $js \">\n";
        if ($addlist <> "") {
            $item = explode("|", $addlist);
            $combo.="<option value=\"" . $item[1] . "\">" . $item[0] . "</option>\n";
        }
        $nombres = explode(",", $nombrevalor);
        $valores = explode(",", $values);
        for ($i = 0; $i < count($valores); $i++) {

            if ($id == $valores[$i]) {
                $combo.= "<option value=\"" . $valores[$i] . "\" selected>" . $nombres[$i] . "</option>\n";
            } else {
                $combo.= "<option value=\"" . $valores[$i] . "\">" . $nombres[$i] . "</option>\n";
            }
        }
        $combo.= "</select>\n\n";
        return $combo;
    }

    function mayuscula($texto, $modo = 2) {
        if ($modo == 1) $texto = strtoupper($texto);
        else if ($modo == 2) $texto = ucwords(strtolower($texto));
        else if ($modo == 3)  $texto = ucfirst(strtolower($texto));
        else if ($modo == 4) $texto = strtolower($texto);
        else $texto = $texto;
        return $texto;
    }

    function pagerefesh($url = '', $time = 1) {
        if ($url == '') $refresh = "<meta http-equiv=\"refresh\" content=\"$time\" >";
        else $refresh = "<meta http-equiv=\"refresh\" content=\"$time;url=$url\">";
        return $refresh;
    }

    function refresh($page, $time = 100) {
        return "setInterval( \"$page;\", $time );";
    }

    function onload($fnt) {
        return "<img src=\"img/1x1.gif\" onload=\"javascript:$fnt;\">";
    }

    function tochecked($valor) {
        return $valor == 1 ? 'value="1" checked' : 'value="0"';
    }

    

    /*     * ****************************METODOS INTELIGENTES************************************************ */

    function datosform($variables, $post) {
        $datos = array();
        $i = 0;
        $variables = explode(',', $variables);
        foreach ($variables as $id => $var) {
            foreach ($post as $campo => $valor) {
                if ($var == $campo)
                    $datos[] = $valor;
            }
        }
        return $datos;
    }

    function zipped() {
        $args = func_get_args();

        $ruby = array_pop($args);
        if (is_array($ruby))
            $args[] = $ruby;

        $counts = array_map('count', $args);
        $count = ($ruby) ? min($counts) : max($counts);
        $zipped = array();

        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < count($args); $j++) {
                $val = (isset($args[$j][$i])) ? $args[$j][$i] : null;
                $zipped[$i][$j] = $val;
            }
        }
        return $zipped;
    }

    public function sethtmllog($text) {
        $fp = fopen("logs/htmllog.txt", "a");
        fwrite($fp, date('d/m/Y h:i:s A') ."  ". $text . PHP_EOL);
        fclose($fp);
    }
}
?>
