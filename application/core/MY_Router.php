<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Router Class
 *
 * @author      Damien K.
 * @autor 		Jason, for _set_request($segments) method
 */
 
class MY_Router extends CI_Router {
    
	public function __construct()
	{
		parent::__construct();
	}
	
	/*
	 *	Update segments function to allow hypens in url
	 *
	 *	http://ianluckraft.co.uk/2011/01/codeigniter-allow-hyphen-in-url/
	 *
	 */	
	public function _set_request($segments)
	{
		
		// Fix only the first 2 segments
		for($i = 0; $i < 5; ++$i){
			
			if(isset($segments[$i])){
				
				$segments[$i] = str_replace('-', '_', $segments[$i]);
				
			}
		}
			
		// Run the original _set_request method, passing it our updated segments.
		parent::_set_request($segments);
			
	}
	// --------------------------------------------------------------------

    /**
     * OVERRIDE
     *
     * Validates the supplied segments.  Attempts to determine the path to
     * the controller.
     *
     * @access    private
     * @param    array
     * @return    array
     */
    function _validate_request($segments)
    {
        if (count($segments) == 0)
        {
            return $segments;
        }

        // Does the requested controller exist in the root folder?
        if (file_exists(APPPATH.'controllers/'.$segments[0].EXT))
        {
            return $segments;
        }

        // Is the controller in a sub-folder?
        if (is_dir(APPPATH.'controllers/'.$segments[0]))
        {
            // @edit: Support multi-level sub-folders
            $dir = '';
            do
            {
                if (strlen($dir) > 0)
                {
                    $dir .= '/';
                }
                $dir .= $segments[0];
                $segments = array_slice($segments, 1);
            } while (count($segments) > 0 && is_dir(APPPATH.'controllers/'.$dir.'/'.$segments[0]));
            // Set the directory and remove it from the segment array
            $this->set_directory($dir);
            // @edit: END

            // @edit: If no controller found, use 'default_controller' as defined in 'config/routes.php'
            if (count($segments) > 0 && ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].EXT))
            {
                array_unshift($segments, $this->default_controller);
            }
            // @edit: END

            if (count($segments) > 0)
            {
                // Does the requested controller exist in the sub-folder?
                if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].EXT))
                {
                    // show_404($this->fetch_directory().$segments[0]);
                    // @edit: Fix a "bug" where show_404 is called before all the core classes are loaded
                    $this->directory = '';
                    // @edit: END
                }
            }
            else
            {
                // Is the method being specified in the route?
                if (strpos($this->default_controller, '/') !== FALSE)
                {
                    $x = explode('/', $this->default_controller);

                    $this->set_class($x[0]);
                    $this->set_method($x[1]);
                }
                else
                {
                    $this->set_class($this->default_controller);
                    $this->set_method('index');
                }

                // Does the default controller exist in the sub-folder?
                if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.EXT))
                {
                    $this->directory = '';
                    return array();
                }

            }

            return $segments;
        }


        // If we've gotten this far it means that the URI does not correlate to a valid
        // controller class.  We will now see if there is an override
        if (!empty($this->routes['404_override']))
        {
            $x = explode('/', $this->routes['404_override']);

            $this->set_class($x[0]);
            $this->set_method(isset($x[1]) ? $x[1] : 'index');

            return $x;
        }


        // Nothing else to do at this point but show a 404
        show_404($segments[0]);
    }

    // --------------------------------------------------------------------

    /**
     * OVERRIDE
     *
     * Set the directory name
     *
     * @access  public
     * @param	string
     * @return  void
     */
    public function set_directory($dir)
    {
        $this->directory = str_replace(array('.'), '', $dir).'/'; // @edit: Preserve '/'
    }

}

/* End of file MY_Router.php */
/* Location: ./application/core/MY_Router.php */