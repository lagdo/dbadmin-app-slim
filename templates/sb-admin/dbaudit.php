<?php $this->extends('dbadmin::layout') ?>

<?php $this->block('htmlHeader') ?>
<?= $this->jaxon->getCss()?>

<?php $this->endblock() ?>

<?php $this->block('htmlFooter') ?>
<?= $this->jaxon->getJs()?>

<?= $this->jaxon->getScript()?>

<script type='text/javascript'>
  <?= attr()->package($this->package, 'ready') ?>;
</script>
<?php $this->endblock() ?>

<?php $this->block('pageContent') ?>
        <div class="container-fluid px-3">
          <?= $this->di->g($this->ui)->layout() ?>
        </div>
<?php $this->endblock() ?>
