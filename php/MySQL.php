<?php
/** Regelt db-Zugriffe und db-Errors
 *  
 */
class MySQL {
    private $mysqliObj;
    private $dataArr;
    private $stringQuery;

    /** MySQL constructor. Instanziert mysqli-Objekt und baut Verbindung zur db auf
     *
     */
    public function __construct() {
        try{
            $this->mysqliObj = new mysqli('localhost', 'root', '', 'm307_noah', 3306);

            if($this->mysqliObj->connect_errno > 0){
                $this->createDB('m307_noah', 'noah_inventar');
                //Erstelle mysqli-Instanz erneut nachdem db erstellt wurde...
                $this->mysqliObj = new mysqli('localhost', 'root', '', 'm307_noah', 3306);
            }
        }catch (mysqli_sql_exception $mysqliExObj){
            echo('SQL-Connect error: '.$this->mysqliObj->connect_error);
        }
    }

    /** Führt einen Query aus
     *
     */
    public function execute(){
        $tmpArr = array();
        $resultObj = isset($this->stringQuery) ? $this->mysqliObj->query($this->stringQuery) : false;

        if(is_object($resultObj) and $resultObj->num_rows >= 1) {
            while ($arrRow = $resultObj->fetch_assoc()) {
                $tmpArr[] = $arrRow;
            }
            $this->dataArr = $tmpArr;
        }else{
            $this->dataArr = null;
        }
    }

    /** Prüft ob Query erfolgreich ausgeführt wurde
     *
     * @return bool
     */
    public function validate(){
        if(($this->mysqliObj->affected_rows != -1 and strlen($this->mysqliObj->error) <= 0)){
            return true;
        }

        return false;
    }

    /** 
     * @return mixed
     */
    public function getErrorMsg(){
        return $this->mysqliObj->error;
    }

    /** Escapt einen string
     *
     * @param $stringString
     * @return string
     */
    public function escape($stringString){
        return $this->mysqliObj->real_escape_string($stringString);
    }

    /**
     * @return mixed
     */
    public function getDataArr(){
        return $this->dataArr;
    }

    /**
     * @param $stringQuery
     */
    public function setQuery($stringQuery){
        $this->stringQuery = $stringQuery;
    }

    /**
     * @return mixed
     */
    public function getQuery(){
        return $this->stringQuery;
    }

    /** Erstellt Datenbank strDatabase und Table strTable und fügt einen Datensatz ein
     * 
     */
    private function createDB($strDatabase, $strTable){
        $mysqliObj = new mysqli('localhost', 'root', '', null, 3306);
        $mysqliObj->query('CREATE DATABASE IF NOT EXISTS '.$strDatabase.' CHARACTER SET UTF8 collate utf8_general_ci');
        $mysqliObj->query('USE '.$strDatabase.'');
        $mysqliObj->query('CREATE TABLE IF NOT EXISTS '.$strTable.' (
            Id INT PRIMARY KEY AUTO_INCREMENT,
            `inventar_Geraetename` VARCHAR(40) NOT NULL,
            inventar_Inventarnummer VARCHAR(40) NOT NULL,
            inventar_Kategorie ENUM("Computer", "Audio", "Monitor") NOT NULL DEFAULT "Computer",
            inventar_Kaufdatum DATE DEFAULT NULL,
            inventar_Bemerkung TEXT
          )');

        $mysqliObj->query('INSERT INTO `noah_inventar`
        (
            `inventar_Geraetename`,
            inventar_Inventarnummer,
            inventar_Kategorie,
            inventar_Kaufdatum,
            inventar_Bemerkung
        )
        VALUES
        (
           "Apple MacBook Air 13.3",
           "KL156",
           "Computer",
           "2016-01-01",
           "Bemerkung"
            )
        ');
    }
}