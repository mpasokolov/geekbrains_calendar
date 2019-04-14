<?php

$this -> title = 'Обновить команду';
$this -> params['breadcrumbs'][] = $this -> title;

?>

<?= $this -> render('_form', ['model' => $model]) ?>