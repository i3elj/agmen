<?php
use function Tusk\globals;
use function Tusk\snip;
?>

<?= snip("goback") ?>

<h1>Here are some queries</h1>

<ul>
	<?php foreach ($queries as $key => $value): ?>
		<li><?= $key ?>: <?= $value ?></li>
	<?php endforeach; ?>
</ul>

<?= globals("footer") ?>
