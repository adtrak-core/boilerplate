<?php



namespace PGMB\DependencyInjection;


/**
 * Class Container
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 * @package PGMB\DependencyInjection
 */
class Container implements \ArrayAccess {

	/**
	 * Flag whether the container is locked or not.
	 *
	 * @var bool
	 */
	private $locked;

	/**
	 * Values stored within the container
	 *
	 * @var array
	 */
	private $values;

	/**
	 * Container constructor.
	 *
	 * @param array $values
	 */
	public function __construct(array $values = array()){
		$this->locked = false;
		$this->values = $values;
	}

	/**
	 * Configure the container using the given container configuration object(s)
	 *
	 * @param mixed $configurations
	 */
	public function configure($configurations){
		if(!is_array($configurations)){
			$configurations = array($configurations);
		}
		foreach($configurations as $configuration){
			$this->modify($configurations);
		}
	}

	/**
	 * Check whether the container is locked or not
	 *
	 * @return bool
	 */
	public function is_locked(){
		return $this->locked;
	}

	/**
	 * Lock the container so it can no longer be modified
	 *
	 */
	public function lock(){
		$this->locked = true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetExists( $offset ) {
		return array_key_exists($offset, $this->values);
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetGet( $offset ) {
		if(!array_key_exists($offset, $this->values)){
			throw new \InvalidArgumentException(sprintf('Container doens\'t have a value stored for the "%s" key.', $offset));
		}elseif(!$this->is_locked()){
			$this->lock();
		}

		return $this->values[$offset] instanceof \Closure ? $this->values[$offset]($this) : $this->values[$offset];
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetSet( $offset, $value ) {
		if($this->locked){
			throw new \RuntimeException('Container is locked and cannot be modified');
		}
		$this->values[$offset] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetUnset( $offset ) {
		if($this->locked){
			throw new \RuntimeException('Container is locked and cannot be modified');
		}
		unset($this->values[$offset]);
	}

	/**
	 * Creates a closure used for creating a service using the given callable.
	 *
	 * @param $callable
	 *
	 * @return callable
	 */
	public function service($callable){
		if(!is_object($callable) || !method_exists($callable, '__invoke')){
			throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
		}

		return function (Container $container) use ($callable){
			static $object;

			if(null === $object){
				$object = $callable($container);
			}

			return $object;
		};
	}

	/**
	 * Modify the container using the given container configuration object.
	 *
	 * @param mixed $configuration
	 */
	private function modify($configuration) {
		if(is_string($configuration)){
			$configuration = new $configuration();
		}
		if(!$configuration instanceof ContainerConfigurationInterface){
			throw new \InvalidArgumentException('Configuration object must implement the "ContainerConfigurationInterface".');
		}
		$configuration->modify($this);
	}
}
