<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'Database.lib.php');
require_once(LIB_PATH . 'QueryBuilder.lib.php');
require_once(AETHER_PATH . 'lib/AetherSubSection.php');
require_once(AETHER_PATH . 'lib/AetherTextResponse.php');

/**
 * 
 * I_AM_TOO_LAZY_TO_WRITE_A_DESCRIPTION
 * 
 * Created: 2007-02-02
 * @author Raymond Julin
 * @package aether.sections.priceguide
 */

class AetherSectionPriceguideFrontpage extends AetherSubSection {
    
    /**
     * Render and return response
     *
     * @access public
     * @return AetherResponse
     */
    public function response() {
        $tpl = $this->sl->getTemplate(96);

        $tpl->selectTemplate('categories');
        //$tpl->setVar('categories', $this->getCategories);
        $content = $tpl->returnPage();

        $tpl->selectTemplate('ad200');
        $content .= $tpl->returnPage();

        $tpl->selectTemplate('ourReviews');
        //$tpl->setVar('ourReviews', $this->getOurReviews);
        $content .= $tpl->returnPage();

        $tpl->selectTemplate('userReviews');
        $tpl->setVar('userReviews', $this->getUserReviews());
        $content .= $tpl->returnPage();

        $tpl->selectTemplate('userLists');
        //$tpl->setVar('userLists', $userLists);
        $content .= $tpl->returnPage();

        return new AetherTextResponse($content);
    }

    /**
     * Grab the latest published user reviews
     *
     * @param category The category we want reviews from. Defaults to all
     * @access private
     * @return array
     */
    private function getUserReviews($category = false) {
        $db = new Database('prisguide');
        $qb = new QueryBuilder;

        $qb->addFrom('erfaringer_data', 'e');
        $qb->addFrom('products', 'p');
        $qb->addFrom('category_site_link', 's');
        $qb->addFrom('product_category_link', 'pcl');
        $qb->addFrom('categories_lang', 'c');
        $qb->addFrom('manufacturers', 'm');

        $qb->addSelect('p.product', 'productName');
        $qb->addSelect('p.product_id', 'productId');
        $qb->addSelect('pcl.cat_id', 'catId');
        $qb->addSelect('c.category_name', 'categoryName');
        $qb->addSelect('e.id');
        $qb->addSelect('e.totalkarakter', 'rating');
        $qb->addSelect('e.overskrift', 'title');
        $qb->addSelect('m.manufacturer_name', 'manufacturerName');

        if ($category > 0) {
            $qb->addWhere('c.cat_id', '=', $category, "AND", NO_ESCAPE);
        }

        $qb->addWhere('p.manufacturer_id', '=', 'm.manufacturer_id', "AND", NO_ESCAPE);
        $qb->addWhere('pcl.product_id', '=', 'e.product_id', "AND", NO_ESCAPE);
        $qb->addWhere('e.product_id', '=', 'p.product_id', "AND", NO_ESCAPE);
        $qb->addWhere('pcl.product_id', '=', 'p.product_id', "AND", NO_ESCAPE);
        $qb->addWhere('s.cat_id', '=', 'pcl.cat_id', "AND", NO_ESCAPE);
        $qb->addWhere('c.cat_id', '=', 'pcl.cat_id', "AND", NO_ESCAPE);
        // TODO Don't hardcore lang_id
        $qb->addWhere('c.lang_id', '=', 83, "AND", NO_ESCAPE);
        $qb->addWhere('e.active', '=', 'true');

        $qb->addGroupBy('e.id');
        $qb->addOrder('e.datetime', 'DESC');
        $qb->setLimit(5);

        return $db->query($qb->build());
    }
}

?>
