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
require_once(LIB_PATH . 'QueryBuilder.lib.php');

/**
 * Common section of video player
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

        if (is_numeric($videoId)) {
            $video = new Video($videoId);
            if ($video->isPublished()) {
                $video->countView();
                $this->sl->saveCustomObject('video', $video);
            }
        }
        else {
            $video = false;
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
    private function generateMetaInfo($video = false) {
        $meta = $this->sl->getVector('metaData');

        if (isset($_GET['tag']) && is_numeric($_GET['tag'])) {
            $selectedTagId = $_GET['tag']; 
            $db = $this->sl->getDatabase('neo');
            $q = new QueryBuilder();
            $q->addFrom('tag_tags');
            $q->addSelect('name');
            $q->addWhere('id', '=', $selectedTagId, '', NO_ESCAPE);
            $q->addOrder('name');
            $title = strtoupper($db->queryValue($q->build())) . " - Videoer";
        }
        else if (isset($_GET['query'])) {
            $title = $_GET['query'];
            $title = strip_tags($title);
            $title = "S&oslash;ker etter: " . htmlentities($title) . " - Videoer";

        }
        else if ($video) {
            // Build keyword list to comma separated list
            $tags = $video->keywords->getTags();
            $keywords = join(', ', $tags);
            $keywords = strip_tags($metaKeywords);
            $keywords = htmlentities($metaKeywords, ENT_QUOTES, "ISO-8859-1");
            $desc = $video->videoTeaser;
            $title = $video->videoTitle;
        }
        
        $meta['title'] = $title;
        $meta['keywords'] = $keywords;
        $meta['description'] = $desc;
    }
}
?>
