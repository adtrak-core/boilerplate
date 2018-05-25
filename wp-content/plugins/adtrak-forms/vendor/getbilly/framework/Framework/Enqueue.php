<?php

namespace Billy\Framework;

class Enqueue {

    /**
     * All the filters.
     * @var array
     */
    protected static $filters = [
        'hook',
        'page',
        'post',
        'category',
        'archive',
        'search',
        'postType'
    ];

    /**
     * Checks if the enqueue passes any filters applied,
     * then appends the site url to the path before calling
     * wp_enqueue_style or wp_enqueue_script depending on there
     * extension
     *
     * @param $attrs
     * @param $footer
     */
    public function buildInclude($attrs, $footer)
    {
        if (isset($attrs['filter']) && !empty($attrs['filter'])) {

			$filterBy = key($attrs['filter']);
            $filterWith = reset($attrs['filter']);

            if (!is_array($filterWith)) {
                $filterWith = [$filterWith];
            }

            if (!$this->filterBy($filterBy, $attrs, $filterWith))
				return;
        }

        if (!isset($attrs['uses']))
            $attrs['uses'] = [];

        if (pathinfo($attrs['src'], PATHINFO_EXTENSION) === 'css') {
            wp_enqueue_style($attrs['as'], $attrs['src']);
        } else {
            wp_enqueue_script($attrs['as'], $attrs['src'], $attrs['uses'], false, $footer);

            if(isset($attrs['localize'])) {
                wp_localize_script($attrs['as'], $attrs['as'], $attrs['localize']);
            }
        }
    }

    /**
     * Filters by a specific filter.
     *
     * @param $by
     * @param $attrs
     * @param $with
     * @return bool
     */
    protected function filterBy($by, $attrs, $with)
    {
        $method = 'filter' . ucfirst($by);

        if (!method_exists($this, $method))
            return false;

        return $this->{$method}($attrs, $with);
    }

    /**
     * Adds the enqueue to add_action related to admin pages
     * @param        $attrs
     * @param string $footer
     */
    public function admin($attrs, $footer = 'header')
    {
        add_action('admin_enqueue_scripts', function ($hook) use ($attrs, $footer)
		{
            $attrs['hook'] = $hook;
            $this->buildInclude($attrs, $this->setFooterFlag($footer));
        });
    }

    /**
     * Adds the enqueue to add_action related to login pages
     *
     * @param        $attrs
     * @param string $footer
     */
    public function login($attrs, $footer = 'header')
    {
        add_action('login_enqueue_scripts', function () use ($attrs, $footer)
        {
            $this->buildInclude($attrs, $this->setFooterFlag($footer));
        });
    }

    /**
     * Adds the enqueue to add_action related to front pages
     *
     * @param        $attrs
     * @param string $footer
     */
    public function front($attrs, $footer = 'header')
    {
        add_action('wp_enqueue_scripts', function () use ($attrs, $footer)
        {
            $this->buildInclude($attrs, $this->setFooterFlag($footer));
        });
    }

    /**
     * Checks if footer flag is set.
     *
     * @param string $footer
     * @return bool
     */
    protected function setFooterFlag($footer)
    {
        return $footer === 'footer';
    }
}
