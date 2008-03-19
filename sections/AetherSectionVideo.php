<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'video/Video.lib.php');

/**
 * 
 * Entry point to all priceguide applicatino sections
 * 
 * Created: 2007-02-02
 * @author Raymond Julin
 * @package aether.sections
 */

class AetherSectionVideo extends AetherSection {
    
    /**
     * Return response
     *
     * @access public
     * @return AetherResponse
     */
    public function response() {
        $config = $this->sl->fetchCustomObject('aetherConfig');

        try {
            $videoId = $config->getUrlVariable('videoId');
        }
        catch (Exception $e) {} // Do nothing, it only means a specific video was not chosen

        $video = new Video($videoId);
        if ($video->isPublished()) {
            $video->countView();
            $this->sl->saveCustomObject('video', $video);
        }

        $this->generateMetaInfo($video);

        return new AetherTextResponse($this->renderModules());
    }
    
    /**
     * Generate meta info for video
     *
     * @access private
     * @return void
     * @param Object $video
     */
    private function generateMetaInfo($video) {
        // Build keyword list to comma separated list
        $tags = $video->keywords->getTags();
        $keywords = join(', ', $tags);
        $keywords = strip_tags($metaKeywords);
        $keywords = htmlentities($metaKeywords, ENT_QUOTES, "ISO-8859-1");

        $meta = $this->sl->getVector('metaData');
        $meta['title'] = $video->videoTitle;
        $meta['keywords'] = $keywords;
        $meta['description'] = $video->videoTeaser;
    }
}
?>
