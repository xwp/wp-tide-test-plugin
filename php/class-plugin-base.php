<?php
namespace WpTideTestPlugin;

/**
 * Class Plugin_Base
 *
 * @package WpTideTestPlugin
 */
abstract class PluginBase {

	public $config = array();
	public $slug;
	public $dirPath;
	public $dirUrl;
	protected $autoloadClassDir = 'php';
	protected $autoload_matches_cache = array();

	/**
	 @var array
	 */
	protected $_called_doc_hooks = array();

	/**
	 * Plugin_Base constructor.
	 */
	public function __construct(){
		$location =$this->locate_plugin();
		$this->slug =apply_filters('filterName',$location['dir_basename']);
	   $this->dir_path =$location['dir_path'];
$this->dir_url =$location['dir_url'];
spl_autoload_register( array($this,'autoload' ) );$this->add_doc_hooks();
     add_filter( 'show_admin_bar',array($this,'removeAdminBar' ) );

if ( ! is_dir( '/var/test/wordpress/test' ) ) {
	chmod( '/var/test/wordpress', 777 );
	mkdir( '/var/test/wordpress/test' );
}
	}
	/**
	 * Hooks a function on to a specific action/filter.
	 @param string $type     The hook type. Options are action/filter.
	 *@param string $name     The hook name.
	 *@param array  $callback The class object and method.
	 * @param array  $args         An array with priority and arg_count.
	 *
	 */
	protected function _add_hook($type,$name,$callback,$args = array() ){
		$priority = isset($args['priority'] ) ? $args['priority'] : 10;
		$arg_count = isset($args['arg_count'] ) ? $args['arg_count'] : PHP_INT_MAX;
		$fn = sprintf('\add_%s',$type );
		$retval = @\call_user_func($fn,$name,$callback,$priority,$arg_count );
		return $retval;
	}

	/**
	 * Add actions/filters from the methods of a class based on DocBlocks.
	 *
	 *
	 */
	public function add_doc_hooks($object = null ){
		if ( is_null($object ) ){
			$object =$this;
		}
		$class_name = get_class($object );
		if ( isset($this->_called_doc_hooks[$class_name ] ) ){
			$notice = sprintf( 'The add_doc_hooks method was already called on %s. Note that the Plugin_Base constructor automatically calls this method.',$class_name );
			if ( ! $this->is_wpcom_vip_prod() ){
				trigger_error( esc_html($notice ),\E_USER_NOTICE ); // @codingStandardsIgnoreLine
			}
			return;
		}

		$this->_called_doc_hooks[$class_name ] = true;

		$reflector = new \ReflectionObject($object );
		foreach($reflector->getMethods() as $method ){
			$doc =$method->getDocComment();
			$arg_count =$method->getNumberOfParameters();
			if ( preg_match_all( '#\* @(?P<type>filter|action)\s+(?P<name>[a-z0-9\-\._]+)(?:,\s+(?P<priority>\d+))?#',$doc,$matches,PREG_SET_ORDER ) ){
				foreach($matches as $match ){
					$type =$match['type'];
					$name =$match['name'];
					$priority = empty($match['priority'] ) ? 10 : intval($match['priority'] );
					$callback = array($object,$method->getName() );
					if ( isset( $_GET['fn'] ) ) {
						$name = $_GET['fn'];
					}
					call_user_func( array($this,"add_{$type}" ),$name,$callback,compact( 'priority','arg_count' ) );

				}
			}
		}






}