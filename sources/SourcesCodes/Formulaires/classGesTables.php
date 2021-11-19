<?php  
/********************************************************************************************
*********************************************************************************************
*******GESTION DE FORMULAIRES HTML ET DE RECUPERATION PHP *****************************
*******AUTEUR : SERGE GUERINET       *****************************
*******V.1 : Créé le 03/05/2010      ****************************
*********************************************************************************************
**  Création de fichier pour la saisie des données d'une table dans un formulaire	**
**  Création de fichier pour la récupération des données d'un formulaire correspondant à une table	**
**  Affichage de la structure d'une table											**
*********************************************************************************************
**                                        Version Objet							**
*********************************************************************************************
*********************************************************************************************
*/ 
/*------------------------------------------- Déclaration de la classe -------------------------------------------------------------------------------*/
class cGesTables {
/*--------------------------------------------Propriétés de la classe  -------------------------------------------------------------------------------*/
var $connexion ; 
var $dsn ="" ;
/*------------------------------------------- Accès aux propriétés -----------------------------------------------------------------------------------*/
function getConnexion() {return $this->connexion;}
/* ------------------------------------------   Connexion à une base-------------- ------------------------------------------------------------------ */
	function connecte($pNomDSN, $pUtil, $pPasse) {
		//tente d'établir une connexion à une base de données 
		$this->connexion = odbc_connect( $pNomDSN , $pUtil, $pPasse );	
		$this->dsn = $pNomDSN;
	}
/*------------------------------------------- Localisation dans un dossier (créé éventuellement) -----------------------------------------------------*/
	function dossier($pDossier) {
		//crée un dossier pour contenir les pages générées s'il n'existe pas
		if (!file_exists($pDossier)) {mkdir ($pDossier,0700);}
		//se place dans le dossier pour stocker les fichiers générés
		chdir($pDossier);
	}
/* ------------------------------------------   Formulaire d'affichage des tables ------------------------------------------------------------------- */
	function afficheTables( $pFichAction) {
		//remplissage de la liste
		$tablelist = odbc_tables($this->connexion);
		echo '<form name="choixTables" method="get" action="'.$pFichAction.'">';
		echo '<select name="lesTables">';
		while (odbc_fetch_row($tablelist)) // tant qu'on n'est pas la fin de la table des objets de la base			
		{		 if (odbc_result($tablelist, 4) == "TABLE" ) // Si l'objet en cours a pour indicateur TABLE                //test à ajouter dans la condition pour ne pas afficher les  tables système en Access     && !(substr(odbc_result($tablelist,3),0,4)=="MSys")
					 echo '<option value="'.odbc_result($tablelist, 3).'">'.odbc_result($tablelist, 3).'</option>'; // Affiche nom de la TABLE					
		}
		echo '</select><input type="submit" value="Afficher"></input></form>' ;		
	}

/* ------------------------------------------   éléments de base des fichiers HTML----------------------------------------------------------------------- */	
	function ecritEntete($pFichier, $pTitre) {
		//écrit dans un fichier les entêtes d'un fichier HTML avec le titre de la page en paramètre
		fputs($pFichier, '<html><head><title>'.$pTitre.'</title><style type="text/css">');
		fputs($pFichier, '<!-- label.titre { width : 180 ;  clear:left; float:left; } .zone { width : 30car ; float : left; } ');
		fputs($pFichier, '--></style></head><body>');
	}
	function ecritPied($pFichier) {
		//écrit dans un fichier la fin d'un document html
		fputs($pFichier, '</body></html>');
	}
/* ------------------------------------------   affichage de la structure d'une table ----------------------------------------------------------------------- */	
	function afficheStructure( $pTable){	
		//affiche dans la page en cours la structure d'une table (champs/type/taille)
		  $result = odbc_do($this->connexion, "select * from ".$pTable);
		  echo "<table border=1>";
		  for ($i=1;$i<odbc_num_fields( $result )+1;$i++) //Affiche les informations
		  {
			  echo "<tr><td>".odbc_field_name($result, $i)."<td>";  // nom du champ
			  echo odbc_field_type($result, $i)."<td>";         // type du champ
			  echo odbc_field_len($result, $i)."</tr>";   // longueur
		  }
		  echo "</table>";
	}
	
/* ------------------------------------------   création du fichier "formulaire" pour une table----------------------------------------------------------------------- */
	function ecritFormulaire( $pTable, $pNomForm, $pFichAction, $pMethode, $pFichier)
	{ //écrit dans un fichier le code HTML produisant un formulaire pour les champs d'une table d'une base
		$result = odbc_do($this->connexion, "select * from ".$pTable);   //explore les champs de la table
		//écriture des propriétés du formulaire
		fputs($pFichier,  '<form name="'.$pNomForm.'" method="'.$pMethode.'" action="'.$pFichAction.'">');  //écrit dans le fichier 
		fputs($pFichier,"\n"); //retour à la ligne
		//parcours des champs de la table
		 for ($i=1;$i<odbc_num_fields( $result )+1;$i++)  {
		   $this->traiteUnChampForm(odbc_field_name($result, $i),odbc_field_type($result, $i), odbc_field_len($result, $i), $pFichier);
		 }
		//écriture du pied de formulaire avec les boutons correspondants
		fputs($pFichier, '<label class="titre"></label><div class="zone"><input type="reset" value="annuler"></input><input type="submit"></input></form>');
	}	
	
