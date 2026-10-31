<?php $this->extends('dbadmin::auth/layout') ?>

<?php $this->block('pageTitle') ?>
<?= __('Login') ?>
<?php $this->endblock() ?>

<?php $this->block('headTitle') ?>
Jaxon DbAdmin
<?php $this->endblock() ?>

<?php $this->block('pageContent') ?>
            <form method="POST" action="/login" class="needs-validation" novalidate="">
                <label class="label"><?= __('Email') ?></label>
                <p class="control has-icon has-icons-left">
                  <input class="input" id="inputEmail" type="email" name="email" placeholder="name@example.com" required autofocus />
                  <span class="icon is-small is-left"><i class="fa fa-envelope"></i></span>
<?php if(isset($this->errors['email'])): ?>
                  <span class="help is-danger"><?= $this->errors['email'] ?></span>
<?php endif ?>
                </p>
                <label class="label mt-3"><?= __('Password') ?></label>
                <p class="control has-icon has-icons-left">
                  <input class="input" id="inputPassword" type="password" name="password" placeholder="Password" />
                  <span class="icon is-small is-left"><i class="fa fa-lock"></i></span>
<?php if(isset($this->errors['password'])): ?>
                  <span class="help is-danger"><?= $this->errors['password'] ?></span>
<?php endif ?>
                </p>
                <p class="control mt-5">
                  <label for="inputRememberPassword" class="b-checkbox checkbox">
                    <input id="inputRememberPassword" type="checkbox" name="remember" checked />
                    <span class="check"></span>
                    <span class="control-label"><?= __('Remember Me') ?></span>
                  </label>
                </p>
                <hr>
                <p class="control">
                  <button type="submit" class="button is-primary"><?= __('Login') ?></button>
                </p>
            </form>
<?php $this->endblock() ?>
