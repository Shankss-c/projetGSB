<?php  
	//démarre la gestion des sessions
	session_start();
	//récupère le nom de l'ODBC saisi dans le formulaire de choix
	if (isset($_GET["dsn"]) ) {		
		$_SESSION["dsn"] = $_GET["dsn"];
		$_SESSION["chemin"]= $_GET["chemin"];
	}
	//utilise la source ODBC récupérée ou déjà en Session
	if (isset($_SESSION["dsn"])) {
		include ("classGesTables.php");
		//connexion en ODBC à une base de données avec la Classe cGestTables
		$cnx = new cGesTables ;	
		$cnx->connecte($_SESSION["dsn"], "root","");	
		//se place dans le dossier de destination
		$cnx->dossier($_SESSION["chemin"]);
		//si la connexion a réussi
		if ($cnx->getConnexion()!=null) {			
			//affiche le formulaire des tables de la base
			$cnx->afficheTables('listetables.php');
			//agit en fonction de ce qui a été selectionné dans la liste
			if (isset($_GET['lesTables'])) {
				echo $_GET['lesTables'];   //nom de la table sélectionnée
				//génération du fichier formulaire
				$fic = fopen("form".$_GET['lesTables'].".htm","w");
				$cnx->ecritEntete($fic,'formulaire '.$_GET['lesTables']);
				$cnx->ecritFormulaire( $_GET['lesTables'],"form".$_GET['lesTables'],'recup'.$_GET['lesTables'].".php","post",$fic);
				$cnx->afficheStructure( $_GET['lesTables']);
				$cnx->ecritPied($fic);			
				fclose($fic);
				echo '<br /> Voir le formulaire : '.$_SESSION["chemin"].'\form'.$_GET['lesTables'].'.htm';
				//génération du fichier PHP de récupération des données du formulaire
				$ficPHP= fopen('recup'.$_GET['lesTables'].".php","w");
				$cnx->ecritRecupForm( $_GET['lesTables'],"POST",$ficPHP);
				fclose($ficPHP);
				echo '<br /> Voir le fichier de récupération des données : '.$_SESSION["chemin"].'\recup'.$_GET['lesTables'].'.php';
				}
		}
		else {echo "Connexion échouée";}	
	}
	else echo 'Vous devez d\'abord choisir un ODBC : cliquez <a href="choixODBC.php">ici</a>';
	   
?>