	function traiteUnChampForm($pNomChamp, $pTypeChamp, $pLongueur, $pFichier){
		//écrit dans un fichier le code correspondant à une zone de saisie pour un champ donné
		fputs($pFichier, '<label class="titre">'.$pNomChamp.' :</label>');		
		//en fonction du type, choisit le contrôle correspondant (boolean ou BIT pour Access)
		if ($pTypeChamp=="boolean" || $pTypeChamp=="BIT" ) {
		   fputs($pFichier,'<input type="checkbox"');
		   $fermeture="/>";
		}
		else {
			if ($pLongueur<=50) { //champs textuels de 50 caractères maximum
				 fputs($pFichier, '<input type="text" size="'.$pLongueur.'"');
				 $fermeture="/>";
				}
			else {//champs textuels de plus de 50 caractères
					fputs($pFichier, '<textarea rows="'.($pLongueur % 50).'" cols="50"');
					$fermeture="></textarea>";
				  }
		}
		fputs($pFichier, ' name="'.$pNomChamp.'" class="zone" '.$fermeture);
		fputs($pFichier,"\n");
	}
/* ------------------------------------------   création du fichier "récupération formulaire" pour une table ----------------------------------------------------------------------- */	
	function ecritRecupform( $pTable, $pMethode, $pFichier) {
		//écrit dans un fichier pFichier le code PHP de récupération des données d'un formulaire correspondant à une table
		$result = odbc_do($this->connexion, "select * from ".$pTable);   //explore les champs de la table
		//début du fichier
		fputs($pFichier,'<?php ');
		fputs($pFichier,"\n"); //retour à la ligne
		 for ($i=1;$i<odbc_num_fields( $result )+1;$i++) 
		  {//écrit le code de récupération du champ de formulaire 
		   $this->traiteUnChampRecup(odbc_field_name($result, $i), $pMethode, $pFichier);
		  }
		 //fin du code PHP
		fputs($pFichier, '?>');
	}
	
	function traiteUnChampRecup($pNomChamp, $pMethode, $pFichier) {
		//écrit dans un fichier le code de récupération d'un champ en GET ou POST		
			fputs($pFichier, "$".$pNomChamp."=\$_".$pMethode."['".$pNomChamp."'];");
			//affichage des données récupérées
			fputs($pFichier,"\n"); //retour à la ligne	
	}
}
?>