<?php namespace Billy\Framework;

use Illuminate\Container\Container;

class Application extends Container
{
	protected static $instance;
    
    protected $configurations = [];

	public function __construct()
	{		
        static::setInstance($this);

        $this->registerDatabase();

        $this->instance(
            'app', 
            $this
        );

        $this->instance(
            'Illuminate\Container\Container', 
            $this
        );
        
        $this->instance( 
            'enqueue',
            $this->make('Billy\Framework\Enqueue')
        );

        $this->alias(
            'enqueue', 
            'Billy\Framework\Enqueue'
        );

        $this->instance( 
            'loader',
            $this->make('Billy\Framework\Loader')
        );  

        $this->alias(
            'loader', 
            'Billy\Framework\Loader'
        );
    }

	/**
     * Register the basic bindings into the container.
     * @return void
     */
    protected function registerDatabase()
    {
        return new \Billy\Framework\Database;     
    }  

	public function loadPlugin($config)
	{  
        $this->loadPluginX(
            'loader',
            array_get($config, 'loader', [])
        );  

		$this->loadPluginX(
            'enqueue',
            array_get($config, 'enqueue', [])
        ); 
	}

  	/**
     * Load all a plugin's :x.
     *
     * @param array $requires
     * @return void
     */
    protected function loadPluginX($x, $requires = [])
    {
        $container = $this;
        $$x = $this[$x];

        foreach ($requires as $require) {
            @require_once "$require";
        }
    }


	public function activatePlugin($root)
	{
		$config = $this->getPluginConfig($root);
	
 		foreach (array_get($config, 'activators', []) as $activator) {
            if (! file_exists($activator)) 
                continue;

			$this->loadWith($activator, [
				'enqueue',
                'loader'
			]);
		}

	}

    public function deactivatePlugin($root)
	{
		$config = $this->getPluginConfig($root);
	
 		foreach (array_get($config, 'dectivators', []) as $deactivator) {
            if ( ! file_exists($deactivator))
                continue;

			$this->loadWith($deactivator, [
				'enqueue',
                'loader'
			]);
		}
	}

	/**
     * Loads a file with variables in scope.
     *
     * @param  string $file
     * @param  array  $refs
     * @return void
     */
    protected function loadWith($file, $refs = [])
    {
        $container = $this;

        foreach ($refs as $ref) {
            $$ref = $this[$ref];
        }

        @require $file;
    }

	/**
     * Gets a plugin's configuration.
     *
     * @param  string $root
     * @return array
     */
    public function getPluginConfig($root)
    {
 		if ( ! isset($this->configurations[$root])) {
            $this->configurations[$root] = @require_once "$root/plugin.config.php" ?: [];
        }
        
        return $this->configurations[$root];
    }
}