<?php // vim:set tabstop=4 shiftwidth=4 smarttab expandtab:

/**
 * 
 * Base class definition for aether services
 * 
 * Created: 2007-04-30
 * @author Raymond Julin
 * @package aether.lib
 */

abstract class AetherService {
    
    /**
     * Service locator
     * @var AetherServiceLocator
     */
    protected $sl = null;
    
    /**
     * Specific options for this service
     * @var array
     */
    protected $options = array();
    
    /**
     * What type of output should this service return
     * @var string
     */
    protected $type = 'xml';
    
    /**
     * Constructor. Accept service locator
     *
     * @access public
     * @return AetherService
     * @param AetherServiceLocator $sl
     * @param array $options
     */
    public function __construct(AetherServiceLocator $sl, $options=array()) {
        $this->sl = $sl;
        $this->options = $options;
    }
    
    /**
     * Render services.
     *
     * @access public
     * @return string
     */
    abstract public function render();
    
    /**
     * Render to xml
     *
     * @access public
     * @return string
     * @param array $data
     */
    public function __toXml($data, $document=false) {
        if (!$document) {
            $document = new DOMDocument('1.0', 'UTF-8');
        }
        foreach ($data as $key => $val) {
            if (is_numeric($key))
                $key = 'item';
            $tmp = $document->createElement($key);
            if (is_array($val)) {
                $tmp->appendChild($this->__toXml($val, $document));
            }
            else {
                $tmp->appendChild($document->createTextNode($val));
            }
            $document->appendChild($tmp);
        }
        return $document;
    }
    
    /**
     * Pack data to format as requested for this service
     *
     * @access public
     * @return mixed
     * @param mixed $data
     */
    public function pack($data) {
        switch ($this->type) {
            case 'xml':
            default:
                $document = $this->__toXml($data);
                return $document->saveXML();
                break;
        }
    }
}
?>
