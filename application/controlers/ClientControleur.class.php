﻿<?php
class ClientControleur{
	
	private $_clientManager;
		
	public function __construct(ClientManager $clientManager){
		$this->_clientManager=$clientManager;
	}
	
	public function get($numero){
		return $this->_clientManager->get($numero);
	}
	
	public function getList($critereTri){
		$numero = '%';
		if (!empty($_POST['numero'])) {$numero.=$_POST['numero'].'%';}
		
		$nom = '%';
		if (!empty($_POST['nom'])) {$nom.=$_POST['nom'].'%';}
		
		$prenom = '%';
		if (!empty($_POST['prenom'])) {$prenom.=$_POST['prenom'].'%';}
			
		$adresse = '%';
		if (!empty($_POST['adresse'])) {$adresse.=$_POST['adresse'].'%';}		
		
		$referant = '%';
		if (!empty($_POST['referant'])) {$referant.=$_POST['referant'].'%';}	
		
		$liste_clients = $this->_clientManager->getList($numero, $nom, $prenom, $adresse, $referant, 'nom');
		return $liste_clients;
	}
	
	public function addClient(){
		$out='';
		if (!empty($_POST['numero']) ) {
			$client = new Client($_POST);
			
			if (!$this->_clientManager->exists($client)) {
				if($this->_clientManager->add($client)){
					$out='Le client numéro '.$_POST['numero'].' a bien été ajouté.';
				}else{
					$out='OUPS ! Il y a eu un problème.'; 
				}
			} else {
				$out='Erreur : ce numéro est déjà pris ! ';
			}
		}else{
			$out='Erreur : vous ne devriez pas être ici !';
		}
		return $out;
	}
	
	public function editClient(){
		$out='';
		if (!empty($_POST['numero']) ) {
			$client = new Client($_POST);
			
			if ($this->_clientManager->exists($client)) {
				if($this->_clientManager->update($client)){
					$out='Le client numéro '.$_POST['numero'].' a bien été modifié.';
				}else{
					$out='OUPS ! Il y a eu un problème.'; 
				}
			} else {
				$out='Erreur : ce numéro n\'existe pas ! ';
			}
		}else{
			$out='Erreur : vous ne devriez pas être ici !';
		}
		return $out;
	}
	
	public function deleteClient($client){
		return ($this->_clientManager->delete($client))?'Le client immatriculée '.$client->numero().' a bien été supprimé.':'OUPS ! Il y a eu un problème.'; 
	}
	
}
?>