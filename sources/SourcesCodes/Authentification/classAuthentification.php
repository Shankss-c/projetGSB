<?php  
/********************************************************************************************
*********************************************************************************************
*******GESTION DE L'AUTHENTIFICATION EN  PHP *****************************
*******AUTEUR : SERGE GUERINET       *****************************
*******V.1 : Créé le 14/06/2011      ****************************
*********************************************************************************************
**  Teste l'identité d'un individu auprès d'une base de données (en clair ou en MD5) 	**
**  Teste l'identité d'une personne auprès d'un annuaire LDAP 	                        **
*********************************************************************************************
**                                                                                Version Objet							**
*********************************************************************************************
*********************************************************************************************
** REMARQUE 
 pour utiliser la bibliothèque ldap : 
   décommenter extension=php_ldap.dll dans le fichier php.ini présent dans le dossier apache
   ou activer ce module dans l'inteface du WAMP/LAMP/MAMP/XAMP
   rédemarrer les services
*/ 
/*------------------------------------------- Déclaration de la classe -------------------------------------------------------------------------------*/
class cAuthentification {
/*--------------------------------------------Propriétés de la classe  -------------------------------------------------------------------------------*/
// pour la base de données
var $connexion ; 
var $dsn ="" ;
var $mode="" ;  //mode  d'authentification : LDAP ou Base de données
var $nivoSecu ;  //niveau  de sécurité pour le mot de passe dans la base de données (text ou md5)
var $table ;  //table qui contient les données d'identification
var $champId ; //champs pour tester les valeurs fournies
var $champPasse ; 
// pour l'annuaire LDAP
var $nomServeur;
var $nomDomaine ;
// gestion des erreurs
var $erreur = "";
/*------------------------------------------- Accès aux propriétés -----------------------------------------------------------------------------------*/
function getConnexion() {return $this->connexion;}
function getErreur() { return $this->erreur;}
/* ------------------------------------------   Fonctionnement avec une base------------------------------------------------------------------------- */
	function connecte($pNomDSN, $pUtil, $pPasse) {
		//tente d'établir une connexion à une base de données 
		try {
			$this->dsn = $pNomDSN;
			$this->mode = "bdd";
			$this->connexion = odbc_connect( $pNomDSN , $pUtil, $pPasse ) ;				
			}
		catch(Exception $e) {$this->erreur="Echec Base";header("Location:testId.php");}
	}
	function definitChamps( $pTable, $pId, $pPasse, $pSecu) {
		//affecte les valeurs à la table et aux champs qui contiennent les identifiants
		$this->table = $pTable;
		$this->champId = $pId;
		$this->champPasse = $pPasse;
		$this->nivoSecu = $pSecu;
	}
/* 	------------------------------------------   Fonctionnement avec un annuaire -------------- --------------------------------------------------------- */
	function définitAnnuaire( $pServeur, $pDomaine) {
		$nomServeur = $pServeur ;
		$nomDomaine = $pDomaine ;
		$mode = "ldap" ;
	}
/* ------------------------------------------   Vérification de l'identité ------------------------------------------------------------------- */
	function verifId( $pId, $pPasse) {
		$retour = false;
		if ($this->erreur=="") { //s'il n'y a pas eu d'erreur de connexion
			if ($this->mode=="bdd" ) {
				$requete = "select count(*) as nbRep from $this->table where $this->champId='$pId' and $this->champPasse=";
				if ($this->nivoSecu=="md5") {$requete .= "md5('$pPasse')";} else {$requete .= "'$pPasse'";}
				$rs = odbc_do($this->connexion,$requete) or die($this->erreur="Echec Requete");
				$reponse = odbc_fetch_array($rs); 			
				if ($reponse["nbRep"] == 1 ) // Retourne vrai s'il y a un résultat correspondant
					{$retour = true;}
				else
					{$this->erreur = "Identifiants incorrects";}
				odbc_close($this->connexion  );
			}
			else //on est sur un annuaire
			{	//se connecte à la machine
				$serveur = ldap_connect($this->nomServeur) or die ($this->erreur = "Echec Annuaire");
				//si succès
				if ($serveur<>"") {				
					//teste la connexion avec les données de l'utilisateur 
					$utilisateur = "$pId@$this->nomDomaine";					
					$connexion = ldap_bind($serveur, $utilisateur, $pPasse) or die($this->erreur = "Identifiants incorrects" );
					$retour=true ; //si la connexion a réussi
				}
				ldap_close($serveur);
			}
		}
		return $retour;
	}
	
}