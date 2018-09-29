<?php
class Telechargement
{
// DEBUT DE CLASSE UPLOAD abciweb.net version 3.0 compatible php >= 5.2

private $index_ses = 'Up5+TzUdDfgt';
private $qualite = 88; // 0 interdit

private $tab_mes = array(
0 => 'Le poids total maximum du formulaire autorisé par le serveur est dépassé ', // complété 
1 => ' téléchargé dans le dossier ',// précédé/complété
2 => ' renommé ',// précédé/complété
3 => ' problème lors du transfert du fichier',// précédé
4 => ' extension non autorisée. Extensions autorisées : ',// précédé/complété
5 => ' est un nom invalide. Veuillez renommer votre fichier avant le téléchargement',// précédé
6 => ' ce fichier existe déjà',// précédé
7 => ' n\'est pas une image valide. Types de fichiers images autorisés : gif, jpg, jpeg, png',// précédé
8 => ' excède le poids maximum de fichier autorisé par le serveur ',// précédé/complété
9 => ' excède le poids maximum de fichier autorisé pour le formulaire ',// précédé/complété
10 => ' non téléchargé. Problème lors du téléchargement vers le serveur',// précédé
11 => ' a une résolution trop importante pour être traité. Envoyez un fichier avec une résolution inférieure',// précédé
12 => ' problème lors de la création de l\'image intermédiaire. Fichier non traité',// précédé
13 => ' problème lors du redimensionnement',// précédé
14 => ' problème lors du transfert du fichier redimensionné',// précédé
15 => ' redimensionné en ',// précédé/complété
16 => ' optimisé en ',// // précédé/complété par les dimensions
17 => ' nom de destination du fichier invalide',// précédé
18 => 'téléchargement OK', // précédé du nom du fichier
19 => 'Le nombre maximum de fichiers est dépassé ',// complété, utilisé par Set_Max_nombreFichers() et Verif_max_nb_fichiers_serveur()
20 => 'Seuls les fichiers suivants ont été traités',// utilisé par Set_Max_nombreFichers() et Verif_max_nb_fichiers_serveur()
21 => 'x', //Caractère séparant les dimensions des images dans le tableau des messages et le tableau des résultats
22 => ' Suggestion possible : ',// complété par un nom de fichier valide
23 => 'Champ de téléchargement vide. Aucun fichier n\'a été téléchargé',// utilisé par Set_Message_champVide()
24 => 'Aucun fichier n\'a été téléchargé'// utilisé par Set_Max_nombreFichers()
);

private $extensions_autorisees = null;
private $ses_mes;
private $ses_etat;
private $index_mes;
private $index_etat;
private $repertoire;
private $renomme = false;
private $renommage_case_sensitive = false;
private $mode_renommage_incr = false;
private $controle_fichier = false;
private $controle_img = false;
private $verif_ext;
private $file_form;
private $verif_post;
private $verif_get;
private $dim_image_source = null;
private $nouveau_nom = null;
private $nouveau_nom_ext = false;
private $admin_max_poids_fichier = null;
private $admin_max_poids_fichier_ini = null;
private $message_champ_vide = false;
private $tab_mes_champ_vide = array();
private $nb_fichiers_utilisateur = 0;
private $post_max_size_serveur = null;
private $upload_max_filesize_serveur = null;
private $upload_max_filesize_serveur_oct = null;
private $memory_limit_serveur = null;
private $max_file_uploads_serveur = null;
private $max_nb_fichiers_admin = null;
private $max_nb_fichier_stop = false;
private $depassement_nb_fichiers_serveur = false;
private $nb_localfile_incr = 0;
private $nb_setredim_incr = 0;
private $redimension = array();
private $is_array_rep_redim = false;
private $copie_original_suffixe = false;





public function __construct ($repertoire = null, $verif_post = null, $file_form = null, $verif_get = null, $tab_message = null)
{      
	if (!session_id()) session_start();
		 
	$this->repertoire = trim($repertoire);
	$this->verif_post = trim($verif_post);
	$this->file_form = trim($file_form);
	$this->verif_get = trim($verif_get);
	
	$this->Verif_param();
	$this->Verif_repertoire($this->repertoire);
	
	$this->index_mes = $this->verif_post.' '.$this->file_form.'mes';                      
	$this->index_etat = $this->verif_post.' '.$this->file_form.'res';

	$this->ses_mes[$this->index_mes] =& $_SESSION[$this->index_ses][$this->index_mes]; 
	$this->ses_etat[$this->index_etat] =& $_SESSION[$this->index_ses][$this->index_etat];
	
	if (isset($tab_message)) $this->Set_Tab_messages($tab_message);
	
	$this->Config_serveur();
	
	$this->Verif_max_post(); /* Si on mettait cette détection d'erreur dans la fonction "Upload()" ou "Upload_liste()" on ne pourrait pas conditionner ces fonctions à l'existence d'une variable $_POST (puisque $_POST est vide si la configuration post_max_size du serveur est dépassée) ce qui néanmoins est utile si l'on veut faire : if ($_POST['champ_important'] == 'renseigné') {upload}*/
}





// FONCTIONS DE CONTROLE DES DONNEES WEBMESTRE
private function Verif_param ()
{
	if($this->repertoire === '' || $this->verif_post === '' || $this->file_form === '')
										 
	exit('Les trois premiers paramètres de la classe de téléchargement de fichiers, correspondant :<br /><br />- 1/ Au nom du répertoire de destination  <br />- 2/ Au nom du champ $_POST de contrôle d\'envoi du formulaire  <br />- 3/ Au nom du champ $_FILES du formulaire<br /><br /> DOIVENT ETRE RENSEIGNES<br />');
}




private function Verif_repertoire ($rep)
{                              
	if (!is_dir($this->Adresse_repertoire($rep)))
		{
			exit('- Chemin du dossier de destination "'.$this->Adresse_repertoire($rep).'" NON VALIDE');
		}
								 
	if (!is_writable($this->Adresse_repertoire($rep)))                  
		{
			exit('- Chemin du dossier de destination "'.$this->Adresse_repertoire($rep).'" NON ACCESSIBLE EN ECRITURE');
		}																		 
}              




private function Verif_tab_extension ()
{            
	if(is_array($this->extensions_autorisees))                                                    
	{
		if(count($this->extensions_autorisees) > 0)  
																																	
			$this->verif_ext = true;
			else
			$this->verif_ext = false;
	}                  
	else if ($this->controle_img === false && count($this->redimension) == 0)
	{
		exit("Par sécurité, vous devez employer la fonction \"Set_Extensions_accepte\" pour envoyer un tableau d'extensions autorisées après l'initialisation de la classe et avant l'utilisation de la fonction d'upload, excepté si vous employez la fonction \"Set_Controle_dimImg\" qui contrôle des images de type gif, jpg, jpeg ou png, ou si vous employez la fonction \"Set_Redim\" de redimensionnement des images qui effectue ces mêmes contrôles.<br /><br />Alternativement, si vous ne souhaitez pas vérifier l'extension des fichiers, envoyez un tableau vide.");
	}
}




private function Verif_tab_messages ($tab)
{                              
	try
		{                                                              
			if (!is_array($tab))
				{
					throw new Exception('- Le tableau des messages envoyé en paramètre n\'est pas un tableau valide. Il doit correspondre au tableau suivant : <br >');
				}
									 
			if (count($tab) !== count($this->tab_mes))                  
				{
					throw new Exception('- Le tableau des messages envoyé en paramètre n\'a pas le nombre d\'éléments nécessaire (= '.count($this->tab_mes).') correspondant aux libellés suivants : <br >');
				}																		 
		}									 
	catch(Exception $e)	 
		{
			echo $e->getMessage();
			echo '<pre>';
			print_r($this->tab_mes);
			echo '</pre>';
			exit;
		}
}




private function Verif_nouveau_nom ($nom, $param = false)
{
	try
		{                                                              
			$nom_fichier = $this->Nettoie_Nom_fichier($nom);
			 
			if($nom_fichier === false)
				{
					throw new Exception('- Le nom de fichier "'.$nom.'" n\'est pas un nom de fichier valide.');
				}      
					 
			if($nom_fichier !== $nom)
				{
					throw new Exception('- Le nom de fichier "'.$nom.'" n\'est pas un nom de fichier valide. Suggestion possible : "'.$nom_fichier.'"');
				}                                                                                          
		}
	catch(Exception $e)	 
		{
			if ($param === false)
				{
					echo $e->getMessage();
					exit;
				}
				else
				{
					$suggestion = $nom_fichier !== false && $nom_fichier !== $nom && !empty($this->tab_mes[22]) ? $this->tab_mes[22].$nom_fichier : null;
					$this->Set_message('', '', '"'.$nom.$this->tab_mes[17].$suggestion);
					$this->nouveau_nom = false;
				}
		}
}              




private function Verif_caracteres_fichier ($add,$nom)
{
	try
		{                                                              
			$add_test = $this->Nettoie_Nom_fichier($add);
			 
			if($add_test === false)
				{
					throw new Exception('- Le '.$nom.' "'.$add.'" n\'est pas valide.');
				}      
					 
			if($add_test !== $add)
				{
					throw new Exception('- Le '.$nom.' "'.$add.'" n\'est pas valide. Suggestion possible : "'.$add_test.'"');
				}                                                                                          
		}
	catch(Exception $e)	 
		{
			echo $e->getMessage();
			exit;
		}
		
		return true;
}              




// FONCTIONS PUBLIQUES 

// Set_public DE PARAMETRAGE
public function Set_Extensions_accepte ($extensions_autorisees = null)
{
	$this->extensions_autorisees = is_array($extensions_autorisees) ? array_map('strtolower',$extensions_autorisees) : null;
}




public function Set_Renomme_fichier ($incr = false, $unix = false)
{
	if (trim($incr) != false) $this->mode_renommage_incr = true;
	
	if (trim($unix) != false) $this->renommage_case_sensitive = true;
										 
	$this->renomme = true;
}




public function Set_Nomme_fichier ($nom = null, $extension = false, $param = false)
{
	$nom = trim($nom);
	$param = trim($param) != false ? true : false;
	
	/* si $nom incorrect et $param != false, Verif_nouveau_nom() défini $this->nouveau_nom = false et enregistre un message d'erreur; si $nom incorrect et $param = false on sort du script avec un message d'erreur.
	*/
	$this->Verif_nouveau_nom($nom,$param);
	// A ce niveau si $this->nouveau_nom existe il est égal à false
	$this->nouveau_nom = isset($this->nouveau_nom)? $this->nouveau_nom : $nom;
	 
	if(trim($extension) != false) $this->nouveau_nom_ext = true;
}




public function Set_Controle_fichier ()
{
	$this->controle_fichier = true;
}




public function Set_Controle_dimImg ()
{
	$this->controle_img = true;
}




public function Set_Message_court ($message = null)
{
	$this->tab_mes[1] = null;
	$this->tab_mes[18] = $message;
}




public function Set_Message_champVide ($message = null)
{
	$this->message_champ_vide = true;
	
	if(is_array($message))
	{
		$this->tab_mes_champ_vide = $message;
	}
	else if (trim($message) != '')
	$this->tab_mes[23] = $message;
}




public function Set_Max_poidsFicher ($poids = null)
{
	$this->admin_max_poids_fichier_ini = $poids;
	$poids = $this->Return_Octets($poids);
	
	$this->upload_max_filesize_serveur_oct = isset($this->upload_max_filesize_serveur) ? $this->Return_Octets($this->upload_max_filesize_serveur) : null; 
	
	if(is_numeric($poids) && $poids > 0)
	{
		$this->admin_max_poids_fichier = $poids;
		if (isset($this->upload_max_filesize_serveur_oct) && $poids > $this->upload_max_filesize_serveur_oct)
		{
			exit('Le paramètre indiqué dans la fonction "Set_Max_poidsFicher" ('.$poids.') est supérieur au maximum autorisé par la configuration "upload_max_filesize" du serveur ('.$this->upload_max_filesize_serveur.')');			
		}
	}
	else
	{
		exit('Le paramètre indiqué dans la fonction "Set_Max_poidsFicher" doit être une valeur numérique non nulle représentant des octets ou une valeur numérique suivie d\'une des unités suivante "K", "M", "G", "T" ou "Ko", "Mo", "Go", "To" (variantes majuscules/minuscules acceptées)');
	}
}




public function Set_Max_nombreFichers ($max = null, $stop = false)
{	
	if(is_numeric($max))
	{
		$this->max_nb_fichiers_admin = intval($max);
		
		if(isset($this->max_file_uploads_serveur) && $this->max_nb_fichiers_admin > $this->max_file_uploads_serveur)
		{
			exit('Le premier paramètre indiqué dans la fonction "Set_Max_nombreFichers" ('.$this->max_nb_fichiers_admin.') est supérieur au nombre maximum autorisé par la configuration "max_file_uploads" du serveur ('.$this->max_file_uploads_serveur.')');
		}
	}
	else
	{
		exit('Le premier paramètre indiqué dans la fonction "Set_Max_nombreFichers" demande une valeur numérique');
	}
	
	if(trim($stop) != false) $this->max_nb_fichier_stop = true;
}




public function Set_Separateur_dimImg ($separateur = null)
{
	if(isset($separateur) && $separateur !== '') $this->tab_mes[21] = $separateur;
}




public function Set_Redim ($largeur_max = null, $hauteur_max = null, $rep = null, $qualite = null, $agrandissement = false)
{
	$i = $this->nb_setredim_incr++; 	
	$is_array_rep = is_array($rep) ? true : false;
	
	if($is_array_rep) 
	{
		$this->is_array_rep_redim = true;
		if(isset($rep[1]) && trim($rep[1]) == $this->repertoire) $this->copie_original_suffixe = true;
	}
	
	$rep_redim = $is_array_rep && isset($rep[1]) ? $rep[1] : $rep;  
	$rep_redim = !is_array($rep_redim) && trim($rep_redim) != '' ? trim($rep_redim) : $this->repertoire;

	if($rep_redim != $this->repertoire) $this->Verif_repertoire($rep_redim);
	
	$suffixe = $is_array_rep && isset($rep[0]) && $this->Verif_caracteres_fichier (trim($rep[0]),'suffixe') ? trim($rep[0]) : null;
	$this->redimension[$rep_redim][$i]['suffixe'] = $suffixe;

	$prefixe = $is_array_rep && isset($rep[2]) && $this->Verif_caracteres_fichier (trim($rep[2]),'préfixe') ? trim($rep[2]) : null;
	$this->redimension[$rep_redim][$i]['prefixe'] = $prefixe;
				 
	$largeur_max = trim($largeur_max) != false ? trim($largeur_max) : null;
	$this->redimension[$rep_redim][$i]['L_max'] = is_numeric($largeur_max) ? abs(round($largeur_max)) : null;
				 
	$hauteur_max = trim($hauteur_max) != false ? trim($hauteur_max) : null;
	$this->redimension[$rep_redim][$i]['H_max'] = is_numeric($hauteur_max) ? abs(round($hauteur_max)) : null;
				 
	$qualite = trim($qualite) != false ? trim($qualite) : null;
	$this->redimension[$rep_redim][$i]['Qualite'] = is_numeric($qualite) && $qualite > 0 && $qualite < 101 ? abs(round($qualite)) : $this->qualite;
				 
	$this->redimension[$rep_redim][$i]['agrandissement'] = $agrandissement;
}




public function Set_Tab_messages ($tab = null)
{
	$this->Verif_tab_messages($tab); 

	$this->tab_mes = $tab;
}




// Get_public 
public function Get_Tab_message ()
{
	if (isset($this->ses_mes[$this->index_mes]))
	{
		$tab_result = array();
	
		foreach($this->ses_mes[$this->index_mes] as $num => $tab_rep) 
		{		
			foreach ($tab_rep as $value)
			{
				if(is_array($value))
				$tab_result[] = $value;
				else
				$tab_result[] = array($value);
			}
		}
											 
		$this->ses_mes[$this->index_mes] = null;
											 
		return $tab_result;
	}											 
	else return array();                          
}




// pour compatibilité avec anciennes versions de cette classe < 3.0
public function Get_Tab_upload ()
{ 
	if($this->is_array_rep_redim) exit('La fonction Get_Tab_upload() n\'est pas compatible pour récupérer tous les résultats du téléchargement'."\n".'quand vous configurez la fonction Set_Redim() avec un tableau pour renseigner le troisième paramètre.'."\n".'Utilisez la fonction Get_Tab_result() pour récupérer le tableau des résultats dans ce cas'."\n".'(ou dans tous les cas pour un code plus générique).');
	
	if (isset($this->ses_etat[$this->index_etat]))										 
	{
		$tab_result = array();
			 
		$tab_result['identifiant'] = $this->verif_post;
											 
		$tab_result['champ'] = $this->file_form;
											 
		$tab_result['resultat'] = array();
		
		foreach($this->ses_etat[$this->index_etat] as $num => $repertoires)
		{
			$repertoires = array_filter($repertoires);	
			
			foreach ($repertoires as $rep => $noms)      
			{				
				$noms = array_filter($noms);

				foreach ($noms as $nom => $value)
				{
					if(is_array($value))
					{
						$value = array_map("trim",$value);
						$value = array_filter($value);
						
						$tab_result['resultat'][$num][$rep]['nom_ini'] = isset($value[0]) ? $value[0] : null;					
						$tab_result['resultat'][$num][$rep]['nom'] = isset($value[1]) ? $value[1] : null;																			
						$tab_result['resultat'][$num][$rep]['dim'] = isset($value[2]) ? $value[2] : null;
					}
				}
			}
		}

		$this->ses_etat[$this->index_etat] = null;
											 
		return $tab_result;
	}											 
	else return array();
}      




public function Get_Tab_result ()
{  
	if (isset($this->ses_etat[$this->index_etat]))										 
	{
		$tab_result = array();
			 
		$tab_result['identifiant'] = $this->verif_post;
											 
		$tab_result['champ'] = $this->file_form;
											 
		$tab_result['resultat'] = array();
		
		foreach($this->ses_etat[$this->index_etat] as $num => $repertoires)
		{
			$repertoires = array_filter($repertoires);		
			foreach ($repertoires as $rep => $noms)      
			{				
				$noms = array_filter($noms);

				foreach ($noms as $nom => $value)
				{
					if(is_array($value))
					{
						$value = array_map("trim",$value);
						$value = array_filter($value);
						
						$tab_result['resultat'][$num][$rep][$nom]['nom_ini'] = isset($value[0]) ? $value[0] : null;					
						$tab_result['resultat'][$num][$rep][$nom]['nom'] = isset($value[1]) ? $value[1] : null;																			
						$tab_result['resultat'][$num][$rep][$nom]['dim'] = isset($value[2]) ? $value[2] : null;
					}
				}
			}
		}

		$this->ses_etat[$this->index_etat] = null;
											 
		return $tab_result;
	}											 
	else return array();
}      




public function Return_Config_serveur ($tab=null)
{
	$tab = trim($tab) != false ? true : false;
	if ($tab) return array('post_max_size'=>$this->post_max_size_serveur,'upload_max_filesize'=>$this->upload_max_filesize_serveur,'max_file_uploads'=>$this->max_file_uploads_serveur,'memory_limit'=>$this->memory_limit_serveur);
	else
	return "post_max_size $this->post_max_size_serveur, upload_max_filesize $this->upload_max_filesize_serveur, max_file_uploads $this->max_file_uploads_serveur, memory_limit $this->memory_limit_serveur"; 
}




public function Get_Reload_page ()
{
	header("Location:".$_SERVER['PHP_SELF']);
	exit;  
}




public function Return_Octets($val)
{
	$val = trim($val);
	$val = str_replace(array(',',' '),array('.',''),$val);
	$val = rtrim($val, "oO");

	$last = strtolower(substr($val,-1));

	switch($last)
	{
		case 't':  $val *= 1024;
		case 'g':  $val *= 1024;
		case 'm': $val *= 1024;
		case 'k':  $val *= 1024;
	}

	return $val;
}




// UPLOAD MOTEUR
public function Upload ($reload = false)
{
	if (trim($reload) != false) $reload = true;
										 
	$this->Upload_Liste($reload);
}




// FONCTION UPLOAD : Liste (dans le cas d'un tableau) le champ spécifié de type $_FILES et envoie le résultat à la fonction Upload_fichier puis effectue ou non un reload
private function Upload_Liste ($reload = false)
{
	$this->Verif_tab_extension();
																																																	
	if (isset($_POST[$this->verif_post],$_FILES[$this->file_form]))
	{
		$this->Verif_max_nb_fichiers_serveur();
		
		$localfile = $_FILES[$this->file_form]['name'];
		
		$this->Nb_fichiers_utilisateur($localfile);
			
		if(isset($this->max_nb_fichiers_admin))
		{
			$this->Verif_max_nb_fichiers_admin();
		}
		
		if (is_array($localfile))
			foreach ($localfile as $index_champ => $nom_fichier)
			{
				$nom_local = $_FILES[$this->file_form]['name'][$index_champ];
				$nom_temp = $_FILES[$this->file_form]['tmp_name'][$index_champ];
				$poids = $_FILES[$this->file_form]['size'][$index_champ];
				$erreur = $_FILES[$this->file_form]['error'][$index_champ];	
				
				$this->Upload_fichier ($index_champ,$nom_local,$nom_temp,$erreur,$poids);
			}           
		 	else                                      
		 	{
				$nom_local = $_FILES[$this->file_form]['name'];
				$nom_temp = $_FILES[$this->file_form]['tmp_name'];
				$poids = $_FILES[$this->file_form]['size'];
				$erreur = $_FILES[$this->file_form]['error'];
																														
				$this->Upload_fichier (0,$nom_local,$nom_temp,$erreur,$poids);
			}
																										 
		if ($reload) $this->Get_Reload_page();                       
	}            
}




// FONCTION UPLOAD FICHIER          
private function Upload_fichier ($index_champ, $nom_local, $nom_temp, $erreur, $poids)
{	
	$nomme_fichier = null;
	
	$extension = pathinfo($nom_local, PATHINFO_EXTENSION);
	$ext_point = $extension != '' ? '.'.$extension : null;

	// Si on a employé la fonction Set_nomme_fichier()
	if (isset($this->nouveau_nom) && $this->nouveau_nom !== false)
	{
		// Si nouveau_nom_ext = true -> le fichier prend l'extension du fichier téléchargé
		$nomme_fichier = $this->nouveau_nom_ext == true ? $this->nouveau_nom.$ext_point : $this->nouveau_nom;
		$ext_nomme_fichier = pathinfo($nomme_fichier, PATHINFO_EXTENSION);
		$ext_point = $ext_nomme_fichier != '' ? '.'.$ext_nomme_fichier : null;
	}  

	// Vérifications contrôles avec Verif_upload_fichier()
	$nom_fichier = $this->Verif_upload_fichier($index_champ, $nom_local, $nom_temp, $erreur, $poids, $nomme_fichier);
						 
	if ($nom_fichier === false) return false;


	$adresse_fichier = $this->Adresse_repertoire($this->repertoire).$nom_fichier;

	// Si on utilisé Set_Redim et que l'image originale (sans traitement) ne doit pas être sauvegardée...
	if (array_key_exists($this->repertoire,$this->redimension) && !$this->copie_original_suffixe)	
	{
		// On fera les tests avec "Set_Controle_fichier" et "Set_Renomme_fichier" sur le nom composé par la première utilisation de "Set_Redim" qui indique explicitement ou implicitement le même réperoire de destination que celui indiqué dans l'initialisation de la classe.
		// Le nom peut être modifié avec un suffixe ou un préfixe.
		$tab_redim_rep_ini = $this->redimension[$this->repertoire];
		$test_nom_fichier = array_shift($tab_redim_rep_ini);
		$test_nom_fichier = $test_nom_fichier['prefixe'].pathinfo($nom_fichier, PATHINFO_FILENAME).$test_nom_fichier['suffixe'].$ext_point;
		
		$adresse_suffixe_admin = $this->Adresse_repertoire($this->repertoire).$test_nom_fichier;
		$no_original = true;
	}
	else
	{
		// Si l'on a pas employé Set_Redim ou si l'image originale doit être sauvegardée on garde l'adresse définie plus haut 
		$no_original = false;
	}


	$adresse_test = $no_original ? $adresse_suffixe_admin : $adresse_fichier;
	
	// Si l'on a employé la fonction "Set_Controle_fichier"                                
	if ($this->controle_fichier == true && is_file($adresse_test))
	{		 
		$this->Set_message ($this->repertoire, $index_champ, '"'.basename($adresse_test).'"'.$this->tab_mes[6]);
											 
		$this->Set_result ($this->repertoire, $index_champ, false);
		
		return false;
	}


	$adresse_fichier_ini = $adresse_fichier;
	// Si on a employé la fonction Set_Renomme_fichier(), renommage en cas de doublon sur le serveur avec Rename_fich() 
	if ($this->renomme === true)
	{
		$adresse_fichier = $this->Rename_fich($adresse_test,$this->mode_renommage_incr,$this->renommage_case_sensitive);
	}
	

	// Si on a employé la fonction Set_Redim(), redimensions avec Redim_liste()
	if (count($this->redimension) > 0)
	{
		// On cherche l'éventuel suffixe donné par la fonction "Rename_fich" 
		$strlen_ext = strlen($ext_point);		
		$start_substr = strlen($adresse_test)-$strlen_ext;
		
		$add_suffixe_auto = substr($adresse_fichier,$start_substr);
		$add_suffixe_auto = substr($add_suffixe_auto,0,strlen($add_suffixe_auto)-$strlen_ext);

		$suffixe = array();
		foreach ($this->redimension as $rep => $redim) 
		{
			foreach ($redim as $value) $suffixe[$rep][] = $value['suffixe'].$add_suffixe_auto;
		}
		
		$redim = $this->Redim_liste ($index_champ, $nom_local, $nom_temp, $adresse_fichier_ini, $suffixe);
									 
		if($redim === false) return false;

		if ($no_original) return false;
	}


	$nom_fichier = basename($adresse_fichier);
	
	if (@move_uploaded_file($nom_temp, $adresse_fichier))
	{
															 
		$resultat1 = !empty($this->tab_mes[1]) ? '"'.$nom_local.'"'.$this->tab_mes[1].$this->repertoire : null;
		$resultat2 = !empty($this->tab_mes[1]) ? '"'.$nom_local.'"'.$this->tab_mes[2].'"'.$nom_fichier.'"'.$this->tab_mes[1].$this->repertoire : null;

		$resultat = $nom_local === $nom_fichier ?  $resultat1 : $resultat2;
		
		if (isset($resultat))
		{
			$this->Set_message ($this->repertoire, $index_champ, $resultat);
		}
		else if(isset($this->tab_mes[18]))
		{
			$this->Set_message ($this->repertoire, $index_champ, '"'.$nom_local.'" '.$this->tab_mes[18]);
		}

		if (($this->controle_img === true || count($this->redimension) > 0) && isset($this->dim_image_source))
		{
			$dim = implode($this->tab_mes[21],$this->dim_image_source);
																		 
			$this->Set_result ($this->repertoire, $index_champ, array($nom_local,$nom_fichier,$dim));
		}                                                                      
		else 
		{
			$this->Set_result ($this->repertoire, $index_champ, array($nom_local,$nom_fichier));
		}
	}                          
	else      
	{
		$this->Set_message ($this->repertoire, $index_champ, '"'.$nom_local.'"'.$this->tab_mes[3]);
		$this->Set_result ($this->repertoire, $index_champ, false);
															 
		@unlink($nom_temp);
	}
}




// FONCTION VERIF UPLOAD FICHIER
private function Verif_upload_fichier ($index_champ, $nom_local, $nom_temp, $erreur, $poids, $nomme_fichier = null)
{ 

	// Si l'on a employé la fonction "Set_Max_nombreFichers". On exclu les champs vides (erreur != 4)
	if (isset($this->max_nb_fichiers_admin) && $erreur != 4)
	{
		$this->nb_localfile_incr++;
		
		if(($this->max_nb_fichier_stop && $this->nb_fichiers_utilisateur > $this->max_nb_fichiers_admin) || ($this->nb_localfile_incr > $this->max_nb_fichiers_admin)) return false;
		// Le message d'information est enregistré par la fonction "Verif_max_nb_fichiers_admin" appelée dans "Upload_Liste"
	}
	
	
	// Si utilisation de Set_Max_poidsFicher. $erreur = 1 (erreur serveur upload_max_filesize) reportée en config admin pour l'affichage des messages si les valeurs "config admin" et "config serveur" sont égales 
	if(isset($this->admin_max_poids_fichier) && ($poids > $this->admin_max_poids_fichier || ($erreur == 1 && isset($this->upload_max_filesize_serveur_oct) && $this->upload_max_filesize_serveur_oct == $this->admin_max_poids_fichier))) 
	{
		$erreur = 2; // Message d'information reporté dans la condition if ($erreur !== 0) suivante
		$add = '('.$this->admin_max_poids_fichier_ini.')';
	}
	

	// Si $erreur != 0 problème lors de l'upload ou champ vide (=4)      
	if ($erreur !== 0)                    
	{
		$add = isset($add)? $add : null;
		
		$message = null;
		switch ($erreur)
		{// 1 et 2 inversé pour indiquer un dépassement admin avant un dépassement upload_max_filesize du serveur si les deux valeurs de config sont égales
			case "2" : $message = '"'.$nom_local.'"'.$this->tab_mes[9].$add; break;
			case "1" : $add = isset($this->upload_max_filesize_serveur) ? '('.$this->upload_max_filesize_serveur.')': null;
					   $message = '"'.$nom_local.'"'.$this->tab_mes[8].$add; break;
			case "4" : if ($this->message_champ_vide == true)// Si l'on a employé la fonction "Set_Message_champVide"
						{
							if(empty($this->tab_mes_champ_vide))
							{
								$message = $this->tab_mes[23];
							}
							else
							{
								if (isset($this->tab_mes_champ_vide[$index_champ]) && trim($this->tab_mes_champ_vide[$index_champ]) != '') $message = $this->tab_mes_champ_vide[$index_champ];
							}
						}
						break;
						
			default : $message = '"'.$nom_local.'"'.$this->tab_mes[10]; 
		}
		
		if (isset($message)) $this->Set_message ($this->repertoire, $index_champ, $message);
											 
		$this->Set_result ($this->repertoire, $index_champ, false);
		
		return false;
	}      
		

	//$this->nouveau_nom = false défini par la fonction "Verif_nouveau_nom" qui enregistre également le message d'information correspondant	
	if ($this->nouveau_nom === false) return false;


	// Nettoyage du nom de fichier pour avoir un nom de fichier valide sur le serveur
	$nom_fichier = isset($nomme_fichier) ? $nomme_fichier : $this->Nettoie_Nom_fichier($nom_local);

	// Si $nom_fichier égal à false
	if ($nom_fichier === false)
	{
		$this->Set_message ($this->repertoire, $index_champ, '"'.$nom_local.'"'.$this->tab_mes[5]);											
		$this->Set_result ($this->repertoire, $index_champ, false);

		return false;
	}


	// Si le témoin de vérification "$this->verif_ext" sur les extensions retourne true
	if ($this->verif_ext == true && $this->Verif_extension($nom_local) === false)	
	{                                                  
		$liste_extensions = implode(', ',$this->extensions_autorisees);
											 
		$this->Set_message ($this->repertoire, $index_champ, '"'.$nom_local.'"'.$this->tab_mes[4].$liste_extensions);
		
		$this->Set_result ($this->repertoire, $index_champ, false);
		
		return false;                                      
	}


	// Si l'on a employé la fonction "Set_Controle_dimImg"
	if ($this->controle_img === true && count($this->redimension) == 0)
	{
		$this->dim_image_source = null;
											 
		$infos_images = $this->Infos_image($nom_temp);
											 
		if ($infos_images === false)
		{                                                      
			$this->Set_message ($this->repertoire, $index_champ, '"'.$nom_local.'"'.$this->tab_mes[7]);
			$this->Set_result ($this->repertoire, $index_champ, false);
																																																														
			return false;
		}
		else
		{
			$this->dim_image_source = array_slice($infos_images, 0, 2);
		}
	}																		 
	
	
	// Si on arrive ici c'est que tout c'est bien passé et l'on retourne le nom du fichier
	//return $this->Adresse_repertoire($this->repertoire).$nom_fichier;	
	return $nom_fichier;
}




private function Set_message ($repertoire = null, $index = null, $message)
{
	if (isset($index) && is_numeric($index)) 
	
	$this->ses_mes[$this->index_mes][$index][$repertoire][] = $message;
										 
	else
										 
	$this->ses_mes[$this->index_mes]['mes'][] = $message;
}




private function Set_result ($repertoire = null, $index, $array)
{   
	$this->ses_etat[$this->index_etat][$index][$repertoire][] = $array;	
}




private function Verif_extension ($fichier)
{
	$extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
						 
	if (in_array($extension,$this->extensions_autorisees))
						 
	return true;
	else
	return false;                  
}




private function Config_serveur()
{ 
	$post_max_size = ini_get('post_max_size');
	$this->post_max_size_serveur = $post_max_size != false ? $post_max_size : null;
	
	$upload_max_filesize = ini_get('upload_max_filesize');
	$this->upload_max_filesize_serveur = $upload_max_filesize != false ? $upload_max_filesize : null;
	
	$nbmax = ini_get('max_file_uploads');
	$this->max_file_uploads_serveur = $nbmax != false && is_numeric($nbmax) ? $nbmax : null;
	
	$memory = ini_get('memory_limit');
	$this->memory_limit_serveur = $memory != false ? $memory : null;
}




private function Verif_max_post()
{
	if (isset($_GET[$this->verif_get]) && !isset($_POST[$this->verif_post]))
	{
		$add = isset($this->post_max_size_serveur) ? '('.$this->post_max_size_serveur.')' : null;
		$this->Set_message('', '', $this->tab_mes[0].$add);
		
		$this->Get_Reload_page();                      
	}
	
	if (function_exists('error_get_last'))
	{
		$derniere_erreur = error_get_last();
		if(isset($derniere_erreur) && $derniere_erreur['type'] == 2 && strpos($derniere_erreur['message'],'POST Content-Length') !== false)
		{
			$this->Set_message('', '', $this->tab_mes[0].'('.$this->post_max_size_serveur.')');
			
			$this->Get_Reload_page();                      
		}
	}
}




private function Verif_max_nb_fichiers_serveur()
{
	if (function_exists('error_get_last'))
	{
		$derniere_erreur = error_get_last();
		
		if(isset($derniere_erreur) && $derniere_erreur['type'] == 2 && strpos($derniere_erreur['message'],'Maximum number of allowable file uploads has been exceeded') !== false)
		{
			$this->depassement_nb_fichiers_serveur = true;

			if ($this->max_nb_fichiers_admin == null)
			{				
				$this->Set_message ('', '', $this->tab_mes[19].'('.$this->max_file_uploads_serveur.')');
				$this->Set_message ('', '', $this->tab_mes[20]);
			}
		}
	}
}




private function Verif_max_nb_fichiers_admin()
{

	if($this->nb_fichiers_utilisateur > $this->max_nb_fichiers_admin || $this->depassement_nb_fichiers_serveur)
	{		
		$this->Set_message ('', '', $this->tab_mes[19].'('.$this->max_nb_fichiers_admin.')');
		
		if ($this->max_nb_fichier_stop)
			$this->Set_message ('', '', $this->tab_mes[24]);
			else
			$this->Set_message ('', '', $this->tab_mes[20]);
	}
}




private function Nb_fichiers_utilisateur($localfile)
{
	$nb_localfile = 0;
	
	if(is_array($localfile)) $nb_localfile = count(array_filter($localfile));
	else if ($localfile != '') $nb_localfile = 1;
	
	$this->nb_fichiers_utilisateur = $nb_localfile;
}              




private function Adresse_racine()
{
	$adresse_racine = (substr($_SERVER['DOCUMENT_ROOT'],-1) == '/')? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['DOCUMENT_ROOT'].'/' ;
										 
	return $adresse_racine;
}              



											 
private function Adresse_repertoire($rep)
{
	return $this->Adresse_racine().$rep.'/';
}




public function Nettoie_Nom_fichier($nom_fichier)
{
	$cible = array(
	'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ă', 'Ą',
	'Ç', 'Ć', 'Č', 'Œ',
	'Ď', 'Đ',
	'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ă', 'ą',
	'ç', 'ć', 'č', 'œ',
	'ď', 'đ',
	'È', 'É', 'Ê', 'Ë', 'Ę', 'Ě',
	'Ğ',
	'Ì', 'Í', 'Î', 'Ï', 'İ',
	'Ĺ', 'Ľ', 'Ł',
	'è', 'é', 'ê', 'ë', 'ę', 'ě',
	'ğ',
	'ì', 'í', 'î', 'ï', 'ı',
	'ĺ', 'ľ', 'ł',
	'Ñ', 'Ń', 'Ň',
	'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ő',
	'Ŕ', 'Ř',
	'Ś', 'Ş', 'Š',
	'ñ', 'ń', 'ň',
	'ò', 'ó', 'ô', 'ö', 'ø', 'ő',
	'ŕ', 'ř',
	'ś', 'ş', 'š',
	'Ţ', 'Ť',
	'Ù', 'Ú', 'Û', 'Ų', 'Ü', 'Ů', 'Ű',
	'Ý', 'ß',
	'Ź', 'Ż', 'Ž',
	'ţ', 'ť',
	'ù', 'ú', 'û', 'ų', 'ü', 'ů', 'ű',
	'ý', 'ÿ',
	'ź', 'ż', 'ž',
	'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р',
	'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'р',
	'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
	'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
	);
				 
	$rempl = array(
	'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'A', 'A',
	'C', 'C', 'C', 'CE',
	'D', 'D',
	'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'a', 'a',
	'c', 'c', 'c', 'ce',
	'd', 'd',
	'E', 'E', 'E', 'E', 'E', 'E',
	'G',
	'I', 'I', 'I', 'I', 'I',
	'L', 'L', 'L',
	'e', 'e', 'e', 'e', 'e', 'e',
	'g',
	'i', 'i', 'i', 'i', 'i',
	'l', 'l', 'l',
	'N', 'N', 'N',
	'O', 'O', 'O', 'O', 'O', 'O', 'O',
	'R', 'R',
	'S', 'S', 'S',
	'n', 'n', 'n',
	'o', 'o', 'o', 'o', 'o', 'o',
	'r', 'r',
	's', 's', 's',
	'T', 'T',
	'U', 'U', 'U', 'U', 'U', 'U', 'U',
	'Y', 'Y',
	'Z', 'Z', 'Z',
	't', 't',
	'u', 'u', 'u', 'u', 'u', 'u', 'u',
	'y', 'y',
	'z', 'z', 'z',
	'A', 'B', 'B', 'r', 'A', 'E', 'E', 'X', '3', 'N', 'N', 'K', 'N', 'M', 'H', 'O', 'N', 'P',
	'a', 'b', 'b', 'r', 'a', 'e', 'e', 'x', '3', 'n', 'n', 'k', 'n', 'm', 'h', 'o', 'p',
	'C', 'T', 'Y', 'O', 'X', 'U', 'u', 'W', 'W', 'b', 'b', 'b', 'E', 'O', 'R',
	'c', 't', 'y', 'o', 'x', 'u', 'u', 'w', 'w', 'b', 'b', 'b', 'e', 'o', 'r'
	);
		 
	$nom_fichier = str_replace($cible, $rempl, $nom_fichier);

	$nom_fichier = preg_replace('#[^.a-z0-9_-]+#i', '', $nom_fichier);
						 
	if (trim($nom_fichier) !== '')						 
	return $nom_fichier;
	else
	return false;
}




public function Rename_fich($adresse_fichier, $incr = false, $unix = false, $stop = 0)
{
	if (is_file($adresse_fichier))
	{
		$info = pathinfo($adresse_fichier);
		$extension = isset($info['extension']) && $info['extension'] != '' ? '.'.$info['extension'] : null;
		$dossier = $info['dirname'];
		$filename = $info['filename'];
		
		if (trim($incr) != false && $stop < 90)
		{
			$file = addcslashes($filename,'.');			
			$ext = isset($extension) ? addcslashes($extension,'.') : null;									

			$match = $unix ? '#^'.$file.'_[0-9]+'.$ext.'$#' : '#^'.$file.'_[0-9]+'.$ext.'$#i';
			
			$tab_identique = array();
			
			$files = new RegexIterator(new DirectoryIterator($dossier),$match);
			foreach ($files as $fileinfo) $tab_identique[] = $fileinfo->getFilename();

			natsort($tab_identique);
			
			$dernier = array_pop($tab_identique);
			
			unset($tab_identique);
						
			$dernier = isset($dernier)? pathinfo($dernier, PATHINFO_FILENAME) : '';
			
			$file = preg_replace_callback('#([0-9]+$)#', create_function('$matches','return $matches[1]+1;'), $dernier, '1', $count);

			$filename = !empty($count)? $file : $filename.'_1';
		}
		else
		{
			$filename .= '_'.uniqid();
		}
																													
		$filename = isset($extension) ? $filename.$extension : $filename;												
																				 
		$adresse = $dossier.'/'.$filename;
		
		if (!is_file($adresse)) return $adresse;
		else																													
		return $this->Rename_fich($adresse_fichier, $incr, $unix, ++$stop);                        
	}																				 
	else 
	{
		return $adresse_fichier;
	}
}



			 
private function Enoughmem ($x, $y, $max_mem, $rgb = 3) 
{	
	if (function_exists('memory_get_usage')) 
		{
			//http://www.php.net/manual/fr/function.imagecreatetruecolor.php#99623
			return ( $x * $y * $rgb * 1.7 < $max_mem - memory_get_usage() );
		}
	else return true;
}




private function Infos_image ($fich)
{
	$types_accepte = array(1,2,3);
	 
	$infos = @getimagesize($fich);     
				 
	if (!empty($infos[0]) && !empty($infos[1]) && !empty($infos[2]) && in_array($infos[2],$types_accepte))
				 
	return array($infos[0], $infos[1], $infos[2]);                 
	else       
	return false;
}




private function Image_create($fich, $type)
{
	switch ($type)
	{
		case "1" : $nouvelle_image = @imagecreatefromgif($fich); break;
		case "2" : $nouvelle_image = @imagecreatefromjpeg($fich); break;
		case "3" : $nouvelle_image = @imagecreatefrompng($fich); break;
					 
		default : $nouvelle_image = null;
	}
					 
	if (is_resource($nouvelle_image))
 
	return $nouvelle_image;
	else
	return false;
}




private function Envoi_image($ressource, $destination, $type, $qualite)
{   
	switch ($type)
	{	
		case "1" : $envoi = @imagegif($ressource, $destination); break;
		case "2" : $envoi = @imagejpeg($ressource, $destination, $qualite); break;
		case "3" : $qualite = $qualite == 0 ? 1 : $qualite;
					$qualite = 10 - ceil($qualite/10);                      
					$envoi = @imagepng($ressource, $destination, $qualite, PNG_ALL_FILTERS);
					break;
					 
		default : $envoi = false;
	}
								 
	if ($envoi != false)
				 
	return true;  
	else
	return false;
}




public function Dim_Prop_max($largeur_ini=0,$hauteur_ini=0,$largeur_max=0,$hauteur_max=0,$agrandissement=false)
{
	if(empty($largeur_ini) || empty($hauteur_ini) || !is_numeric($largeur_ini) || !is_numeric($hauteur_ini)) return false;
	
	$largeur_ini = abs(round($largeur_ini));
	$hauteur_ini = abs(round($hauteur_ini));
	$largeur_max = is_numeric($largeur_max) ? abs(round($largeur_max)) : 0;
	$hauteur_max = is_numeric($hauteur_max) ? abs(round($hauteur_max)) : 0;	
	$stop_redim = trim($agrandissement != false)? false : true;
	
	$ratio_orig = $largeur_ini/$hauteur_ini;
				 
	if(!empty($largeur_max) && empty($hauteur_max))
	{                      
		$largeur_fin = $largeur_max;
		$hauteur_fin = round($largeur_max/$ratio_orig);
	}
	else if(empty($largeur_max) && !empty($hauteur_max))
	{      
		$largeur_fin = round($hauteur_max*$ratio_orig);
		$hauteur_fin = $hauteur_max;
	}
	else
	{
		$ratioh = $hauteur_max/$hauteur_ini;
		$ratiow = $largeur_max/$largeur_ini;
		$ratio = min($ratioh, $ratiow);

		$largeur_fin = round($ratio*$largeur_ini);
		$hauteur_fin  = round($ratio*$hauteur_ini);        
	}

	if((($largeur_fin > $largeur_ini || $hauteur_fin > $hauteur_ini) && $stop_redim) || (empty($largeur_max) && empty($hauteur_max)))
	{
		$largeur_fin = $largeur_ini;
		$hauteur_fin = $hauteur_ini;
	}      
	
	return array($largeur_fin,$hauteur_fin);
}




private function Redim_liste ($index_champ, $nom_local, $nom_temp, $adresse_fichier, $tab_suffixe)
{                              
	$info_image = $this->Infos_image ($nom_temp);
	 
	$this->dim_image_source = null;

	if ($info_image === false)            
	{
		$this->Set_message ($this->repertoire, $index_champ, '"'.$nom_local.'"'.$this->tab_mes[7]);
		$this->Set_result ($this->repertoire, $index_champ, false);
																																				
		return false;
	}
	else
	{
		$this->dim_image_source = array_slice($info_image, 0, 2);
	}      
 
	$dim_max = true;
 
	if ($info_image[2] == 2)               
	{                      
		$m_limit = isset($this->memory_limit_serveur) ? $this->Return_Octets($this->memory_limit_serveur) : null;

		if (isset($m_limit) && !$this->Enoughmem($info_image[0],$info_image[1],$m_limit))
		{
			$dim_max = false;                                      
		}
	}

	$nouvelle_image = $dim_max === true ? $this->Image_create ($nom_temp, $info_image[2]) : false;

	$nom_fichier = basename($adresse_fichier);
	$extension = pathinfo($nom_fichier, PATHINFO_EXTENSION);
	$extension = $extension != '' ? '.'.$extension : null;
	$nom_fichier = pathinfo($nom_fichier, PATHINFO_FILENAME);
	
	
	foreach ($this->redimension as $rep => $redim)
	{
		$i = 0;
		foreach ($redim as $value)
		{
			if($dim_max === false)
			{
				$this->Set_message ($rep, $index_champ, '"'.$nom_local.'"'.$this->tab_mes[11]);
				$this->Set_result ($rep, $index_champ, false);
				
				break 2;
			}
	  
			if ($nouvelle_image === false)
			{
				$this->Set_message ($rep, $index_champ, '"'.$nom_local.'"'.$this->tab_mes[12]);										
				$this->Set_result ($rep, $index_champ, false);
				 
				break 2;
			}
	
			$largeur_max = $value['L_max'];
			$hauteur_max = $value['H_max'];
			$qualite = $value['Qualite'];
			$agrandissement = $value['agrandissement'];                          
			$prefixe = $value['prefixe'];
			$suffixe = $tab_suffixe[$rep][$i++];
		
			$nom_fich = $prefixe.$nom_fichier.$suffixe.$extension;

			$dimensions = $this->Dim_Prop_max($info_image[0],$info_image[1], $largeur_max,$hauteur_max,$agrandissement);// ne retournera jamais false car les conditions initiales sont déjà vérifiées par Infos_image
			
			$largeur_fin = $dimensions[0];
			$hauteur_fin = $dimensions[1];
	
			$redimensionnement = $info_image[0] == $largeur_fin && $info_image[1] == $hauteur_fin ? false : true;
	
			if ($redimensionnement && $nouvelle_image !== false)
			{    
				$dim_desti = true;
				 
				if ($info_image[2] == 2)               
				{
					if ($m_limit !== false && !$this->Enoughmem($largeur_fin,$hauteur_fin,$m_limit))
					$dim_desti = false;
				}
	
				$ressource = $dim_desti == true ? @imagecreatetruecolor ($largeur_fin, $hauteur_fin) : false;
							 
				if (!is_resource ($ressource))
				{
					$this->Set_message ($rep, $index_champ, '"'.$nom_fich.'"'.$this->tab_mes[11]);									
					$this->Set_result ($rep, $index_champ, false);
	
					break 2;
				}
				
				// fond transparent pour les png avec transparence
				if ($info_image[2] == 3) 
				{
					$alpha = @imagesavealpha($ressource, true);
					if($alpha)
					{
						$trans_color = @imagecolorallocatealpha($ressource, 0, 0, 0, 127);
						if($trans_color !== false) @imagefill($ressource, 0, 0, $trans_color);
					}
				}
	
				$redimensionnement = @imagecopyresampled ($ressource, $nouvelle_image, 0, 0, 0, 0, $largeur_fin, $hauteur_fin, $info_image[0], $info_image[1]);
							 
				if ($redimensionnement == false)
				{
					$this->Set_message ($rep, $index_champ, '"'.$nom_fich.'"'.$this->tab_mes[13]);								
					$this->Set_result ($rep, $index_champ, false);
	
					break 2;
				}
				
				$envoi = $this->Envoi_image ($ressource, $this->Adresse_repertoire($rep).$nom_fich, $info_image[2], $qualite);
							 
				@imagedestroy($ressource);
	
				if ($envoi === false)
				{
					$this->Set_message ($rep, $index_champ, '"'.$nom_fich.'"'.$this->tab_mes[14]);							
					$this->Set_result ($rep, $index_champ, false);
	
					break 2;
				}				 
				else			 
				{						
					$resultat1 = !empty($this->tab_mes[1]) ? '"'.$nom_fich.'"'.$this->tab_mes[15].$largeur_fin.$this->tab_mes[21].$hauteur_fin.$this->tab_mes[1].$rep : null;
					
					$resultat2 = !empty($this->tab_mes[1]) ? '"'.$nom_local.'"'.$this->tab_mes[2].'"'.$nom_fich.'"'.$this->tab_mes[15].$largeur_fin.$this->tab_mes[21].$hauteur_fin.$this->tab_mes[1].$rep : null;
	
					$resultat = $nom_local === $nom_fich ?  $resultat1 : $resultat2;
	
					if (isset($resultat))
					{
						$this->Set_message ($rep, $index_champ, $resultat);
					}
					else if (isset($this->tab_mes[18]))
					{
						$this->Set_message ($rep, $index_champ, '"'.$nom_fich.'" '.$this->tab_mes[18]);
					}
	
					$dim = $largeur_fin.$this->tab_mes[21].$hauteur_fin;
																 
					$this->Set_result ($rep, $index_champ, array($nom_local,$nom_fich,$dim));
				}
			}
	
			else if ($nouvelle_image !== false)
						 
			{
				$envoi = $this->Envoi_image ($nouvelle_image, $this->Adresse_repertoire($rep).$nom_fich, $info_image[2], $qualite);
							 
				if ($envoi === false)
				{
					$this->Set_message ($rep, $index_champ, '"'.$nom_fich.'"'.$this->tab_mes[3]);								
					$this->Set_result ($rep, $index_champ, false);
																												
					break 2;
				}
				else
				{
					$resultat1 = !empty($this->tab_mes[1]) ? '"'.$nom_fich.'"'.$this->tab_mes[16].$largeur_fin.$this->tab_mes[21].$hauteur_fin.$this->tab_mes[1].$rep : null;
					
					$resultat2 = !empty($this->tab_mes[1]) ? '"'.$nom_local.'"'.$this->tab_mes[2].'"'.$nom_fich.'"'.$this->tab_mes[16].$largeur_fin.$this->tab_mes[21].$hauteur_fin.$this->tab_mes[1].$rep : null;
	
					$resultat = $nom_local === $nom_fich ?  $resultat1 : $resultat2;
	
					if (isset($resultat))
					{
						$this->Set_message ($rep, $index_champ, $resultat);
					}
					else if (isset($this->tab_mes[18]))
					{
						$this->Set_message ($rep, $index_champ, '"'.$nom_fich.'" '.$this->tab_mes[18]);
					}	
								 
					$dim = $largeur_fin.$this->tab_mes[21].$hauteur_fin;											
					$this->Set_result ($rep, $index_champ, array($nom_local,$nom_fich,$dim));
				}			 
			}
		}
	}
	
	@imagedestroy($nouvelle_image);
}
//FIN DE CLASSE
}
?>