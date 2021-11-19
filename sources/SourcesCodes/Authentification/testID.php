	<html>
	<head>
		<script language="javascript">
			function controlSaisie(){
				if (f_choix.elements["id"].value=="" || f_choix.elements["passe"].value=="") 
					{alert("Vous devez renseigner les deux champs");}
				else {document.location.href="verifId.php?id=" + f_choix.elements["id"].value + "&passe=" + f_choix.elements["passe"].value;}
			}
		</script>
		<style>
		  .zoneTitre {clear:left;float:left;width:150px;}
		  .zoneChamp {float:left;vertical-align:center;}
		</style>
	</head>
	<body>
	<?php session_start();
			if (isset($_SESSION["resultat"])) {//il y a eu une ereur de connexion
				echo '<p color="red">Erreur lors de l\'identification : '.$_SESSION["resultat"].'</p>';
			}
	?>
	<form name="f_choix" action="verifId.php" method="post">
		<label class="zoneTitre">Identifiant </label><input class="zoneChamp" name="id" type="text" /> 
		<label class="zoneTitre">Mot de passe</label>
		<input class="zoneChamp" type="password" name="passe"/>
		<input  class="zoneChamp" type="button" onClick="controlSaisie();" value="OK"/>
	</form>
	</body>
	</html>
	
