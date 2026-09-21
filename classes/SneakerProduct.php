<?php
require_once "Product.php";

class SneakerProduct extends Product {
    protected $style;

    public function __construct($id, $name, $price, $category, $description, $image_url, $style) {
        parent::__construct($id, $name, $price, $category, $description, $image_url);
        $this->style = $style;
    }

    public function getStyle() {
        return $this->style;
    }

    public function setStyle($style) {
        $this->style = $style;
    }

    public function displaySneaker() {
        $this->displayProduct();
        echo "<br>Style: " . $this->getStyle();
    }
}
?>
