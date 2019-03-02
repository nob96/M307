<?php
error_reporting(0);
require_once 'MySQL.php';

route();

/** Ermittelt Aktion anhand action-Feld im request-Array
 * 
 */
function route(){
    switch ($_REQUEST['action']){
        case 'delete':
            delete();
            break;
        case 'get':
            get();
            break;
        case 'set':
            set();
            break;
        case 'update':
            update();
            break;
        default:
            break;
    }
}

/** Löscht datensatz anhand id und gibt ergebnis in json-string aus.
 * 
 */
function delete(){
    $mysqlObj = new MySQL();
    $strSQLWhereStatement = getSQLWhereIdStatement();

    $mysqlObj->setQuery("DELETE FROM `noah_inventar` WHERE ".$mysqlObj->escape($strSQLWhereStatement));
    $mysqlObj->execute();

    buildReturnMsg($mysqlObj->validate(), 'Delete', $mysqlObj->getErrorMsg());
}

/** Holt Datensatz anhand id und gibt json-string aus.
 * 
 */
function get(){
    $mysqlObj = new MySQL();
    $strSQLWhereStatement = getSQLWhereIdStatement();

    $mysqlObj->setQuery('SELECT * FROM `noah_inventar` WHERE '.$mysqlObj->escape($strSQLWhereStatement));
    $mysqlObj->execute();

    if($mysqlObj->validate()){
        echo json_encode($mysqlObj->getDataArr());
    }else{
        buildReturnMsg($mysqlObj->validate(), 'Get', $mysqlObj->getErrorMsg());
    }
}

/** Schreibt einen neuen Datensatz in db und gibt Ergebnis als json string aus.
 * 
 */
