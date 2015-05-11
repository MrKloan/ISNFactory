<?php
/**
 * Classe d'abstraction gérant la connexion et les interractions bas niveau avec la base de données.
 */
class SQL
{
	public $base = NULL;

	public function __construct()
	{
		//Tentative de connexion à la base de données.
		try
		{
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION; //Gestion d'erreurs par émission d'Exceptions
			$this->base = new PDO(DB_TYPE.":host=".DB_HOST.";dbname=".DB_BASE, DB_USER, DB_PASSWORD, $pdo_options);
			$this->base->exec("SET NAMES ".DB_ENCODING); //Modification de l'encodage utilisé pour interagir avec la BDD
		}
		//En cas d'échec de la connexion...
		catch(PDOException $e)
		{
			if(DEBUG)
				die($e);
			else
				die('Erreur lors de la connexion à la base de données.');
		}
	}

	/**
	 * Sélectionne et renvoie sous forme d'objet les informations souhaitées dans la base de données.
	 * @param $request Un tableau associatif contenant les paramètres nécessaires à l'exécution de la requête.
	 */
	public function select($request)
	{
		try
		{
			$sql = 'SELECT * FROM '.$request['table'].' ';
			$params = array();

			//S'il existe des conditions à cette sélection...
			if(isset($request['conditions']))
			{
				$sql .= 'WHERE ';
				$conditions = $request['conditions'];
				//S'il n'y a qu'une seule condition : 'conditions' => 'id=1'
				if(!is_array($conditions))
				{
					if(strstr($conditions, '='))
					{
						$sql .= substr($conditions, 0, strpos($conditions, '=')+1);
						if(substr($conditions, strpos($conditions, '=')+1, strlen($conditions)-strpos($conditions, '=')) == 'NOW()')
							$sql .= 'NOW()';
						else
						{
							$sql .= '?';
							array_push($params, substr($conditions, strpos($conditions, '=')+1, strlen($conditions)-strpos($conditions, '=')));
						}
					}
					else if(strstr($conditions, '<>'))
					{
						$sql .= substr($conditions, 0, strpos($conditions, '<>')+2);
						if(substr($conditions, strpos($conditions, '<>')+2, strlen($conditions)-strpos($conditions, '<>')) == 'NOW()')
							$sql .= 'NOW()';
						else
						{
							$sql .= '?';
							array_push($params, substr($conditions, strpos($conditions, '<>')+2, strlen($conditions)-strpos($conditions, '<>')));
						}
					}
				}
				//S'il y en a plusieurs : 'conditions' => array('id' => '1', 'role' => 'role_student')
				else
				{
					$cond = array();
					foreach($conditions as $column=>$value)
					{
						if($value == 'NOW()')
							array_push($cond, 'NOW()');
						else
						{
							array_push($cond, $column.'=?');
							array_push($params, $value);
						}
					}
					$sql .= implode(' AND ', $cond);
				}
			}
			//Exécution de la requête.
			$result = $this->base->prepare($sql);
			$result->execute($params);
			//Renvoie du résultat sous forme d'objet.
			return $result->fetchAll(PDO::FETCH_OBJ);
		}
		catch(PDOException $e)
		{
			if(DEBUG)
				die($e);
			else
				die('Erreur lors de la récupération d\'informations en provenance de la base de données.');
		}
	}

	/**
	 * Renvoie le premier élément d'une sélection.
	 * @param $request Un tableau associatif contenant les paramètres nécessaires à l'exécution de la requête.
	 */
	public function selectFirst($request)
	{
		return current($this->select($request));
	}

	/**
	 * Met à jour une information de la base de données.
	 * @param $request Un tableau associatif contenant les paramètres nécessaires à l'exécution de la requête.
	 */
	public function update($request)
	{
		try
		{
			$sql = 'UPDATE ';
			$params = array();

			//S'il n'y a qu'une seule colonne à mettre à jour, on formatte la requête.
			if(!is_array($request['columns']))
			{
				$sql .= $request['table'].' SET '.$request['columns'].'=?';
				array_push($params, $request['values']);
			}
			//Si plusieurs colonnes sont spécifiées, alors c'est qu'autant de valeurs ont dûes l'être également.
			else
			{
				$cols = array();
				for($i=0 ; $i < count($request['columns']) ; $i++)
				{
					if($request['values'][$i] == 'NOW()')
						array_push($cols, $request['columns'][$i].'=NOW()');
					else
					{
						array_push($cols, $request['columns'][$i].'=?');
						array_push($params, $request['values'][$i]);
					}
				}
				//On reformate la requête
				$sql .= $request['table'].' SET '.implode(', ', $cols);
			}

			//Gestion des conditions
			if(isset($request['conditions']))
			{
				$sql .= ' WHERE ';
				$conditions = $request['conditions'];
				//S'il n'y a qu'une seule condition : 'conditions' => 'id=1'
				if(!is_array($conditions))
				{
					$sql .= substr($conditions, 0, strpos($conditions, '=')+1);
					if(substr($conditions, strpos($conditions, '=')+1, strlen($conditions)-strpos($conditions, '=')) == 'NOW()')
						$sql .= 'NOW()';
					else
					{
						$sql .= '?';
						array_push($params, substr($conditions, strpos($conditions, '=')+1, strlen($conditions)-strpos($conditions, '=')));
					}
				}
				//S'il y en a plusieurs : 'conditions' => array('id' => '1', 'role' => 'role_student')
				else
				{
					$cond = array();
					foreach($conditions as $column=>$value)
					{
						if($value == 'NOW()')
							array_push($cond, $column.'=NOW()');
						else
						{
							array_push($cond, $column.'=?');
							array_push($params, $value);
						}
					}
					$sql .= implode(' AND ', $cond);
				}
			}
			//Exécution de la requête.
			$result = $this->base->prepare($sql);
			$result->execute($params);
			return true;
		}
		catch(PDOException $e)
		{
			if(DEBUG)
				die($e);
			else
				die('Erreur lors de la mise à jour d\'informations dans la base de données.');
		}
	}

