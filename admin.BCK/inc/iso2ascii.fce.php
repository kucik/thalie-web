<?php
	function iso2ascii($return)

	{
    $trans = array(
      "á" => "a",
      "č" => "c",
      "ď" => "d",
      "é" => "e",
      "ě" => "e",
      "í" => "i",
      "ľ" => "l",
      "ň" => "n",
      "ó" => "o",
      "ř" => "r",
      "š" => "s",
      "ť" => "t",
      "ú" => "u",
      "ů" => "u",
      "ý" => "y",
      "ž" => "z",
      "Á" => "a",
      "Č" => "c",
      "Ď" => "d",
      "É" => "e",
      "Ě" => "e",
      "Í" => "i",
      "Ľ" => "l",
      "Ň" => "n",
      "Ó" => "o",
      "Ř" => "r",
      "Š" => "s",
      "Ť" => "t",
      "Ú" => "u",
      "Ů" => "u",
      "Ý" => "y",
      "Ž" => "z"    
    );
    
		$return = strtr($return,$trans);

		$return = Str_Replace(Array(" ", "_"), "-", $return); //nahradí mezery a podtržítka pomlčkami

		$return = Str_Replace(Array("(",")",".","!",",","\"","'","?",":"), "", $return); //odstraní ().!,"'

		$return = StrToLower($return); //velká písmena nahradí malými.

		return $return;

	}
?>
