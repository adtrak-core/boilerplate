<?php 

namespace Billy\Framework;

use Exception;
use InvalidArgumentException;

class Loader
{
	protected $namespace = null;

	protected function add($data) 
	{
		if(!isset($data['priority'])) {
			$data['priority'] = 10;
		}

		if(!isset($data['args'])) {
			$data['args'] = 1;
		}

  		foreach (['method', 'uses'] as $key) {
            if (isset($data[$key])) {
                continue;
            }
            throw new InvalidArgumentException("Missing {$key} definition for action");
        }

  		if (!in_array($data['type'], ['action', 'filter'])) {
            throw new InvalidArgumentException("Unknown action type '{$data['type']}'");
        }

        switch ($data['type']) {
            case 'filter':
                add_filter($data['method'], $data['uses'], $data['priority'], $data['args']);
                break;
            case 'action':
                add_action($data['method'], $data['uses'], $data['priority'], $data['args']);
                break;
            default:
                break;
        }
	}

    public function filter($data, $type = null)
    {
	    if (is_null($type)) {
			$data['type'] = 'filter';
		}

        $this->add($data);
    }

	public function action($data, $type = null)
	{
	    if (is_null($type)) {
			$data['type'] = 'action';
		}

        $this->add($data);
	}

	/**
     * Sets the current namespace.
     *
     * @param  string $namespace
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Unsets the current namespace.
     *
     * @return void
     */
    public function unsetNamespace()
    {
        $this->namespace = null;
    }
}