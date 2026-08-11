<?php $this->extends('tpl::layout') ?>

<?php
use Lagdo\DbAdmin\App\DbAuditPackage;
use function Jaxon\attr;
use function Jaxon\jaxon;
?>

<?php $this->block('htmlHeader') ?>
<?= jaxon()->getCss(), "\n" ?>
<?php $this->endblock() ?>

<?php $this->block('htmlFooter') ?>
<?= jaxon()->getJs(), "\n", jaxon()->getScript(), "\n" ?>
<?php
$readyScript = attr()->package(DbAuditPackage::class, 'ready');
?>
<?php if($readyScript !== ''): ?>
<script type='text/javascript'>
  <?= $readyScript ?>;
</script>
<?php endif ?>
<?php $this->endblock() ?>

<?php $this->block('pageContent') ?>
        <div class="container-fluid px-3">
          <?php echo jaxon()->package(DbAuditPackage::class)->layout() ?>
        </div>
<?php $this->endblock() ?>
