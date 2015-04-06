<?php namespace Kyalo\Orient;


use PhpOrient\Protocols\Binary\Data\Record as OrientRecord;
use PhpOrient\Protocols\Binary\Data\ID as OrientID;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class Entity  /*implements \ArrayAccess, Arrayable, Jsonable, \JsonSerializable */{

	protected $_properties = [];

	protected $id;

	protected $oClass;

	protected $oRecordData = [];

	public $_exists = false;

	public function getOClass(){
		return $this->oClass;
	}

	public function setOClass($oClass){
		$this->oClass = $oClass;
		return $this;
	}


	public function loadORecordData(Array $oData, $exists = true, $sync = true){
		foreach ($oData as $key => $value) {
			$this->oRecordData[$key] = $value;

			if($sync) $this->_properties[$key] = $value;
		}
		$this->_exists = $exists;
		return $this;
	}
/*
	public static function createInstance(OrientRecord $record){
		$entity = new static();
		$entity->setOrientRecord($record);
		return $entity;
	}

	public function setOrientRecord(OrientRecord $record){
		$this->oRecord = $record;
		$this->oID = $record->getRid();
		$this->setRawProperties($record->getOData())
		return $this;
	}
*/
	public function fill(Array $properties){
		foreach ($properties as $key => $property) {
			$this->_properties[$key] = $property;
		}
		return $this;
	}

	protected function setRawProperties(Array $properties){
		foreach ($properties as $key => $property) {
			$this->_properties[$key] = $property;
		}
		return $this;
	}

/*	
	public function perpOrientRecord(){
		if(!$this->oRecord)
			$this->oRecord = new OrientRecord;
		$this->oRecord->setOData($this->_properties);
		if($this->oID)
			$this->oRecord->setRid($this->oID);

		return $this;
	}


	public function getOrientRecord(){
		//$this->perpOrientRecord();
		return $this->oRecord;
	}
*/

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getId(){
		return $this->id;
	}

	public function getOProperties(){
		$oProperties = $this->_properties;
		if(isset($oProperties['id']))
			unset($oProperties['id']);

		return $oProperties;
	}

	public function getDirtyOProperties(){
		$dirty = array();
		foreach ($this->getOProperties() as $key => $value) {
			if(!array_key_exists($key, $this->oRecordData)){
				$dirty[$key] = $value;
			}
			elseif ($value !== $this->oRecordData[$key]) {
				$dirty[$key] = $value;
			}
		}
		return $dirty;
	}

	public function getDirtyProperties(){
		$dirty = array();
		foreach ($this->_properties as $key => $value) {
			if(!array_key_exists($key, $this->oRecordData)){
				$dirty[$key] = $value;
			}
			elseif ($value !== $this->oRecordData[$key]) {
				$dirty[$key] = $value;
			}
		}
		return $dirty;
	}

	public function __get($property){
		if($property == 'id')
			return $this->getId();

		if(in_array($property, $this->_properties)){
			return $this->_properties[$property];
		}

	}

	public function __set($property, $value){
		$this->_properties[$property] = $value;
	}

}