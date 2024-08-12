<?php
class Holiday {
    private $name;
    private $description;
    private $date;
    private $endDate;
    private $type;
    private $staysSame;

    // Constructor
    public function __construct($name, $description = null, $date, $endDate = null, $type, $staysSame) {
        $this->name = $name;
        $this->description = $description;
        $this->date = $date;
        $this->endDate = $endDate;
        $this->type = $type;
        $this->staysSame = $staysSame;
    }

    // Getters and Setters
    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
    }

    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDate() {
        return $this->date;
    }
    public function setDate($date) {
        $this->date = $date;
    }

    public function getEndDate() {
        return $this->endDate;
    }
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    public function getType() {
        return $this->type;
    }
    public function setType($type) {
        $this->type = $type;
    }

    public function getStaysSame() {
        return $this->staysSame;
    }
    public function setStaysSame($staysSame) {
        $this->staysSame = $staysSame;
    }
}

class NameDay {
    private $name;
    private $date;
    private $listNames;
    private $staysSame;

    // Constructor
    public function __construct($name, $date, $listNames, $staysSame) {
        $this->name = $name;
        $this->date = $date;
        $this->listNames = $listNames;
        $this->staysSame = $staysSame;
    }

    // Getters and Setters
    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
    }

    public function getDate() {
        return $this->date;
    }
    public function setDate($date) {
        $this->date = $date;
    }

    public function getListNames() {
        return $this->listNames;
    }
    public function setListNames($listNames) {
        $this->listNames = $listNames;
    }

    public function getStaysSame() {
        return $this->staysSame;
    }
    public function setStaysSame($staysSame) {
        $this->staysSame = $staysSame;
    }
}
