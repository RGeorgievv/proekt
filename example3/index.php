<?php

// pyrvo vkluchvame template engine-a v koda
require_once('./modules/template.php');

// zadavame pytia do direktoriata s HTML shabloni
$path = './templates/';

// syzdawame instakcia na klasa
$tpl = new Template($path);

// zadavame hosta, potrebitelsko ime i parolata
$host = '127.0.0.1';
$user = 'root';
$password = '';
$db_name = 'agoptigura';
// vyvedete korektnite danni v zavisimost ot rabotnoto miasto kydeto ste sednali

// vryzvame se kym SUBD
$DBH = new mysqli($host, $user, $password, $db_name);
if ($DBH->connect_errno) {
	// ako vyznikne greshka izvejdame syobshtenieto koeto vryshta mysql
	print $DBH->connect_error;
	exit;
}

$DBH->query("SET NAMES utf8");



//	menu
$query = 'SELECT*
FROM courses';


// izpylniavame zaiavkata
if (!$result = $DBH->query($query)) {
	print $DBH->error;
	exit; 
}

// tuk izvlichame vseki zapis ot rezultata 
// prehvyrliame dannite kym modula za upravlenie na potrebitelskia i-face
// toi zamestva etiketite v _table_row.html s korektnata informacia
// i wryshta obrabotelia HTML kod na reda
// tozi kod go syhraniavame vyv vremenna promenliva, narechena $temp 
$temp = '';
while ($row = $result->fetch_assoc()) {
	foreach($row as $index => $st)
	$tpl ->set($index, $row [$index]);

		if(@$_GET['pate']==$row['id']){
		$tpl->set('Gop','selected');
		}
		else{
			$tpl->set('Gop','');
		}
	$temp = $temp . $tpl->fetch('Option.html');
}

// tuk veche redovete sa izgenerirani i sega triabva
// mnojestvoto redove da go vmyknem v HTML koda na _main_template.html
$tpl->set('specialnosti', $temp);

// resultati
// tuk pishem zaiavkata
$sql = 'SELECT firstName,lastName,FN,name
FROM courses,students
where courses.id=students.courseId ';

if(!empty($_GET['pate'])){
	$sql=$sql.' and courseId = ?';
}

// izpylniavame zaiavkata
if (!$stmt = $DBH->prepare($sql)) {
	print $DBH->error;
	exit; 
}			

// Свързваме променливата $minGrade, която съдържа минималната оценка, по която търсим 
// с безименния параметър ? в sql заявката.
// Тъй като оценката е число с плаваща запетая, то в първия аргумент на метода указваме d.
if(!empty($_GET['pate'])){ 			
if (!$stmt->bind_param("s", $_GET['pate'])) {
	print $DBH->error;
	exit; 
}
}
// Ако в последствие трябва да изпълним същата заявка, но с друга стойност на оценката,
// тогава само присвояваме нова стойност на $minGrade, без да свързваме (bind-ваме) променлива отново.
// $minGrade = 4;			
			
// Изпълнява се заявката.
if (!$stmt->execute()) {
	// Ако заявката не може да се изпълни се извежда 
	// съобщение за грешка и се прекратява 
	// изпълнението на програмата.
	print $DBH->error;
	exit; 
}

// Извличаме целия резултат (т.е. всички записи наведнъж), който се буферира в php приложението ни.
// Така приложението ни ще знае броя на върнатите записи и ще освободи заетите от резултата ресурси в СУБД.
// Обектът $result се обработва по същия начин, като при обикновените заявки.
if (!$result = $stmt->get_result()) {
	print $DBH->error;
	exit; 
}

// tuk izvlichame vseki zapis ot rezultata 
// prehvyrliame dannite kym modula za upravlenie na potrebitelskia i-face
// toi zamestva etiketite v _table_row.html s korektnata informacia
// i wryshta obrabotelia HTML kod na reda
// tozi kod go syhraniavame vyv vremenna promenliva, narechena $temp 
$temp = '';
while ($row = $result->fetch_assoc()) {
	foreach($row as $index => $st)
	$tpl ->set($index, $row [$index]);

	$temp = $temp . $tpl->fetch('_table_row.html');
}

	if(!empty($temp)){
		$tpl->set('studenti', $temp);
	}
	else{
		$tpl->set('studenti',$tpl->fetch('nodata.html'));
	}


$result->free();
$DBH->close();

// izvlichame sydyrjanieto na _main_template.html 
// i zameniame etiketa specialnosti
// s generiranite redove v tablicata

print $tpl->fetch('_main_template.html');

// i tova e :)

?>