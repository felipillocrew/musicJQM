var serviceURL = "http://www.felipillocrew.com/music/php/";

var musica;
var msg=$('#msgbox');

$('#MusicListPage').bind('pageinit', function(event) { 
	msg.hide();
	getMusicList();
	$('#msgtext').text('Listo');
	msg.show();
	
});

function getMusicList() {
	$('#msgbox').on("tap",hidebox);
	$.getJSON(serviceURL + 'mp3.php', function(data) {
		$('#musicaLista li').remove();
		musica = data.items;
		$.each(musica, function(index, song) { 
			var imagen = song.imagen!= null ? '<img src="' + song.imagen + '"/>' : '<img src="images/no_cancion_art.jpg"/>';
			$('#musicaLista').append('<li><a href="songdetails.html?id=' + song.id + '">' +
				imagen +
				'<h3 class="ui-li-heading">' + song.artista + ' - ' + song.titulo +'</h3>'+
				'<p class="ui-li-desc">' + song.album + ',' + song.fecha + ',' + song.genero + '</p>'+
				'<p class="ui-li-desc">' + song.tiempo + '</p></a></li>'
				);
		});
		$('#musicaLista').listview('refresh');
	})
	.error(function(data) {
		console.log(data);
	});
	
}

function hidebox(){
	$('#msgbox').hide();
}



