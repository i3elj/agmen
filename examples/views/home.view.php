<?php
use function Agmen\globals;

$query = r->getPath("queries", query_params: ["test" => "foo", "baz" => "bar"]);
$params = r->getPath("parameters", route_params: ["foo" => "bar"]);
$secure = r->getPath("secure");
?>

<h1>Home with router class</h1>

<ul>
	<li><a href="<?= $query ?>">Test query parameters</a></li>
	<li><a href="<?= $params ?>">Test url parameters: <?= $params ?></a></li>
	<li><a href="<?= $secure ?>">try it!</a></li>
</ul>

<?= globals("footer") ?>
