<?php
require_once "BaseService.php";
require_once __DIR__ . "/../dao/PartyDao.php";

class PartyService extends BaseService{
    public function __construct(){
        parent::__construct(new PartyDao);
    }
}