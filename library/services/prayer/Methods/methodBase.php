<?php
abstract class MethodBase
{
	private $_name;
	protected $_parameters = array();

	public function getName()
	{
		return $this->_name;
	}
	
	public function setName($name)
	{
		$this->_name = $name;
	}
	
	public function getParameters()
	{
		return $this->_parameters;
	}
	
	public function setParameters(array $parameters)
	{
		$this->_parameters = $parameters;
	}

	public function __construct($name)
	{
		$this->_name = $name;
	}
}

?>