function set(){
    $mysqlObj = new MySQL();
    $validateArr = validateForm();

    if($validateArr['return']){
        $mysqlObj->setQuery('INSERT INTO `noah_inventar`
                            (
                                `inventar_Geraetename`,
                                inventar_Inventarnummer,
                                inventar_Kategorie,
                                inventar_Kaufdatum,
                                inventar_Bemerkung
                            )
                            VALUES
                            (
                                "'.trim($mysqlObj->escape($_REQUEST['inventar_Geraetename-input'])).'",
                                "'.trim($mysqlObj->escape($_REQUEST['inventar_Inventarnummer-input'])).'",
                                "'.trim($mysqlObj->escape($_REQUEST['inventar_Kategorie-input'])).'",
                                "'.trim($mysqlObj->escape($_REQUEST['inventar_Kaufdatum-input'])).'",
                                "'.trim($mysqlObj->escape($_REQUEST['inventar_Bemerkung-input'])).'"                      
                                )
                            ');
                            
        $mysqlObj->execute();        
        buildReturnMsg($mysqlObj->validate(), 'Add', $mysqlObj->getErrorMsg());
    }else{
        echo json_encode($validateArr);
    }
}

/** Verändert einen bestehenden Datensatz und gibt ergebnis als json string aus.
 * 
 */
function update(){
    $mysqlObj = new MySQL();
    $validateArr = validateForm();
    $strSQLWhereStatement = getSQLWhereIdStatement();

    if($validateArr['return']){
        $mysqlObj->setQuery('UPDATE `noah_inventar`
                             SET 
                                 `inventar_Geraetename` = "'.trim($mysqlObj->escape($_REQUEST['inventar_Geraetename-input'])).'",
                                 `inventar_Inventarnummer` = "'.trim($mysqlObj->escape($_REQUEST['inventar_Inventarnummer-input'])).'",
                                 `inventar_Kategorie` = "'.trim($mysqlObj->escape($_REQUEST['inventar_Kategorie-input'])).'",
                                 `inventar_Kaufdatum` = "'.trim($mysqlObj->escape($_REQUEST['inventar_Kaufdatum-input'])).'",
                                 `inventar_Bemerkung` = "'.trim($mysqlObj->escape($_REQUEST['inventar_Bemerkung-input'])).'"
                                 WHERE '.$strSQLWhereStatement.'
                            ');

        $mysqlObj->execute();
        buildReturnMsg($mysqlObj->validate(), 'Update', $mysqlObj->getErrorMsg());
    }else{
        //Sonst Formular-Rückmeldung ausgeben
        echo json_encode($validateArr);
    }
}

/**
 * Wenn id gesetzt, dann id="id-input", sonst 1=1
 * 
 * @return string
 */
function getSQLWhereIdStatement(){
    return (null !== $_REQUEST['Id-input'] and strlen($_REQUEST['Id-input'] > 0))  ? "Id =".$_REQUEST['Id-input'] : "1=1"; 
}

/**
 * Validiert nummer/email/text
 * 
 * @return bool
 */
function validateInput($strValue, $strType){
    switch ($strType){
        case 'email':
            return (strlen($strValue) > 0 and filter_var($strValue, FILTER_VALIDATE_EMAIL) and isHTML($strValue) === 0);
        case 'text':
            return (strlen($strValue) > 0 and isset($strValue) and isHTML($strValue) === 0);
        case 'color':
            return (isset($strValue));
        case 'number':
            return (is_numeric($strValue));
    }
}

/**
 * Validiert alle inputs des formulars. Das Ergebnis wird in returnArr geschrieben und zurückgegeben.
 * 
 * @return array()
 */
function validateForm(){
    $returnArr = array();
    
    if(!validateInput($_REQUEST['inventar_Geraetename-input'], 'text')){
        $returnArr['msg'] = 'Gerätename konnte nicht validiert werden!';
        $returnArr['bcolor'] = 'coral';
        $returnArr['return'] = false;
    }elseif(!validateInput($_REQUEST['inventar_Inventarnummer-input'], 'text')){
        $returnArr['msg'] = 'Inventarnummer konnte nicht validiert werden!';
        $returnArr['bcolor'] = 'coral';
        $returnArr['return'] = false;
    }elseif(!validateInput($_REQUEST['inventar_Kategorie-input'], 'text')){
        $returnArr['msg'] = 'Kategorie konnte nicht validiert werden!';
        $returnArr['bcolor'] = 'coral';
        $returnArr['return'] = false;
    }elseif(strlen($_REQUEST['inventar_Kaufdatum-input']) > 0 && !validateInput($_REQUEST['inventar_Kaufdatum-input'], 'text')){ //Input Kaufdatum nur validieren wenn überhaupt abgeschickt wurde!
        $returnArr['msg'] = 'Kaufdatum konnte nicht validiert werden!';
        $returnArr['bcolor'] = 'coral';
        $returnArr['return'] = false;
    }elseif(strlen($_REQUEST['inventar_Bemerkung-input']) > 0  && !validateInput($_REQUEST['inventar_Bemerkung-input'], 'text')){ //Input Bemerkung nur validieren wenn überhaupt abgeschickt wurde!
        $returnArr['msg'] = 'Bemerkung konnte nicht validiert werden!';
        $returnArr['bcolor'] = 'coral';
        $returnArr['return'] = false; 
    }else{
        $returnArr['return'] = true;
    }

    return $returnArr;
}

/**
 * Prüft ob HTML im String vorhanden ist. Achtung: preg_match gibt FALSE zurück wenn ein Fehler auftrat, 0 wenn nichts gefunden wurde!
 * 
 * @return bool
 */
function isHTML($strString){
    return preg_match("/<(.*)>/", $strString);
}

function buildReturnMsg($blnValidate, $strOperation, $strMsg){
    $returnArr = array();

    if($blnValidate){
        $returnArr['msg'] = $strOperation.' success.';
        $returnArr['bcolor'] = 'lightgreen';
        $returnArr['return'] = $blnValidate;
    }else{
        $returnArr['msg'] = $strOperation.' failed. MySQL: '.$strMsg;
        $returnArr['bcolor'] = 'coral';
        $returnArr['return'] = $blnValidate;
    }

    echo json_encode($returnArr);
}