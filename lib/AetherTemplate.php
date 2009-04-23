<?php // 
/**
 * 
 * Super class for templating interface of Aether
 * 
 * Created: 2009-04-23
 * @author Raymond Julin
 * @package aether
 */
abstract class AetherTemplate {
    
    /**
     * Return template object for selected engine
     *
     * @return AetherTemplate
     * @param string $engine Name of engine to use
     */
    public static function get($engine) {
        if ($engine == 'smarty') {
            $class = 'AetherTemplateSmarty';
            require_once(AETHER_PATH . 'lib/templating/AetherTemplateSmarty.php');
        }
        else {
            // Default template engine
            $class = 'AetherTemplateSmarty';
            require_once(AETHER_PATH . 'lib/templating/AetherTemplateSmarty.php');
        }
        return new $class;
    }
    
    /**
     * Set a template variable 
     *
     * @return void
     * @param string $key
     * @param mixed $value
     */
    abstract public function set($key, $value);
}
?>
