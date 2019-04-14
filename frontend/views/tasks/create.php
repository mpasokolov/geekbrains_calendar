<?php
$this -> title = 'Создать задачу';
$this -> params['breadcrumbs'][] = $this -> title;
?>

<div class="createTask">
    <?= $this -> render('_form', ['model' => $model]) ?>
</div>
