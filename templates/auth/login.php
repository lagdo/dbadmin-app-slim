<?php $this->extends('tpl::auth/layout') ?>

<?php $this->block('pageTitle') ?>
<?= __('Login') ?>
<?php $this->endblock() ?>

<?php $this->block('pageContent') ?>
                  <form method="POST" action="/login" class="needs-validation" novalidate="">
                    <div class="form-floating mb-3">
                      <input class="form-control <?php if(isset($this->errors['email'])): ?>is-invalid<?php endif ?>" id="inputEmail" type="email" name="email" placeholder="name@example.com" required autofocus />
                      <label for="inputEmail"><?= __('Email') ?></label>
                      <div class="invalid-feedback"><?= $this->errors['email'] ?? '' ?></div>
                    </div>

                    <div class="form-floating mb-3">
                      <input class="form-control <?php if(isset($this->errors['password'])): ?>is-invalid<?php endif ?>" id="inputPassword" type="password" name="password" placeholder="Password" />
                      <label for="inputPassword"><?= __('Password') ?></label>
                      <div class="invalid-feedback"><?= $this->errors['password'] ?? '' ?></div>
                    </div>

                    <div class="form-check mb-3">
                      <input class="form-check-input" id="inputRememberPassword" type="checkbox" name="remember" checked />
                      <label class="form-check-label" for="inputRememberPassword"><?= __('Remember Me') ?></label>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                      <span>&nbsp;</span>
                      <button type="submit" class="btn btn-primary" tabindex="4"><?= __('Login') ?></button>
                    </div>
                  </form>
<?php $this->endblock() ?>
