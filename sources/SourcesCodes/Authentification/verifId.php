<?php  
	//démarre la gestion des sessions
	session_start();
	//récupère le nom de l'ODBC saisi dans le formulaire de choix
	if (isset($_GET["id"]) ) {		
		$_SESSION["id"] = $_GET["id"];
		$_SESSION["passe"]= $_GET["passe"];
	}
	//Etablit une connexion avec les coordonnées passées en paramètre
	if (isset($_SESSION["id"])) {
		include ("classAuthentification.php");
		//connexion en ODBC à une base de données avec la Classe cAuthentification
		$cnx = new cAuthentification ;	
		$cnx->connecte("dsn_swiss", "root","");	
		//inidque la table et les chmaps à utiliser
		$cnx->definitChamps("Visiteur","vis_matricule","vis_nom","text");
		//si la connexion a réussi
		if ($cnx->getConnexion()!=null) {			
			//vérifie l'identité de la personne grâce aux coordonnées fournies ou retourne vers l'identification en indiquant l'erreur
			if ($cnx->verifId($_SESSION["id"],$_SESSION["passe"]))
				{unset($_SESSION["resultat"]);header("Location:Reussi.html");}
			else {$_SESSION["resultat"]=$cnx->getErreur();header("Location:testId.php");}
		}
		else {$_SESSION["resultat"]="Connexion échouée";}	
	}
	else echo 'Vous devez d\'abord choisir un ODBC : cliquez <a href="choixODBC.php">ici</a>';
	   
?>