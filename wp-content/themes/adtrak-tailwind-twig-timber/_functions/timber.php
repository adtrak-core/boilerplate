<?php 

  $autoload = dirname(__DIR__) . '/vendor/autoload.php';
  if( file_exists( $autoload ) ) :
    require_once( $autoload );
    new Timber\Timber();
  endif;

  if( !class_exists( 'Timber' ) ) :
    add_action( 'admin_notices', function() {
      echo "<div class=\"error\"><p>Timber does not appear to be available. Check composer install</p></div>";
    });
  endif;

  // Some Timber setup
  Timber::$dirname = array( '_views', '_components' );

  class newSite extends Timber\Site {
    
    /** Add timber support. */
    public function __construct() {
      add_action( 'after_setup_theme', array( $this, 'themeSupports' ) );
      add_filter( 'timber/context', array( $this, 'addToContext' ) );
      parent::__construct();
      $this->timberRoutes();
    }

    function add_to_twig( $twig ) {
        // Adding a function.
        $twig->addFunction( new Timber\Twig_Function( 'address_stacked', 'address_stacked' ) );        
        return $twig;
    }
    
    /** This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    public function addToContext( $context )
    {
      // Menus
      $context['site'] = $this;
      $context['options'] = get_fields('option');
      $context['primaryMenu'] = new Timber\Menu('Primary Menu');
      // Check if the secondary nav menu location has a menu assigned to it
      if( has_nav_menu( 'secondary' ) ) {
        $context['secondaryMenu'] = new Timber\Menu('Secondary Menu');
      }
      $context['footerMenu'] = new Timber\Menu('Footer Menu');
      return $context;
    }

    public function themeSupports() {
      add_theme_support( 'post-thumbnails' );
      add_theme_support( 'menus' );
    }	
  }

new newSite();
