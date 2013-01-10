<?php

require_once('getid3/getid3.php');
require_once('html.class.php');
require_once('mysql.class.php');

class MP3 {

	private $id3;
	private $dir;
	private $xtn;
	private $songs;
	private $song = array();
	private $objHTML;
	private $objDB;
	private $errores = array();

	public function __construct($dir, $xtn = 'mp3') {
		$this->dir = $dir;
		$this->xtn = $xtn;
		$this->objHTML = new html();
		$this->objDB = mysql::getInstance();
	}

	public function findMp3s() {
		if ($this->dir != '') {
			$directorio = opendir($this->dir);
			while ($archivo = readdir($directorio)) {
				if (!is_dir($archivo)) {
					list(, $ext_) = explode('.', $archivo);
					if ($ext_ === $this->xtn) {
						$file = str_replace('../', '', $this->dir) . $archivo;
						$getID3 = new getID3;
						$info = $getID3->analyze($this->dir . $archivo);
						getid3_lib::CopyTagsToComments($info);

						$artista = mysql_real_escape_string($info['comments']['artist'][0]);
						$titulo = mysql_real_escape_string($info['comments']['title'][0]);
						$album = mysql_real_escape_string($info['comments']['album'][0]);
						$genero = mysql_real_escape_string($info['comments']['genre'][0]);
						$year = $info['comments']['year'][0];
						$time = $info['playtime_string'];

						$this->createIMG($info['comments']['picture'][0]['data'], $archivo);
						$infoimagen = '';
						$mineimagen = '';
						$this->saveData(array($titulo, $genero, $time, $infoimagen, $mineimagen, $artista, $album, $year, $archivo, $file));
					}
				}
			}
			closedir($directorio);
			return 'true';
		}
		else
			return 'false';
	}

	private function saveData($data = array()) {
		if (!empty($data)) {
			list($nombre, $genero, $tiempo, $cancionart, $mineart, $artista, $album, $year, $systemname, $systemrute) = $data;
			list($status, $res_c) = $this->objDB->runSQL("select cancionid from cancion where systemname='$systemname';",2);
			if ($status && empty($res_c)) {
				$artistaid = $albumid = 0;
//				$cancionart = empty($cancionart) ? 'null' : "'$cancionart'";
//				$mineart = empty($mineart) ? 'null' : "'$mineart'";
//				$year = empty($year) ? 0 : $year;
				$cancionart=  $this->objDB->comillas($cancionart,0,1);
				$mineart=  $this->objDB->comillas($mineart,0,1);
				$year=  $this->objDB->numerico($year);

				list($status, $res) = $this->objDB->runSQL("select artistaid from artista where nombre like '%$artista%';",2);
				if (empty($res)) {
					$sql = "insert into artista (nombre,artistaart,mineart) values ('$artista',$cancionart,$mineart);";
					list($status, $msg) = $this->objDB->exeSQL($sql);
					$artistaid = $this->objDB->ultimoID('artistaid', 'artista');
				} else {
					list(list($artistaid)) = $res;
				}
				list($status, $res) = $this->objDB->runSQL("select albumid from album where nombre like '%$album%';",2);
				if (empty($res)) {
					$sql = "insert into album (nombre,artistaid,albumart,mineart,fecha) values ('$album',$artistaid,$cancionart,$mineart,$year);";
					list($status, $msg) = $this->objDB->exeSQL($sql);
					if (!$status)
						$this->errores[] = $msg;
					$albumid = $this->objDB->lastID();
				}
				else {
					list(list($albumid)) = $res;
				}

				$sql = "insert into cancion (nombre,genero,tiempo,cancionart,mineart,artistaid,albumid,systemname,systemrute) values ('$nombre','$genero','$tiempo',$cancionart,$mineart,$artistaid,$albumid,'$systemname','$systemrute');";
				list($status, $msg) = $this->objDB->exeSQL($sql);
				if (!$status)
					$this->errores[] = $msg;
			}
			else
				$this->objHTML->sethtmllog("$systemname ya esta en la base de datos");
		}
		else
			$this->objHTML->sethtmllog("no existe data");
		$this->errores[] = 'no existe data';
	}

	public function loadMp3s() {
		$sql='select c.cancionid as id,c.nombre as titulo,c.cancionart as imagen,(select nombre from artista where artistaid=c.artistaid) as artista,(select nombre from album where albumid=c.albumid) as album,(select fecha from album where albumid=c.albumid) as fecha,c.genero,c.tiempo,c.systemrute as link from cancion c;';
		list($status, $res) = $this->objDB->runSQL($sql,1);
		if(!$status) return '{"error":true,"text":"' .$res. '"}';
		else{
			if(empty($res)) return '{"error":true,"text":"No hay información en la BB DD"}';
			else return '{"success":true,"items":' . json_encode($res) . '}';
		}
	}
	public function _loadMp3s() {
		if ($this->dir != '') {
			$directorio = opendir($this->dir);
			while ($archivo = readdir($directorio)) {
				if (!is_dir($archivo)) {
					list(, $ext_) = explode('.', $archivo);
					if ($ext_ === $this->xtn) {
						$file = str_replace('../', '', $this->dir) . $archivo;
						$getID3 = new getID3;
						$info = $getID3->analyze($this->dir . $archivo);
						getid3_lib::CopyTagsToComments($info);

						$artista = $info['comments']['artist'][0];
						$titulo = $info['comments']['title'][0];
						$album = $info['comments']['album'][0];
						$genero = $info['comments']['genre'][0];
						$year = $info['comments']['year'][0];
						$time = $info['playtime_string'];

						$infoimagen = $info['comments']['picture'][0]['data'];
						$mineimagen = $info['comments']['picture'][0]['image_mime'];
						$imagen = $infoimagen != '' ? 'data:' . $mineimagen . ';base64,' . base64_encode($infoimagen) : '';

						$this->song = array('link' => $file, 'titulo' => $titulo, 'artista' => $artista, 'album' => $album, 'year' => $year, 'genero' => $genero, 'time' => $time, 'imagen' => $imagen);
						$this->songs[] = $this->song;
					}
				}
			}
			closedir($directorio);
			return '{"success":true,"items":' . json_encode($this->songs) . '}';
		}
		else
			return '{"error":true,"text":"' . "El directorio no existe" . '"}';
	}

