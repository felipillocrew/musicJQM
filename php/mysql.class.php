<?php

class mysql {

	private $conexion;
	private $resource;
	private $sql;
	private $error = '';
	private $numerror = 0;
	private $numFields = 0;
	private $db = 'felipill_music';
	public static $queries;
	private static $_singleton;

	public static function getInstance() {
		if (is_null(self::$_singleton)) {
			self::$_singleton = new mysql();
		}
		return self::$_singleton;
	}

	private function __construct() {
		$this->conexion = @mysql_connect('localhost', 'felipill_chris', 'c706180t');
		mysql_select_db($this->db, $this->conexion);
		@mysql_query("SET NAMES 'utf8'", $this->conexion);
		$this->queries = 0;
		$this->resource = null;
	}

	public function execute() {
		$this->error = '';
		$this->numerror = 0;

		if (!($this->resource = mysql_query($this->sql, $this->conexion))) {
			$this->error = mysql_error($this->conexion);
			$this->numerror = mysql_errno($this->conexion);
			return false;
		}
		$this->queries++;
		$this->log($this->sql);
		if (substr_compare($this->sql, 'select', 0, strlen('select'), true) == 0)
			$this->numFields = mysql_num_fields($this->resource);
		else
			$this->numFields = 1;
		return $this->resource;
	}

	public function alter() {
		if (!($this->resource = mysql_query($this->sql, $this->conexion))) {
			return false;
		}
		return true;
	}

	function nbsp($text) {
		return str_replace(' ', '&nbsp;', $text);
	}

	public function getAffectedRows() {
		return mysql_affected_rows($this->resource);
	}

	public function loadObjectList() {
		if (!($cur = $this->execute())) {
			$this->error = mysql_error($this->conexion);
			$this->numerror = mysql_errno($this->conexion);

			return false;
		}
		$array = array();
		while ($row = @mysql_fetch_object($cur)) {
			$array[] = $row;
		}
		return $array;
	}

	function loadAssocList($key = '') {
		if (!($cur = $this->execute())) {
			$this->error = mysql_error($this->conexion);
			$this->numerror = mysql_errno($this->conexion);

			return false;
		}
		$array = array();
		while ($row = mysql_fetch_assoc($cur)) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysql_free_result($cur);
		return $array;
	}

