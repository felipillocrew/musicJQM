
$('#editPage').live('pageshow', function(event) {
	var fnt = 'read';
	var id = getUrlVars()["id"];
	$.getJSON(serviceURL + 'mp3.php?fnt='+ fnt +'&id='+id, function(data) {
		var song = data.item;
		$('#title').text(song.titulo)
		$('#editSong #titulo').val(song.titulo)
		$('#editSong #artista').val(song.artista)
		$('#editSong #genero').val(song.genero)
		$('#editSong #album').val(song.album)
		$('#editSong #year').val(song.fecha)
		$('#editSong #candionid').val(id)
	})
	.error(function(data) {
		console.log(data);
	});
	$('#saveLink').on("tap",savedetails);
});



function savedetails(){
	var form = $('#editSong');
	$.ajax({
		type: "POST",
		url: serviceURL + form.attr('action'),
		data: form.serialize(),
		cache: false,
		dataType: "text",
		success: onSuccess
	});
}

$("#ajaxLog").ajaxError(function(event, request, settings, exception) {
	$("#ajaxLog").html("Error Calling: " + settings.url + "<br />HTPP Code: " + request.status);
});
 
function onSuccess(data)
{
	$("#ajaxLog").html("Result: " + data);
}


	