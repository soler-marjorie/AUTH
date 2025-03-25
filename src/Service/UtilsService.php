<?php

namespace App\Service;

class UtilsService{

    public function encodeBase64(mixed $value){
        if(empty($value)){
            throw new \Exception("La valeur à encoder ne peut pas être vide");
        }
        return base64_encode($value);
    }

    public function decodeBase64(mixed $value){
        if(empty($value)){
            throw new \Exception("La valeur à décoder ne peut pas être vide");
        }
        return base64_decode($value);
    }
}