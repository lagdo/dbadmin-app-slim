<?php $this->extends('dbadmin::auth/layout') ?>

<?php $this->block('pageTitle') ?>
Forbidden
<?php $this->endblock() ?>

<?php $this->block('headTitle') ?>
403
<?php $this->endblock() ?>

<?php $this->block('pageContent') ?>
                    <div class="column is-two-thirds has-text-left">
                        <h1 class="title is-2">Forbidden</h1>
                        <p class="is-size-4">Access to this resource is denied.</p>
                        <a href="/">
                            <i class="fas fa-arrow-left me-1"></i> Return to Dashboard
                        </a>
                    </div>
<?php $this->endblock() ?>
