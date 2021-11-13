<?php
//Function for random text ganarator
function get_random_id($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
//Function for object to array converter
function objectToArray(object $object){
    $arrayData = array();
        foreach($object as $key=>$v){
            if($key!=null && $key !=''){
                $arrayData[$key] = $v;
            }
        }
    return $arrayData;
}
//Database class
class Database{
    //Insert data into databse
    function set_data(string $databaseName,string $id,object $dataValue){
        $db_path=$_SERVER['DOCUMENT_ROOT'].'/json/'.$databaseName.'.json';
        $database = new self;
        $arrayData = array();
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/json/')){
            mkdir($_SERVER['DOCUMENT_ROOT'].'/json/');
        }
        if(file_exists($db_path)){
            foreach($database->get_data($databaseName) as $key=>$value){
                $arrayData[$key] = $value;
            }
        }
        $arrayData[$id]=$dataValue;
        $db_object_write = fopen($db_path,'w');
        fwrite($db_object_write,json_encode($arrayData));
        fclose($db_object_write);
        return true;
    }
    //Get selected data from database with key
    function get_data_with_key(string $databaseName, string $keyName){
        $db_path=$_SERVER['DOCUMENT_ROOT'].'/json/'.$databaseName.'.json';
        if(!file_exists($db_path)){
            return trigger_error('Databse not found') && false;
        }else{
        $jsonReadStream = fopen($db_path,'r');
        $db_array = json_decode(fread($jsonReadStream,filesize($db_path)));
        return objectToArray($db_array)[$keyName];
        }
    }
    //Get data into databse
    function get_data(string $databaseName){
        $db_path=$_SERVER['DOCUMENT_ROOT'].'/json/'.$databaseName.'.json';
        if(!file_exists($db_path)){
            return trigger_error('Databse not found') && false;
        }else{
        $jsonReadStream = fopen($db_path,'r');
        $db_array = json_decode(fread($jsonReadStream,filesize($db_path)));
        return $db_array;
        }
    }
    //Get data where equal to
    function get_data_where_equal(string $databaseName, string $keyName, string $value){
        $database = new self;
        $arrayData = objectToArray($database->get_data($databaseName));
        $filtered_array=array();
        foreach($arrayData as $k=>$v){
            $oa = objectToArray($v);
            if($oa[$keyName]==$value){
                $filtered_array[$k]=$v;
            }
        }
        if($filtered_array != null){
            return $filtered_array;
        }
    }
    //Push data into databse
    function push_data(string $databaseName, object $dataValue){
        $db_path=$_SERVER['DOCUMENT_ROOT'].'/json/'.$databaseName.'.json';
        $database = new self;
        $arrayData = array();
        if(file_exists($db_path)){
            foreach($database->get_data($databaseName) as $key=>$value){
                $arrayData[$key] = $value;
            }
        }
        $uid = get_random_id();
        $arrayData[$uid]=$dataValue;
        $db_object_write = fopen($db_path,'w');
        fwrite($db_object_write,json_encode($arrayData));
        fclose($db_object_write);
        return $uid;
    }
    //Delete data from database
    function delete_data(string $databaseName,string $key){
        $db_path=$_SERVER['DOCUMENT_ROOT'].'/json/'.$databaseName.'.json';
        $database = new self;
        $arrayData = array();
        if(!file_exists($db_path)){
            return false;
        }
        foreach($database->get_data($databaseName) as $k=>$value){
            if($k!=$key){
                $arrayData[$k] = $value;
            }
        }
        $db_object_write = fopen($db_path,'w');
        fwrite($db_object_write,json_encode($arrayData));
        fclose($db_object_write);
        return true;
    }
    //Search data from database
    function search_data(string $databaseName, string $text){
        $database = new self;
        define('text',$text);
        $arrayData = objectToArray($database->get_data($databaseName));
        $filtered_array=array_filter($arrayData,function($v,$k){
            $issend = false;
            foreach($v as $value){
                if (strpos(strtolower(json_encode($value)), strtolower(text))) {
                    return $v;
                }
            }
        },ARRAY_FILTER_USE_BOTH);
        if($filtered_array != null){
            return $filtered_array;
        }
    }
}
