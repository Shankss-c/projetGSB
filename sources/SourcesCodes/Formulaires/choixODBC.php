	<html>
	<head>
		<script language="javascript">
			function controlSaisie(){
				if (f_choix.elements["dsn"].value=="" || f_choix.elements["chemin"].value=="") 
					{alert("Vous devez renseigner les deux champs");}
				else {document.location.href="listetables.php?dsn=" + f_choix.elements["dsn"].value + "&chemin=" + f_choix.elements["chemin"].value;}
			}
		</script>
		<style>
		  .zoneTitre {clear:left;float:left;width:150px;}
		  .zoneChamp {float:left;vertical-align:center;}
		</style>
	</head>
	<body>
	<form name="f_choix" action="listetables.php" method="post">
		<label class="zoneTitre">Nom de l'odbc </label><input class="zoneChamp" name="dsn" type="text" /> 
		<label class="zoneTitre">Chemin de stockage des pages créées</label>
		<input class="zoneChamp" type="text" name="chemin"/>
		<input  class="zoneChamp" type="button" onClick="controlSaisie();" value="OK"/>
	</form>
	</body>
	</html>
	
