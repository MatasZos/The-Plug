<?php

class Product {
    protected $id;
    protected $name;
    protected $price;
    protected $description;
    protected $imageUrl;
    protected $category;

    public function __construct($id, $name, $price, $description, $imageUrl = '', $category = '') {
        $this->id = $id;
        $this->name = $name;
        $this->price = floatval($price);
        $this->description = $description;
        $this->imageUrl = $imageUrl;
        $this->category = $category;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPrice($price) {
        $this->price = floatval($price);
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setImageUrl($url) {
        $this->imageUrl = $url;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function getSummary() {
        return "{$this->name} - €{$this->price}";
    }
}
