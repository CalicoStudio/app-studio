<?php
// Gestion multi-serveur de l'application (Premier dispo est pris)
$mysql_server = Array();
$mysql_server[0]["host"] = "localhost";
$mysql_server[0]["user"] = "mhabitat";
$mysql_server[0]["pass"] = "bGu0sD4Yk7";
$mysql_server[0]["base"] = "appstudio";
$mysql_server[1]["host"] = "localhost";
$mysql_server[1]["user"] = "root";
$mysql_server[1]["pass"] = "";
$mysql_server[1]["base"] = "appstudio";

foreach ($mysql_server as $connect) {
	if (@mysql_connect($connect["host"],$connect["user"],$connect["pass"])) {
		$G_mysql_server = $connect["host"];
        $select_base=@mysql_select_db($connect["base"]);
		break;
	}
}
mysql_query("SET NAMES UTF8");

if (!$select_base) { // Si échec
	echo "<font color=\"#CC0000\" face=\"Verdana\"><b>Erreur de connexion !</b></font><br />\n";
	echo "<font face=\"Verdana\"><b>Motorisation Habitat</b></font><br />\n";
	echo "Merci de pr&eacute;venir <a href=\"mailto:support@studio-calico.fr\">Calico</a> qu'une erreur intervient lors de la connexion à la base <b>$base</b></font>";
	exit();
}

//génération du xml des langues
$q_lang=mysql_query("SELECT * FROM `langue` ORDER BY `nom` ASC");
$xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
$xml .= '<langs>'."\n";
while($rows=mysql_fetch_array($q_lang)) {
	$xml.= "\t".'<lang slug="'.$rows['slug'].'"><nom>'.$rows['nom'].'</nom><desc><![CDATA['.$rows['description'].']]></desc></lang>'."\n";
}
$xml .= '</langs>';
$fp = fopen("pays.xml", 'w+');
fputs($fp, $xml);
fclose($fp);
echo utf8_decode('-> Génération du xml des langues<br />');

//génération du xml des catégories
$q_cat=mysql_query("SELECT * FROM `categorie` ORDER BY `ordre` ASC");
$xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
$xml .= '<categories>'."\n";
$lang="";
while($rows=mysql_fetch_array($q_cat)) {
	$xml.= "\t".'<categorie slug="'.$rows['slug'].'" id="'.$rows['id'].'" color="'.$rows['color'].'">'."\n";
	$xml.= "\t\t".'<cat lang="'.$rows['lang'].'" titre="'.addslashes($rows['nom']).'"><![CDATA['.$rows['content'].']]></cat>'."\n";
	
	$qry_trad=mysql_query("SELECT * FROM `categorie_trad` WHERE `id_cat`=".$rows['id']);
	while($r_trad=mysql_fetch_array($qry_trad)) {
		$xml.="\t\t".'<cat lang="'.$r_trad['lang'].'" titre="'.$r_trad['trad'].'">'.$r_trad['content'].'</cat>'."\n";
	}
	$xml.= "\t".'</categorie>'."\n";
}
$xml .= '</categories>';
$fp = fopen("categorie.xml", 'w+');
fputs($fp, $xml);
fclose($fp);
echo utf8_decode('-> Génération du xml des catégories<br />');

?>