<?php

class cn
{
    protected $con = null;
    public function __construct()
    {

        $this->con = new mysqli('localhost', 'root', '', 'inventario1');
    }

    public function consulta($sql)
    {
        return $this->con->query($sql);
    }


    public function secureSQL($strVar)
    {
        //dim banned, final, i
        $banned = array("select", "drop", "|", "'", ";", "--", "insert", "delete", "xp_");
        $vowels = $banned;
        $no = str_replace($vowels, "", $strVar);
        //Print $no;
        $final = str_replace("'", "", $no);
        //return $final;
        return $no;
    }//End Function
}

?>