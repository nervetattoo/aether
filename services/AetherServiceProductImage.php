<?php // vim:set tabstop=4 shiftwidth=4 smarttab expandtab:

/**
 * 
 * Dumps out the product image and url as xml for a productId
 * 
 * Created: 2007-05-09
 * @author Simen Graaten
 * @package themepage.aether
 */

require_once("/home/lib/libDefines.lib.php");
require_once(LIB_PATH . "product/Product.lib.php");

class AetherServiceProductImage extends AetherService {
    
    public function render() {
        $productId = $_GET['productId'];
        $productBaseUrl = $_GET['productBaseUrl'];
        $imageBaseUrl = $_GET['imageBaseUrl'];
        $conatinerId = $_GET['containerId'];

        $p = new Product($productId);
        $image = $p->images[0];

        $productName = "Ukjent produkt";
        $productUrl = "#";
        $imageUrl = "#";

        if ($image) {
            $productUrl = $productBaseUrl . "product.php?productId=" . $productId;

            foreach ($image->imageVersions as $v) {
                if ($v->imageFormatId == 3) {
                    $imageUrl = $imageBaseUrl . $v->imageUrl;
                    $productName = $p->productName;
                }
            }
        }

        header("Content-Type: text/xml");
        return array(
            "return" => array(
                "imageUrl" => $imageUrl, 
                "productName" => $productName, 
                "productUrl" => $productUrl,
                "containerId" => $conatinerId
            )
        );
    }
}
?>