	/**
	 * Insère une ou plusieurs lignes dans une table de la base de données.
	 * @param $request Un tableau associatif contenant les paramètres nécessaires à l'exécution de la requête.
	 */
	public function insert($request)
	{
		try
		{
			$sql = 'INSERT INTO '.$request['table'].' VALUES ';
			$params = array();
			$values = $request['values'];

			//Si la première valeur n'est pas un tableau, on concatène la nouvelle ligne.
			if(!is_array(current($values)))
			{
				$vals = array();
				//On parcourt les éléments du tableau et on les enregistre dans $params.
				for($i=0 ; $i < count($values) ; $i++)
				{
					if($values[$i] == 'NOW()')
						array_push($vals, 'NOW()');
					else
					{
						array_push($vals, '?');
						array_push($params, $values[$i]);
					}
				}
				$sql .= '('.implode(',', $vals).')';
			}
			//Sinon, on concatène les n nouvelles lignes à insérer.
			else
			{
				$lines = array();
				//$i = chaque nouvelle ligne (chaque élément du tableau $values).
				for($i=0 ; $i < count($values) ; $i++)
				{
					$vals = array();
					//On parcourt les éléments du tableau et on les enregistre dans $params.
					for($j=0 ; $j < count($values[$i]) ; $j++)
					{
						if($values[$i] == 'NOW()')
							array_push($vals, 'NOW()');
						else
						{
							array_push($vals, '?');
							array_push($params, $values[$i][$j]);
						}
					}
					array_push($lines, '('.implode(',', $vals).')');
				}
				//On formatte la requête
				$sql .= implode(',', $lines);
			}
			//Exécution de la requête
			$result = $this->base->prepare($sql);
			$result->execute($params);
			return true;
		}
		catch(PDOException $e)
		{
			if(DEBUG)
				die($e);
			else
				die('Erreur lors de l\'insertion d\'informations dans la base de données.');
		}
	}

	/**
	 * Supprime une ou plusieurs lignes dans une table de la base de données.
	 * @param $request Un tableau associatif contenant les paramètres nécessaires à l'exécution de la requête.
	 */
	public function delete($request)
	{
		try
		{
			$sql = 'DELETE FROM '.$request['table'].' ';
			$params = array();

			if(isset($request['conditions']))
			{
				$sql .= 'WHERE ';
				$conditions = $request['conditions'];
				//S'il n'y a qu'une seule condition : 'conditions' => 'id=1'
				if(!is_array($conditions))
				{
					if(strstr($conditions, '='))
					{
						$sql .= substr($conditions, 0, strpos($conditions, '=')+1).'?';
						array_push($params, substr($conditions, strpos($conditions, '=')+1, strlen($conditions)-strpos($conditions, '=')));
					}
					else if(strstr($conditions, '<>'))
					{
						$sql .= substr($conditions, 0, strpos($conditions, '<>')+2).'?';
						array_push($params, substr($conditions, strpos($conditions, '<>')+2, strlen($conditions)-strpos($conditions, '<>')));
					}
				}
				//S'il y en a plusieurs : 'conditions' => array('id' => '1', 'role' => 'role_student')
				else
				{
					$cond = array();
					foreach($conditions as $column=>$value)
					{
						array_push($cond, $column.'=?');
						array_push($params, $value);
					}
					$sql .= implode(' AND ', $cond);
				}
			}
			//Exécution de la requête.
			$result = $this->base->prepare($sql);
			$result->execute($params);
			return true;
		}
		catch(PDOException $e)
		{
			if(DEBUG)
				die($e);
			else
				die('Erreur lors de la suppression d\'informations de la base de données.');
		}
	}

	/**
	 * Renvoie le résultat d'une requête brute envoyée à la base de données.
	 * @param $request La requête destinée à la base de données.
	 */
	public function query($request)
	{
		try
		{
			$result = $this->base->query($request);

			try
			{
				return $result->fetchAll(PDO::FETCH_OBJ);
			}
			catch(PDOException $fe)
			{
				return true;
			}
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}

	/**
     * Renvoie la chaîne d'entrée après avoir échappé tous les caractères potentiellement nuisibles pour la base de données.
     */
    public function secure($str)
    {
        return htmlspecialchars(stripslashes(trim($this->base->quote($str), "'")), ENT_QUOTES);
    }
}
?>