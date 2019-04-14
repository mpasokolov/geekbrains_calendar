<?php

$this -> title = 'Редактирование задачи';
$this -> params['breadcrumbs'][] = ['label' => 'Tasks', 'url' => 'index'];
$this -> params['breadcrumbs'][] = ['label' => $model -> name, 'url' => ['view', 'id' => $model -> id]];
$this -> params['breadcrumbs'][] = $this -> title;

?>

<div class="task_update">
    <?= $this -> render('_form', ['model' => $model, 'result' => isset($result) ? $result : null]) ?>
</div>