	function loadRowList($key = null) {
		$this->error = '';
		$this->numerror = 0;
		if (!($cur = $this->execute())) {
			$this->error = mysql_error($this->conexion);
			$this->numerror = mysql_errno($this->conexion);

			return false;
		}
		$array = array();
		while ($row = mysql_fetch_row($cur)) {
			if ($key !== null) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysql_free_result($cur);
		return $array;
	}

	public function setQuery($sql) {
		$this->error = '';
		$this->numerror = 0;
		if (empty($sql)) {
			$this->error = 'El Query esta vacido';
			return false;
		}
		$this->sql = $sql;
		return true;
	}

	public function freeResults() {
		@mysql_free_result($this->resource);
		return true;
	}

	public function loadObject() {
		$this->error = '';
		$this->numerror = 0;
		if ($cur = $this->execute()) {
			if ($object = mysql_fetch_object($cur)) {
				@mysql_free_result($cur);
				return $object;
			} else {
				$this->error = mysql_error($this->conexion);
				$this->numerror = mysql_errno($this->conexion);

				return false;
			}
		} else {
			$this->error = mysql_error($this->conexion);
			$this->numerror = mysql_errno($this->conexion);
			return false;
		}
	}

	public function sqlerror($ssql = true) {
		$sql = ' SQL=' . $this->sql;
		if (!$ssql)
			$sql = '';
		return $this->error . $sql;
	}

	public function noerror() {
		return $this->numerror;
	}

	public function fields() {
		return $this->numFields;
	}

	public function log($sql) {
		$fp = fopen("logs/dblog.txt", "a");
		$error = '';
		if ($this->error != '')
			$error = "Error --> $this->error";
		fwrite($fp, date('d/m/Y h:i:s A') . " Query --> $sql \n" . date('d/m/Y h:i:s A') . " $error" . PHP_EOL);
		fclose($fp);
	}

	public function setdblog($text) {
		$fp = fopen("logs/dblog.txt", "a");
		fwrite($fp, date('d/m/Y h:i:s A') . "  " . $text . PHP_EOL);
		fclose($fp);
	}

	function __destruct() {
		@mysql_free_result($this->resource);
		@mysql_close($this->conexion);
	}

	/*	 * ****************CLASS MYSQL************************ */

	function runSQL($sql, $asoc = 1) {
		$this->setQuery($sql);
		if ($asoc == 2) {
			if (!$row = $this->loadRowList()) {
				if ($this->noerror() <> 0)
					return array(false, '<span><font color="red"><i><b>' . $this->noerror() . ':' . $this->sqlerror() . '</b></i></font></span>');
				else
					return array(true, array());
			}
			else
				return array(true, $row);
		}
		else {
			if (!$row = $this->loadAssocList()) {
				if ($this->noerror() <> 0)
					return array(false, '<span><font color="red"><i><b>' . $this->noerror() . ':' . $this->sqlerror() . '</b></i></font></span>');
				else
					return array(true, array());
			}
			else
				return array(true, $row);
		}
	}

	function exeSQL($sql) {
		$this->setQuery($sql);
		if (!$r = $this->execute()) {
			$res = array(false, '<span><font color="red"><i><b>' . $this->noerror() . ':' . $this->sqlerror() . '</b></i></font></span>');
		} else {
			$res = array(true, '<span><font color="green"><i><b>Correcto</b></i></font></span>');
		}

		return $res;
	}

	function query($sql) {
		$this->setQuery($sql);
		if (!$r = $this->execute())
			$res = array(false, $this->noerror(), 0,);
		else
			$res = array(true, $r, $this->fields());
		return $res;
	}

	function ultimoID($id, $tabla) {
		list ($status, list(list($id))) = $this->runSQL("select max($id) from $tabla", 2);
		return $id;
	}

	function nextID($tabla) {
		list ($status, list(list($id))) = $this->runSQL("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$this->db' AND TABLE_NAME = '$tabla'", 2);
		return $id;
	}

	function lastID() {
		return mysql_insert_id($this->conexion);
	}

	function actionselect($usuario, $DBid, $campos, $tabla, $where, $php, $fnt1 = 'delete', $fnt2 = 'edit') {
		$evento = 'Anular';
		if ($this->isadmin($usuario)) {
			$borrar = 'concat( "<a href=\"javascript:jsconfirmeliminar(\'' . $php . '\',\'' . $fnt1 . '\',\'",' . $DBid . ',"\',\'' . $evento . '\')\">", "<img src=\'img/delete.png\' width=\'32\' align=\'center\'>", "</a>" ) as borrar';
			$editar = 'concat( "<a href=\"javascript:setfunctionid(\'' . $php . '\',\'' . $fnt2 . '\',\'",' . $DBid . ',"\')\">", "<img src=\'img/edit.png\'  width=\'32\' align=\'center\'>", "</a>" ) as editar';
		} else {
			$borrar = '"<img src=\'img/delete.png\' width=\'32\' align=\'center\'>" as borrar';
			$editar = '"<img src=\'img/edit.png\'  width=\'32\' align=\'center\'>" as editar';
		}
		$sql = "select $campos,$editar,$borrar from $tabla where $where;";

		return $sql;
	}

	function newactionselect($usuario, $DBid, $campos, $tabla, $where, $php, $fnt1 = 'delete', $fnt2 = 'edit') {
		if ($this->isadmin($usuario))
			$borrar = 'concat( "<a href=\"javascript:jsconfirmeliminar(\'' . $php . '\',\'' . $fnt1 . '\',\'",' . $DBid . ',"\',\'' . $evento . '\')\">", "<img src=\'img/delete.png\' width=\'32\' align=\'center\'>", "</a>" ) as borrar';
		else
			$borrar = '"<img src=\'img/delete.png\' width=\'32\' align=\'center\'>" as borrar';
		$evento = 'Anular';
		$sql = "select $campos,$borrar from $tabla where $where;";

		return $sql;
	}

	function mkselect($campos, $tabla, $where) {
		$sql = "select $campos from $tabla where $where;";
		return $sql;
	}

	function makeWhere($values, $opc = '', $conector = 'and') {
		$opc.=' ';
		$mk = $opc;

		foreach ($values as $value) {
			if ($value <> '')
				$mk.=" $value $conector";
		}
		$mk = substr($mk, 0, -(strlen($conector)));
		return $mk;
	}

	function listaID($idname, $tabla, $where = '') {
		if ($where <> '')
			$where = "where $where";

		$sql = "select $idname from $tabla $where;";
		list($status, $ids) = $this->runSQL($sql, 2);
		$lista = '';

		foreach ($ids as $key => $value) {
			foreach ($value as $id => $data)
				$lista.="$data,";
		}
		$lista = substr($lista, 0, -1);
		return $lista;
	}

	function getID($idname, $where, $tabla) {
		if ($this->IDexiste($idname, $where, $tabla) == true) {
			$sql = "select $idname from $tabla where $where;";
			list($status, $row) = $this->runSQL($sql, 2);
			list(list($clave)) = $row;
			return $clave;
		}
	}

	function existeID($DBfield, $where, $tabla) {
		$sql = "select $DBfield from $tabla where $where;";
		list($status, $row) = $this->runSQL($sql, 2);
		if (empty($row))
			$clave = 0;
		else
			list(list($clave)) = $row;
		return $clave;
	}

	function IDexiste($DBfield, $where, $tabla) {
		$sql = "select $DBfield from $tabla where $where;";
		list($status, $row) = $this->runSQL($sql, 2);
		if (empty($row))
			$clave = false;
		else {
			list(list($clave)) = $row;
			if ($clave <> '')
				$clave = true;
		}
		return $clave;
	}

	/*	 * ****************HTML****** */

	function paginaciondb2htmltable($div, $sql, $header, $titulo = '', $php = '', $pagina = 1, $dbid = 0, $border = 0, $cellspacing = 2, $cellpadding = 2, $align = 'left') {
		$html = '';
		$limit = 10;
		$total = 0;
		if ($pagina < 1)
			$pagina = 1;
		$sql = str_replace(';', '', $sql);
		list($status, $rows) = $this->runSQL($sql);
		$total = count($rows);
		if (ceil($total / $limit) < $pagina)
			$offset = (ceil($total / $limit) - 1) * $limit;
		else
			$offset = ($pagina - 1) * $limit;
		$sql.=" LIMIT $offset, $limit";
		list($status, $rows) = $this->runSQL($sql);
		if (!$status)
			$html = "<h2>ERROR</h2> <div class=\"error\">$rows:::$sql</div>";
		else {
			if (!empty($rows)) {

				$html = " <div id=\"$div\">
						<h2>$titulo</h2>
						<table border=\"$border\" cellspacing=\"$cellspacing\" cellpadding=\"$cellpadding\" style=\"width: 100%; text-align: left;\">
						";
				if ($header <> '') {
					$tilulos = explode(',', $header);
					$html.='<thead>';
					foreach ($tilulos as $key => $value) {
						$html.="<th style=\"vertical-align:middle; text-align: $align;\">$value</th>";
					}
					$html.='</thead>';
				}

				$html.='<tbody>';
				$count = 0;
				$numero = 0;
				foreach ($rows as $key => $value) {
					$html.="<tr>";
					$count++;
					$numero = (($pagina - 1) * $limit) + $count;
					$html.="<td style=\"vertical-align:middle; text-align: $align;\">$numero</td>";
					foreach ($value as $id => $data) {
						$html.="<td style=\"vertical-align:middle; text-align: $align;\">$data</td>";
					}
					$html.='</tr>';
				}
				$html.="</tbody>
						<div class=\"pagination center\">";
				$totalPag = ceil($total / $limit);
				$links = array();
				for ($i = 1; $i <= $totalPag; $i++) {
					if ($i == $pagina)
						$links[] = "<a href=\"javascript:setparam('$php','fnt=participantes&id=$dbid&pag=$i')\" class=\"page-active radius\">$i</a>";
					else
					if ($i == $totalPag)
						$links[] = "<a href=\"javascript:setparam('$php','fnt=participantes&id=$dbid&pag=$i')\" class=\"page radius\">$i</a>";
					else
						$links[] = "<a href=\"javascript:setparam('$php','fnt=participantes&id=$dbid&pag=$i')\" class=\"page radius\">$i</a>";
				}
				$html.= implode(" ", $links);

				$html.="</div> </table>
						</div>";
			}
		}
		return $html;
	}

	function combosql($name, $sql, $id = 0, $js = "", $addlist = "", $msg = '<---No hay resultados--->') {
		$combo = "<select name=\"$name\" $js>\n";
		if ($addlist <> "") {
			$item = explode("|", $addlist);
			if ($id == $item[1])
				$combo.="<option value=\"$item[1]\">$item[0]</option>\n";
		}
		list($status, $rows) = $this->runSQL($sql, 2);
		if (empty($rows))
			$combo.="<option value=\"-1\" selected>$msg</option>";
		else {
			foreach ($rows as $key => $value) {
				if ($id == $value[0])
					$combo.= "<option value=\"$value[0]\" selected>$value[1]</option>\n";
				else
					$combo.= "<option value=\"$value[0]\">$value[1]</option>\n";
			}
		}
		$combo.= "</select>\n\n";
		return $combo;
	}

	/*	 * ****************SESION**************************** */

	public function getuserid($usuario) {
		$userid = $this->getID('id', "usuario='$usuario'", 'usuarios');
		return $userid;
	}

	public function getusertype($usuario) {
		$usertype = $this->getID('usertype', "usuario='$usuario'", 'usuarios');
		return $usertype;
	}

	public function isadmin($usuario) {
		$usertype = $this->getusertype($usuario);
		/* Administrator=5
		 * Super Administrator=6
		 */
		$isadmin = false;
		if ($usertype == '5' or $usertype == '6')
			$isadmin = true;
		return $isadmin;
	}

	public function issudo($usuario) {
		$issuadmin = false;
		$usertype = $this->getusertype($usuario);
		if ($usertype == '6')
			$issuadmin = true;
		return $issuadmin;
	}

	public function iscatequista($usuario) {
		$iscatequista = false;
		$userid = $this->getuserid($usuario);
		$rolid = $this->getID('rolid', "user=$userid", 'catequistas');
		if ($rolid == 1)
			$iscatequista = true;
		return $iscatequista;
	}

	public function catequistaid($usuario, $prefijo = '') {
		$catequistaid = '';
		if ($this->isadmin($usuario) == false) {
			$userid = $this->getuserid($usuario);
			$catequistaid = $this->getID('catequistaid', "user=$userid", 'catequistas');
			if (empty($prefijo) == false)
				$catequistaid = "and $prefijo" . "catequistaid=$catequistaid";
		}
		else {
			if (empty($prefijo) == true)
				$catequistaid = 0;
		}
		return $catequistaid;
	}

	public function capillaid($usuario, $prefijo = '') {
		$capillaid = '';
		if ($this->isadmin($usuario) == false) {
			$userid = $this->getuserid($usuario);
			$capillaid = $this->getID('iglesiaid', "user=$userid", 'catequistas');
			if (empty($prefijo) == false)
				$capillaid = "and $prefijo" . "iglesiaid=$capillaid";
		}
		else {
			if (empty($prefijo) == true)
				$capillaid = 0;
		}
		return $capillaid;
	}

	public function comunidadid($usuario, $prefijo = '') {
		$comunidadid = '';
		if ($this->isadmin($usuario) == false) {
			$userid = $this->getuserid($usuario);
			$comunidadid = $this->getID('comunidadid', "user=$userid", 'catequistas');
			if (empty($prefijo) == false)
				$comunidadid = "and $prefijo" . "comunidadid=$comunidadid";
		}
		else {
			if (empty($prefijo) == true)
				$comunidadid = 0;
		}
		return $comunidadid;
	}

	public function grupoid($usuario, $prefijo = '', $catequistaid = 0) {
		$grupoid = '';
		if ($this->isadmin($usuario) == false) {
			$grupoid = $this->getID('distinct grupoid', "catequistaid=" . $this->catequistaid($usuario), 'jovenes');
			if (empty($prefijo) == false)
				$grupoid = "and $prefijo" . "grupoid=$grupoid";
			else
				$grupoid = "and grupoid in (0,$grupoid)";
		}
		else {
			if ($catequistaid <> 0) {
				$grupoid = $this->getID('distinct grupoid', "catequistaid=$catequistaid", 'jovenes');
				if (empty($prefijo) == true)
					$grupoid = "and grupoid in (0,$grupoid)";
				else
					$grupoid = "and $prefijo" . "grupoid=$grupoid";
			}
			else
				$grupoid = "and grupoid in(0," . $this->listaID('grupoid', 'grupos') . ")";
		}
		return $grupoid;
	}

	/*	 * ****************************VALIDACION*************************************** */

	public function isdigit($numero) {
		$numero = strval($numero);
		$numero = str_split($numero);
		foreach ($numero as $value) {
			if (!ctype_digit($value))
				return false;
		}
		return true;
	}

	public function fechaendate($fecha, $outformato = 'yyyy-mm-dd', $concomillas = 1) {
		$dm = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		if ($fecha == null)
			return "NULL";
		$fecha = trim($fecha);
		$fecha = str_replace(" ", "", $fecha);

		if (strstr($fecha, "/") != false)
			$fechapartes = split("/", $fecha);
		else if (strstr($fecha, "-") != false)
			$fechapartes = split("-", $fecha);
		else
			return "NULL";


		if (count($fechapartes) != 3)
			return "NULL";
		if (!($this->isdigit($fechapartes[0]) and $this->isdigit($fechapartes[1]) and $this->isdigit($fechapartes[2])))
			return "NULL";

		$dia = intval($fechapartes[0]);
		$mes = intval($fechapartes[1]);
		$ano = intval($fechapartes[2]);

		if (strlen($fechapartes[2]) <= 2) {
			if ($ano < 30)
				$ano += 2000;
			else
				$ano += 1900;
			$fechapartes[2] = strval($ano);
		}

		if ($mes > 12 or $mes < 1)
			return "NULL";
		if ($dia > $dm[$mes - 1])
			return "NULL";
		if (($ano % 4) != 0 and $mes == 2 and $dia > 28)
			return "NULL";

		if ($outformato == 'dd/mm/yyyy')
			$fecha = implode($fechapartes, '/');
		else if ($outformato == 'dd-mm-yyyy')
			$fecha = implode($fechapartes, '-');
		else if ($outformato == 'yyyy-mm-dd')
			$fecha = implode(array_reverse($fechapartes), '-');
		else if ($outformato == 'yyyy/mm/dd')
			$fecha = implode(array_reverse($fechapartes), '/');


		if ($concomillas == 1)
			return "'$fecha'";
		return $fecha;
	}

	public function fechaents($s, $concomillas = 1) {
		$s = trim($s);
		$s = str_replace("'", "", $s);
		$l = split(" ", $s);
		$n = count($l);
		if ($f == "NULL")
			return "NULL";

		if ($n == 1) {
			if ($concomillas == 1)
				return "'$f'";
			return $f;
		}

		if ($n != 2)
			return "NULL";
		$h = $l[1];
		$l = split(":", $h);
		if (count($l) == 2 or count($l) == 3)
			$l = $this->numericolista($l, 0, -1);
		else
			return "NULL";

		if ($concomillas == 1)
			return "'$s'";
		return s;
	}

	public function numericolista($lista, $ceroesnulo, $digitos) {
		foreach ($lista as $key => $value) {
			$lista[$key] = $this->numerico($value, $ceroesnulo, 0, $digitos);
		}

		return $lista;
	}

	public function numerico($numero, $ceroesnulo = 0, $nuloesnulo = 0, $digitos = -1) {
		if ($numero == '')
			$numero = "NULL";
		if (strtoupper($numero) == "NULL" and $nuloesnulo == 1)
			return "NULL";

		$numero = str_replace("$", "", $numero);
		$numero = str_replace("B/.", "", $numero);
		$numero = str_replace(",", "", $numero);
		$numero = trim($numero);
		$numero = str_replace("  ", "", $numero);
		$s = str_replace(".", "", $numero);
		$sa = str_split($s);

		foreach ($sa as $value) {
			if (!ctype_digit($value)) {
				$numero = "0";
				break;
			}
		}

		if ($ceroesnulo == 1 and $numero == "0")
			return "NULL";

		if ($digitos != -1) {
			$d = floatval($numero);
			$d = round($d, $digitos);
			$numero = strval($d);
		}
		return $numero;
	}

	public function nowtimestamp($concomillas = 1, $formato = "") {
		if ($formato == "")
			$formato = "d/m/Y h:i:s";
		else if ($formato == "dd/mm/yyyy")
			$formato = "d/m/Y h:i:s";
		else if ($formato == "yyyy-mm-dd")
			$formato = "Y-m-d h:i:s";
		else if ($formato == "dd-mm-yyyy")
			$formato = "d-m-Y h:i:s";
		else if ($formato == "yyyy/mm/dd")
			$formato = "Y/m/d h:i:s";
		$fecha = date($formato);
		if ($concomillas == 1)
			return "'$fecha'";
		return $fecha;
	}

	public function nowdate($concomillas = 0, $formato = "") {
		if ($formato == "")
			$formato = "d/m/Y";
		else if ($formato == "dd/mm/yyyy")
			$formato = "d/m/Y";
		else if ($formato == "yyyy-mm-dd")
			$formato = "Y-m-d";
		else if ($formato == "dd-mm-yyyy")
			$formato = "d-m-Y";
		else if ($formato == "yyyy/mm/dd")
			$formato = "Y/m/d";
		$fecha = date($formato);
		if ($concomillas == 1)
			return "'$fecha'";
		return $fecha;
	}

	public function comillas($s, $largo = 0, $comillas = 0) {
		if ($s == null or strtoupper($s) == "NULL" or $s == "")
			return "NULL";
		$s = trim($s);
		$s = str_replace("  ", " ", $s);
		$s = str_replace("'", "", $s);

		if ($s == "")
			return "NULL";

		if ($largo != 0 and strlen($s) > $largo)
			$s = substr($s, 0, $largo - 1);
		if ($comillas != 0)
			$s = "'$s'";
		return $s;
	}

	public function comillasu($s, $largo = 0) {
		$s = $this->comillas($s, $largo);
		return strtoupper($s);
	}

	public function comillasl($s, $largo = 0) {
		$s = $this->comillas($s, $largo);
		return strtolower($s);
	}

	public function comillaslike($s) {
		if ($s == "" or $s == "NULL")
			return "'%'";

		$s = str_replace("'", "", $s);
		$s = str_replace("\"", "", $s);
		$s = str_replace(" ", "%", $s);
		$s = trim($s);
		$s = "%$s%";
		return $this->comillas($s);
	}

	private function esnumerico($string) {
		$string = string . replace("$", "", $string);
		$string = string . replace("B/.", "", $string);
		$string = string . replace(",", "", $string);
		$string = trim($string);
		$string = string . replace(" ", "", $string);
		$string = string . replace("\"", "", $string);
		$string = string . replace(".", "", $string);
		$string = string . replace("-", "", $string);
		return ctype_digit($string);
	}

}

?>