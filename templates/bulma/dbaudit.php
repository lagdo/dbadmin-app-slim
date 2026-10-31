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
        <div style="padding-right: 1rem; padding-left: 1rem;">
          <?= $this->di->g($this->ui)->layout() ?>
        </div>
<?php $this->endblock() ?>
