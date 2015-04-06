<?php namespace Kyalo\Orient;


use PhpOrient\Protocols\Binary\Data\Record as OrientRecord;
use PhpOrient\Protocols\Binary\Data\ID as OrientID;

class Repository {

	protected $entityName = 'Entity';

	protected $oClusterId = -1;

	protected $DB;

	public function __construct($DB = null){
		$this->setDB($DB);
		return $this;
	}

	protected function makeOID($id = null){
		$oID = new OrientID($this->oClusterId, $id);
		return $oID;
	}

	protected function newEntityInstance(){
		$className = $this->entityName;
		return new $className;
	}

	protected function makeORecord($entity){
		$oID = $this->makeOID($entity->getId());
		$oData = $entity->_exists 
				? $entity->getDirtyOProperties() 
				: $entity->getOProperties();
		$record = ( new OrientRecord() )->setOData( $oData )->setRid( $oID );
		return $record;
	}

	protected function isORecord($rec){
		return $rec instanceof OrientRecord;
	}

	protected function isValidEntity($entity){
		$className = $this->entityName;
		return $entity instanceof $className;
	}

	public function setDB($DB = null){
		$this->DB = is_null($DB) ? app('orient') : $DB;
		return $this;
	}

	public function getDB(){
		return $this->DB;
	}

	public function setEntityName($name){
		$this->entityName = $name;
		return $this;
	}

	public function getEntityName(){
		return $this->entityName;
	}

	public function setOClusterId($oClusterId){
		$this->oClusterId = $oClusterId;
		return $this;
	}

	public function getOClusterId(){
		return $this->oClusterId;
	}

	public function find($id){
		$oID = $this->makeOID($id);
		$record = $this->DB->recordLoad($oID);
		if($this->isORecord($record[0])){
			$entity = $this->newEntityInstance();
			$this->loadEntityORecordData($entity, $record[0]);
			return $entity;
		}
		return null;

	}

	public function save($entity) {
		if($entity->_exists){
			echo "Updating";
			$saved = $this->update($entity);
		}
		else{
			echo "Creating";
			$saved = $this->create($entity);
		}		
		return $saved ? true : false;
	}

	public function update($data){
		if(is_array($data)){			
			if(isset($data['id'])){
				$entity = $this->newEntityInstance();
				$entity->setId($data['id']);
				unset($data['id']);
				$entity->fill($data);
			}
			else{
				return $this->create($data);
			}
		}
		elseif($this->isValidEntity($data)){
			$entity = $data;				
		}
		else{
			return false;
		}
		if(count($entity->getDirtyOProperties()) == 0){
			echo "Up to date";
			return true;
		}
		$record = $this->makeORecord($entity);
		$updated = $this->DB->recordUpdate($record);
		if($this->isORecord($updated)){
		 	$this->loadEntityORecordData($entity, $updated);
		 	return $entity;
		 }
		 return false;
	}



	public function create($data){

		if(is_array($data)){	
			$entity = $this->newEntityInstance();
			$entity->fill($data);
		}
		elseif($this->isValidEntity($data)){
			$entity = $data;				
		}
		$record = $this->makeORecord($entity);
		$created = $this->DB->recordCreate($record);
		if($this->isORecord($created)){
			$this->loadEntityORecordData($entity, $created);
		 	return $entity;
		 }
		 return false;
	}

	protected function loadEntityORecordData($entity, $oRecord, $exists = true, $sync = true){
		$entity->loadORecordData($oRecord->getOData(), $exists, $sync);
		$entity->setId($oRecord->getRid()->position);
		return $entity;
	}
}