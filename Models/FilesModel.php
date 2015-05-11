<?php
class FilesModel extends Model
{
	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		$config = $dispatcher->Core->Config->projets;
		if($role != "role_professor")
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');

		parent::__construct($dispatcher);
	}

	public $recognized_ext = array(
		'css',
		'doc',
		'flv',
		'gif',
		'html',
		'jpeg',
		'jpg',
		'mov',
		'mp4',
		'mpeg',
		'pdf',
		'php',
		'png',
		'ppt',
		'rar',
		'swf',
		'txt',
		'wmv',
		'xls',
		'zip'
	);

	public $extensions = array(
		'css'=>'Feuille de style',
		'doc'=>'Microsoft Word',
		'flv'=>'Fichier FLV',
		'gif'=>'Image GIF',
		'html'=>'Page web',
		'jpeg'=>'Image JPEG',
		'jpg'=>'Image JPEG',
		'mov'=>'Fichier MOV',
		'mp4'=>'Fichier MP4',
		'mpeg'=>'Fichier MPEG',
		'pdf'=>'Adobe Acrobat',
		'php'=>'Script PHP',
		'png'=>'Image PNG',
		'ppt'=>'Microsoft Power Point',
		'rar'=>'Archive WinRar',
		'swf'=>'Fichier SWF',
		'txt'=>'Document texte',
		'wmv'=>'Fichier WMV',
		'xls'=>'Microsoft Excel',
		'zip'=>'Archive WinZip'
	);
	
	public function getParams()
	{
		$role = $this->dispatcher->Core->User->getRole();
		$params = $this->dispatcher->params;
		$params = rawurldecode(implode($params, "/"));
		if(!opendir(FILES.$params))
			$this->dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/files');
		else
			return $params;
	}

	public function getBreadcrumb($url)
	{
		$fildariane = array();
		$fildariane = explode('/', $url);
		return $fildariane;
	}
	

	public function getDirectories()
	{
		$maindirectory = FILES;
		$repertoires = array();

		if(false !==($ressource = opendir($maindirectory)))
		{
			while (false !== ($entree = readdir($ressource)))
			{
				if(is_dir($maindirectory."/".$entree))
					if(!preg_match("/^\./", $entree))
						$repertoires[] = $this->getInfos($maindirectory, $entree, 'repertoire');
			}
			closedir($ressource);
		}
		return $repertoires;
	}

	public function getContents($rep)
	{
		$repertoires = array();
		$fichiers = array();
		
		if(false !==($ressource = opendir($rep)))
		{
		
			while (false !== ($entree = readdir($ressource)))
			{
			
				if(is_dir($rep."/".$entree) ) {			
					if(!preg_match("/^\./", $entree)) {
						$repertoires[] = $this->getInfos($rep, $entree, 'repertoire');				
					}			
				}
				else{ 
					$fichiers[] = $this->getInfos($rep, $entree, 'fichier');
				}
			}
			
			// fusion des 2 tableaux pour affichage
			$list = array_merge($repertoires, $fichiers);
			
			closedir($ressource);
		}
		
		return $list;
	}

	public function getInfos($base, $entree, $type)
	{
		$infos = array();
		
		switch($type){
		
			case 'repertoire':
				$infos = array(				
					'type' => 'repertoire', 
					'nom' => $entree,
					'date' => filemtime($base."/".$entree), 
					'taille' => (int)filesize($base."/".$entree),
					'acces' => fileatime($base."/".$entree)
				);
				break;
			
			case 'fichier':
				$infos = array(
					'type' => 'fichier', 
					'nom' =>  pathinfo($base.'/'.$entree, PATHINFO_FILENAME), 
					'extension' => pathinfo($base.'/'.$entree, PATHINFO_EXTENSION), 
					'nom.extension' => pathinfo($base.'/'.$entree, PATHINFO_BASENAME ), 
					'taille' => (int)filesize($base.'/'.$entree), 
					'date' => filemtime($base.'/'.$entree), 
					'acces' => filemtime($base.'/'.$entree)
				);
				break;
			
			case 'erreur':
				$return = array("type"=>"erreur");
				break;

			default:
				$return = array("type"=>"erreur");
				break;				
		}
		
		return $infos;
		
	}

	public function formatSize($taille)
	{
		$unites = array('octets', 'Ko', 'Mo', 'Go', 'To');
		$i = 0;
		$nombre_a_afficher = 0;

		while($taille >= 1) {
			$number = $taille;
			$taille /= 1024;
			$i++;
		}
		
		if(!$i) $i=1;
		$d = explode(".", $number);
		if($d[0] != $number) {
			$number = number_format($number, 2, ",", " ");
		}
		
		return $number." ".$unites[$i-1];
	}
}
?>