
<?php
// <= PHP 5	
$filecontent = file_get_contents('article_01.json');
$content = json_decode($filecontent, true);

var_dump(count($content["articles"]));


var_dump($content["articles"][0]["title"]);

?>
