<?php
class CommentaireManager
{
	private $_db;

	const ACTION_REUSSIE = 1;
	const ACTION_ECHOUEE = 0;

	public function __construct($db){$this->setDb($db);}
	
	public function setDb($db){$this->_db = $db;}

	# prend un commentaire en argument, l'ajoute en base de données, retourne 1
	public function add(Commentaire $commentaire)
	{
		$q = $this->_db->prepare('INSERT INTO commentaire SET voiture = :voiture, technicien = :technicien, date = :date, texte= :texte');

		$q->bindValue(':voiture',$commentaire->voiture(),PDO::PARAM_STR);
		$q->bindValue(':technicien',$commentaire->technicien(),PDO::PARAM_INT);
		$q->bindValue(':date',$commentaire->date()->format('Y-m-d H:i:s'),PDO::PARAM_STR); // insère la date au format Y-m-d H:i:s
		$q->bindValue(':texte',$commentaire->texte(),PDO::PARAM_STR);
		$q->execute();
		return self::ACTION_REUSSIE;
	}

	# compte le nombre de commentaire en base de donnée, retourne un int
	public function count()
	{
		return $this->_db->query('SELECT COUNT(*) FROM commentaire')->fetchColumn();
	}

	# supprime un commentaire en base de donnée, retourne 1
	public function delete(Commentaire $commentaire)
	{
		$q = $this->_db->prepare('DELETE FROM commentaire WHERE (voiture = :voiture AND technicien = :technicien AND date = :date)');
		$q->bindValue(':voiture',$commentaire->voiture(),PDO::PARAM_STR);
		$q->bindValue(':technicien',$commentaire->technicien(),PDO::PARAM_INT);
		$q->bindValue(':date',$commentaire->date(),PDO::PARAM_STR);
		$q->execute();
		return self::ACTION_REUSSIE;
	}

	# teste si un commentaire existe, renvoie un booléen
	public function exists(Commentaire $commentaire)
	{    
		$q = $this->_db->prepare('SELECT COUNT(*) FROM commentaire WHERE (voiture = :voiture AND technicien = :technicien AND date = :date)');
		$q->bindValue(':voiture',$commentaire->voiture(),PDO::PARAM_STR);
		$q->bindValue(':technicien',$commentaire->technicien(),PDO::PARAM_INT);
		$q->bindValue(':date',$commentaire->date(),PDO::PARAM_STR);
		$q->execute();
    
		return (bool) $q->fetchColumn();
	}

	# prend une voiture, un technicien et une date en argument, retourne un commentaire si il existe
	public function get($voiture, $technicien, $date)
	{
		$q = $this->_db->prepare('SELECT * FROM commentaire WHERE (voiture = :voiture AND technicien = :technicien AND date = :date)');	

		$q->bindValue(':voiture',$voiture->immatriculation(),PDO::PARAM_STR);
		$q->bindValue(':technicien',$technicien->numero(),PDO::PARAM_INT);
		$q->bindValue(':date',$date,PDO::PARAM_STR);

		$q->execute();

		$commentaire = $q->fetch(PDO::FETCH_ASSOC);

		return empty($commentaire) ? null : new Commentaire($commentaire);
	}
	
	# prend en argument une voiture, un technicien ou une date, retourne un array de commentaires
	public function getList($voiture, $technicien, $date, $texte)
	{
		$commentaires = [];
		
		$q = $this->_db->prepare('
				SELECT * 
				FROM commentaire 
				WHERE voiture LIKE :voiture 
				AND technicien LIKE :technicien 
				AND date LIKE :date
				AND texte LIKE :texte 
				ORDER BY date
			');

    	$q->bindParam(':voiture', $voiture->immatriculation(), PDO::PARAM_STR);
    	$q->bindParam(':technicien', $technicien->numero(), PDO::PARAM_INT);
		$q->bindParam(':date', $date, PDO::PARAM_STR);
		$q->bindParam(':texte', $texte, PDO::PARAM_STR);
		$q->execute();
	    
		while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
		{
			$commentaires[] = new Commentaire($donnees); 
		}
		return $commentaires;
	}

	# prend un commentaire en argument, l'update en base de donnée, retourne 1 si l'action a réussi, 0 sinon
	public function update(Commentaire $commentaire)
	{
		if($this->exists($commentaire))
		{
			$q = $this->_db->prepare('
				UPDATE commentaire 
				SET texte= :texte 
				WHERE (voiture = :voiture AND technicien = :technicien AND date = :date)
			');
		    
			$q->bindValue(':voiture',$commentaire->voiture(),PDO::PARAM_STR);
			$q->bindValue(':technicien',$commentaire->technicien(),PDO::PARAM_INT);
			$q->bindValue(':date',$commentaire->date(),PDO::PARAM_STR);
			$q->bindValue(':texte',$commentaire->texte(),PDO::PARAM_STR);
		    
			$q->execute();
			return self::ACTION_REUSSIE;
		}
		else
		{
			return self::ACTION_ECHOUEE;
		}
	}
}