var json = {
	"success":true,
	"items":[{
		"id":"1",
		"titulo":"Suavemente",
		"imagen":null,
		"artista":"Elvis Crespo",
		"album":"Suavemente",
		"fecha":"1998",
		"genero":"Merengue",
		"tiempo":"04:29:00",
		"link":"mp3\/Suavemente.mp3"
	},{
		"id":"2",
		"titulo":"Carolina",
		"imagen":null,
		"artista":"Eddy Herrera",
		"album":"Amame",
		"fecha":"1998",
		"genero":"Merengue",
		"tiempo":"04:50:00",
		"link":"mp3\/03 Carolina.mp3"
	},{
		"id":"3",
		"titulo":"Crazy",
		"imagen":null,
		"artista":"Seal",
		"album":"La Quiero a Morir",
		"fecha":"1986",
		"genero":"Pop Americano",
		"tiempo":"04:30:00",
		"link":"mp3\/Crazy.mp3"
	},{
		"id":"4",
		"titulo":"I Get Money-hiphopdons.com",
		"imagen":null,
		"artista":"50 Cent",
		"album":"Tu Dices Que Me Amas",
		"fecha":"2010",
		"genero":"Other",
		"tiempo":"03:46:00",
		"link":"mp3\/02 I Get Money.mp3"
	},{
		"id":"5",
		"titulo":"Este es mi Rap",
		"imagen":null,
		"artista":"Porta",
		"album":"No es cuesti\u00f3n de edades",
		"fecha":"2006",
		"genero":"Rap",
		"tiempo":"04:03:00",
		"link":"mp3\/Porta - este es mi rap.mp3"
	},{
		"id":"6",
		"titulo":"La Quiero a Morir",
		"imagen":null,
		"artista":"Sergio Vargas",
		"album":"La Quiero a Morir",
		"fecha":"1986",
		"genero":"Merengue",
		"tiempo":"07:55:00",
		"link":"mp3\/La Quiero a Morir.mp3"
	},{
		"id":"7",
		"titulo":"Tu Dices Que Me Amas",
		"imagen":null,
		"artista":"Amadis",
		"album":"Tu Dices Que Me Amas",
		"fecha":"2010",
		"genero":"Reggae",
		"tiempo":"03:08:00",
		"link":"mp3\/01-amadis-tu_dices_que_me_amas-rdx.mp3"
	},{
		"id":"8",
		"titulo":"Me Love",
		"imagen":null,
		"artista":"Sean Kingston",
		"album":"title",
		"fecha":"2000",
		"genero":"Other",
		"tiempo":"03:24:00",
		"link":"mp3\/01 Me Love.mp3"
	},{
		"id":"9",
		"titulo":"Stand By Me",
		"imagen":null,
		"artista":"Prince Royce",
		"album":"Prince Royce",
		"fecha":"2010",
		"genero":"Bachata",
		"tiempo":"03:25:00",
		"link":"mp3\/Prince_Royce_-_Stand_By_Me.mp3"
	},{
		"id":"10",
		"titulo":"Tempted to touch",
		"imagen":null,
		"artista":"Rupee",
		"album":"Fidel Cashflow",
		"fecha":"2005",
		"genero":"House",
		"tiempo":"03:36:00",
		"link":"mp3\/05 Tempted to touch.mp3"
	},{
		"id":"11",
		"titulo":"Amor de bandidos",
		"imagen":null,
		"artista":"Dubosky",
		"album":"-------",
		"fecha":"2011",
		"genero":"Reggue",
		"tiempo":"03:47:00",
		"link":"mp3\/Dubosky_-_Amor_de_Bandidos.mp3"
	},{
		"id":"12",
		"titulo":"Michael Jackson - Billy Jean",
		"imagen":null,
		"artista":"80's Music",
		"album":"Smash Hits of the 80's",
		"fecha":"2000",
		"genero":"Pop",
		"tiempo":"04:54:00",
		"link":"mp3\/06  Billy Jean.mp3"
	},{
		"id":"13",
		"titulo":"Yesterday",
		"imagen":null,
		"artista":"Paul McCartney",
		"album":null,
		"fecha":null,
		"genero":"Other",
		"tiempo":"02:34:00",
		"link":"mp3\/Yesterday.mp3"
	},{
		"id":"14",
		"titulo":"Beat It",
		"imagen":null,
		"artista":"Michael Jackson Ft. Fergie",
		"album":"Thriller",
		"fecha":"2008",
		"genero":"Pop Americano",
		"tiempo":"04:10:00",
		"link":"mp3\/18 Beat It.mp3"
	},{
		"id":"15",
		"titulo":"Su Hija Me Gusta",
		"imagen":null,
		"artista":"Farruko Ft Jose Feliciano",
		"album":"El Talento Del Bloque",
		"fecha":"2010",
		"genero":"Balada",
		"tiempo":"04:56:00",
		"link":"mp3\/Farruko Ft Jose Feliciano - Su Hija Me Gusta.mp3"
	},{
		"id":"16",
		"titulo":"Thriller",
		"imagen":null,
		"artista":"Michael Jackson Ft. Fergie",
		"album":"Thriller",
		"fecha":"2008",
		"genero":"Pop",
		"tiempo":"05:58:00",
		"link":"mp3\/Thriller.mp3"
	},{
		"id":"17",
		"titulo":"Demasiado \u00d1i\u00f1a",
		"imagen":null,
		"artista":"Eddy Herrera",
		"album":"battousai",
		"fecha":"2007",
		"genero":"Merengues",
		"tiempo":"04:30:00",
		"link":"mp3\/06 Demasiado nina.mp3"
	},{
		"id":"18",
		"titulo":"Una fan enamorada ( balada)",
		"imagen":null,
		"artista":"Servando y Florentino",
		"album":"Thriller",
		"fecha":"2008",
		"genero":"Other",
		"tiempo":"00:00:00",
		"link":"mp3\/11 Una fan enamorada ( balada).mp3"
	},{
		"id":"19",
		"titulo":"Como Hago",
		"imagen":null,
		"artista":"Eddy Herrera",
		"album":null,
		"fecha":null,
		"genero":"Other",
		"tiempo":"04:04:00",
		"link":"mp3\/14 Como Hago.mp3"
	},{
		"id":"20",
		"titulo":"Me Mata",
		"imagen":null,
		"artista":"Elvis Crespo",
		"album":"title",
		"fecha":"2000",
		"genero":"Other",
		"tiempo":"03:43:00",
		"link":"mp3\/05 Me Mata.mp3"
	},{
		"id":"21",
		"titulo":"You're Beautiful",
		"imagen":null,
		"artista":"James Blunt",
		"album":"You're Beautiful",
		"fecha":"2009",
		"genero":"Acoustic",
		"tiempo":"03:18:00",
		"link":"mp3\/You_are_Beautiful.mp3"
	},{
		"id":"22",
		"titulo":"Dry Your Eyes",
		"imagen":null,
		"artista":"Sean Kingston",
		"album":"Sean Kingston",
		"fecha":"2007",
		"genero":"Other",
		"tiempo":"03:32:00",
		"link":"mp3\/06 Dry Your Eyes.mp3"
	},{
		"id":"23",
		"titulo":"300 Shots",
		"imagen":null,
		"artista":"50 Cent",
		"album":"Fidel Cashflow",
		"fecha":"2005",
		"genero":"Rap",
		"tiempo":"06:33:00",
		"link":"mp3\/09 300 Shots.mp3"
	},{
		"id":"24",
		"titulo":"Disco Inferno",
		"imagen":null,
		"artista":"50 Cent",
		"album":"The Massacre",
		"fecha":"2005",
		"genero":"Rap",
		"tiempo":"03:34:00",
		"link":"mp3\/13 Disco Inferno.mp3"
	},{
		"id":"25",
		"titulo":"Beautiful Girls (prod. by Jona",
		"imagen":null,
		"artista":"Sean Kingston",
		"album":"battousai",
		"fecha":"2007",
		"genero":"Rap",
		"tiempo":"03:44:00",
		"link":"mp3\/Beautiful Girls.mp3"
	},{
		"id":"26",
		"titulo":"Take You There",
		"imagen":null,
		"artista":"Sean Kingston",
		"album":"Sean Kingston",
		"fecha":"2007",
		"genero":"Other",
		"tiempo":"03:57:00",
		"link":"mp3\/03 Take You There.mp3"
	}]
	};