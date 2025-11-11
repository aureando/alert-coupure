<?php $title = 'Tableau de bord'; ?>

<h2>Bienvenue, <?= e(auth_user()['prenom']) ?> !</h2>

<div class="alert alert-info">
    Dashboard en construction...
</div>

<p>Coupures actives : <?= count($coupures) ?></p>
<p>Mes signalements : <?= count($signalements) ?></p>