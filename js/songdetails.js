$('#detailsPage').live('pageshow', function(event) {
	var id = getUrlVars()["id"];
	var fnt = 'read';
	$.getJSON(serviceURL + 'mp3.php?fnt='+ fnt +'&id='+id, displayData)
	.error(function(data) {
		console.log(data);
	});
});

function displayData(data) {
	console.log(data);
	var song = data.item;
	$('#albumArt').attr('src', song.imagen!= null ? song.imagen : "images/no_cancion_art.jpg");
	$('#title').text(song.titulo + ' - ' + song.artista);
	$('#titulo').text(song.titulo);
	$('#audio').append('<audio src="'+song.link+ '" controls></audio>');
	$('#artista').text(song.artista);
	$('#album').text(song.album);
	$('#genero').text(song.genero);
	$('#year').text(song.fecha);
	$('#file').text(song.link);
	$('#file').hide();
       
	if (song.link) {
		$('#actionList').append('<li><a id="editLink" href="editdetails.html?id=' + song.id + '"><h3>Editar</h3></a></li>');
		$('#actionList').append('<li><a id="downLink" href="' + song.link + '"><h3>Descargar</h3></a></li>');
	}
	$('#actionList').listview('refresh');
	
}

function getUrlVars() {
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	return vars;
}