	function createIMG($imagedata, $archivo) {
		if ($imagedata != '') {
			$imagedata = base64_decode($imagedata);
			$img = imagecreatefromstring($imagedata);
			if ($img !== false) {
				//imagejpeg($img, '../images/' . str_replace('.mp3', '.jpg', $archivo));
				if(imagejpeg($img, str_replace('.mp3', '.jpg', $archivo))){
					$this->objHTML->sethtmllog("creada correctamente en ". str_replace('.mp3', '.jpg', $archivo));
				}
				else $this->objHTML->sethtmllog("no se pudo guardar la imagen");
				imagedestroy($img);
			}
			else $this->objHTML->sethtmllog("no se creo la imagen");
		}
	}

	public function readTag($req) {
		list($id) = $this->objHTML->datosform('id', $req);
		$id=  $this->objDB->numerico($id);
		$sql="select c.cancionid as id,c.nombre as titulo,c.cancionart as imagen,(select nombre from artista where artistaid=c.artistaid) as artista,(select nombre from album where albumid=c.albumid) as album,(select fecha from album where albumid=c.albumid) as fecha,c.genero,c.tiempo,c.systemrute as link from cancion c where c.cancionid=$id;";
		list($status, $res) = $this->objDB->runSQL($sql,1);
		$this->objHTML->sethtmllog("readTag: " . print_r($res, true));
		if(!$status) return '{"error":true,"text":"' .$res. '"}';
		else{
			if(empty($res)) return '{"error":true,"text":"No hay información en la BB DD"}';
			else return '{"success":true,"item":' . json_encode($res[0]) . '}';
		}

	}
	
	public function _readTag($req) {
		list($file) = $this->objHTML->datosform('file', $req);
		if ($file != '') {
			$file = '../' . $file;
			$getID3 = new getID3;
			$info = $getID3->analyze($file);
			getid3_lib::CopyTagsToComments($info);

			$artista = $info['comments']['artist'][0];
			$titulo = $info['comments']['title'][0];
			$album = $info['comments']['album'][0];
			$genero = $info['comments']['genre'][0];
			$year = $info['comments']['year'][0];
			$time = $info['playtime_string'];
			$tags = $info['tags'];

			$infoimagen = $info['comments']['picture'][0]['data'];
			$mineimagen = $info['comments']['picture'][0]['image_mime'];
			$imagen = $infoimagen != '' ? 'data:' . $mineimagen . ';base64,' . base64_encode($infoimagen) : '';

			$this->song = array('link' => str_replace('../', '', $file), 'titulo' => $titulo, 'artista' => $artista, 'album' => $album, 'year' => $year, 'genero' => $genero, 'time' => $time, 'imagen' => $imagen, 'tags' => $tags);
			return '{"success":true,"item":' . json_encode($this->song) . '}';
		}
		else
			return '{"error":true,"text":"' . "El archivo no existe" . '"}';
	}

	function writeTag($req) {
		list($file, $titulo, $artista, $album, $genero, $year) = $this->objHTML->datosform('file,titulo,artista,album,genero,year', $req);

		$TaggingFormat = 'UTF-8';
		$getID3 = new getID3;
		$getID3->setOption(array('encoding' => $TaggingFormat));

		require_once('getid3/write.php');
		// Initialize getID3 tag-writing module
		$tagwriter = new getid3_writetags;
		$tagwriter->filename = '../' . htmlspecialchars(urldecode($file));
		$tagwriter->tagformats = array('id3v1', 'id3v2.3', 'id3v2.4');

		// set various options 
		$tagwriter->tag_encoding = $TaggingFormat;
		$tagwriter->overwrite_tags = false;
		$tagwriter->remove_other_tags = false;

		// populate data array
		$TagData['title'][] = $titulo;
		$TagData['artist'][] = $artista;
		$TagData['album'][] = $album;
		$TagData['genre'][] = $genero;
		$TagData['year'][] = $year;

		$tagwriter->tag_data = $TagData;
		$s = '';
		if ($tagwriter->WriteTags()) {
			$s.= 'Successfully wrote tags<BR>';
			if (!empty($tagwriter->warnings))
				$s.= 'There were some warnings:<BLOCKQUOTE STYLE="background-color:#FFCC33; padding: 10px;">' . implode('<BR><BR>', $tagwriter->warnings) . '</BLOCKQUOTE>';
		} else
			$s.= 'Failed to write tags!<BLOCKQUOTE STYLE="background-color:#FF9999; padding: 10px;">' . implode('<BR><BR>', $tagwriter->errors) . '</BLOCKQUOTE>';

		return $s;
	}

	public function nav($req) {
		list($fnt) = $this->objHTML->datosform('fnt', $req);
		switch ($fnt) {
			case 'list':
				return $this->loadMp3s();
				break;
			case 'read':
				return $this->readTag($req);
				break;
			case 'write':
				return $this->writeTag($req);
				break;
			case 'find':
				return $this->findMp3s();
				break;

			default:
				return $this->loadMp3s();
				break;
		}
	}

}

$dir = '../mp3/';
$req = $_REQUEST;
$mp3 = new MP3($dir);
echo $mp3->nav($req);
?>