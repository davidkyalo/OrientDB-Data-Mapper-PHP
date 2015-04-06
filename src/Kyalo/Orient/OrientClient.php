<?php namespace Kyalo\Orient;


use PhpOrient\Protocols\Binary\Data\Record as OrientRecord;
use PhpOrient\Protocols\Binary\Data\ID as OrientID;
use PhpOrient\PhpOrient;

class OrientClient {

	protected $oClient;
	protected $builder;
	protected $defaultConnectionAlias;

	public function __construct(PhpOrient $oClient = null){
		$oClient = is_null($oClient) ? new PhpOrient : $oClient;
		$this->setOClient($oClient);
	}

	public function setOClient(PhpOrient $oClient){
		$this->oClient = $oClient;
		$this->buildConnection();
		return $this;
	}

	protected function buildConnection(){
		$config = $this->getConnectionConfig();
		
		$this->oClient->hostname = $config['host'];
		$this->oClient->port = $config['port'];
		//$this->oClient->connect($config['username'], $config['password']);
		$this->oClient->dbOpen( $config['database'], $config['username'], $config['password'] );
	}

	protected function getConnectionConfig($alias = null){
		$oConfig = $this->getOrientConfig();
		$alias = is_null($alias) ? $oConfig['default'] : $alias;
		$config = $this->getConfig(
					'connections.'. $alias, 
					$this->getConfig('orient_sample_connection'));
		return $config;
	}

	protected function getOrientConfig(){
		return $this->getConfig('orientdb', []);
	}

	protected function getConfig($option = null, $default = null){
		if(is_null($option)) 
			return config('database', []);
		return config('database.'.$option, $default);
	}

	public function execute($command){
		return $this->oClient->command($command);
	}

	public function query($query){
		return $this->oClient->query($query);
	}

	public function recordLoad(OrientID $oID, $args = []){
		$record = $this->oClient->recordLoad($oID, $args);
		return $record;
	}

	public function recordCreate(OrientRecord $record){
		$rec = $this->oClient->recordCreate($record);
		return $rec;
	}

	public function recordUpdate(OrientRecord $record){
		$rec = $this->oClient->recordUpdate($record);
		return $rec;
	}

	public function recordDelete(OrientID $oID){
		$delete = $this->oClient->recordDelete($oID);
		return $delete;
	}

}