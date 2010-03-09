<h1><?php echo $model->t($contr->pageTitle) ?></h1>

<?php if ( !empty($view->error) ): ?>
<p class="message error"><?php echo $view->error ?></p>
<?php endif ?>

<?php if ( !empty($view->notice) ): ?>
<p class="message notice"><?php echo $view->notice ?></p>
<?php endif ?>

<?php if ( $view->action == 'create' && $model->perm->check('admin perm create') || $view->action == 'edit' && $model->perm->check('admin perm edit') ): ?>
<?php if ( $view->action == 'create' ): ?>
<h2><?php echo t('New role') ?></h2>
<?php else: ?>
<h2><?php echo t('Edit role') ?></h2>
<?php endif ?>

<form id="formRole" method="post" action="./?<?php echo $view->action == 'edit' ? 'id=' . $view->id . '&' : '' ?>action=<?php echo $view->action ?>">
	<fieldset>
		<dl>
			<dt>
				<label for="name"><?php echo t('Name') ?></label>
			</dt>
			<dd>
				<input type="text" name="name" id="name" value="<?php echo $model->POST_html_safe['name'] ?>"/>

				<?php if ( isset($model->form->errors['name']) ): ?>
				<span class="error"><?php echo $model->form->errors['name'] ?></span>
				<?php endif ?>
			</dd>
		</dl>
	</fieldset>
	<fieldset>
		<dl>
			<dt><br/></dt>
			<dd>
				<input type="hidden" name="auth_token" value="<?php echo $model->authToken ?>"/>

				<input type="submit" name="form-submit" id="form-submit" value="<?php echo t('Save role') ?>"/>

				<a href="./"><?php echo t('Cancel') ?></a>
			</dd>
		</dl>
	</fieldset>
</form>

<script type="text/javascript">
	<!-- /* <![CDATA[ */
	// Focus the name field
	$(function() {
		$('#name').focus();
	});
	/* ]]> */ -->
</script><?php else: ?>
<?php if ( $model->perm->check('admin perm role create') ): ?>
<p>
	<a class="button" href="./?action=create">&#9998; <?php echo $model->t('Create a new role') ?></a>
</p>
<?php endif ?>
<?php endif ?>

<h2><?php echo t('Roles') ?></h2>

<?php if ( $view->roles ): ?>
<ul>
	<?php foreach ( $view->roles as $role ): ?>
	<li>
		<h4><?php echo h(t($role['name'])) ?></h4>

		<?php if ( $model->perm->check('admin perm edit') || $model->perm->check('admin perm delete') ): ?>
		<p>
			<?php if ( $model->perm->check('admin perm edit') ): ?>
			<a class="button" href="./?id=<?php echo $role['id'] ?>&action=edit"  >&#9986; <?php echo $model->t('Edit this role') ?></a>
			<?php endif ?>
			<?php if ( $model->perm->check('admin perm delete') ): ?>
			<a class="button" href="./?id=<?php echo $role['id'] ?>&action=delete">&#10008; <?php echo $model->t('Delete this role') ?></a>
			<?php endif ?>
		</p>
		<?php endif ?>

		<?php if ( $view->action == 'add' && $view->id == $role['id'] ): ?>
		<h5><?php echo t('Add user') ?></h5>

		<form id="formUser<?php echo $role['id'] ?>" method="post" action="./?action=add&id=<?php echo $role['id'] ?>">
			<fieldset>
				<dl>
					<dt>
						<label for="user"><?php echo t('User') ?></label>
					</dt>
					<dd>
						<select name="user" id="user">
							<option value=""><?php echo t('Select a user') ?></option>
							<?php if ( $view->users ): ?>
							<?php foreach ( $view->users as $user ): ?>
							<option value="<?php echo $user['id'] ?>"><?php echo h($user['username']) ?></option>
							<?php endforeach ?>
							<?php endif ?>
						</select>
					</dd>
				</dl>
			</fieldset>
			<fieldset>
				<dl>
					<dt><br/></dt>
					<dd>
						<input type="hidden" name="auth_token" value="<?php echo $model->authToken ?>"/>

						<input type="submit" name="form-submit-2" id="form-submit-2" value="<?php echo t('Add user') ?>"/>

						<a href="./"><?php echo t('Cancel') ?></a>
					</dd>
				</dl>
			</fieldset>
		</form>
		<?php endif ?>
		
		<h5><?php echo t('Users') ?></h5>

		<p>
			<a class="button" href="./?id=<?php echo $role['id'] ?>&action=add">&#10010; <?php echo $model->t('Add a user') ?></a>
		</p>

		<?php if ( $role['users'] ): ?>
		<ul>
			<?php foreach ( $role['users'] as $user ): ?>
			<li>
				<a class="button" href=".?id=<?php echo $role['id'] ?>&user_id=<?php echo $user['id'] ?>&action=remove"><?php echo t('Remove') ?></a>
				<?php echo $user['username'] ?>
			</li>
			<?php endforeach ?>
		</ul>
		<?php else: ?>
		<p>
			<em><?php echo t('This role has no users') ?></em>
		</p>
		<?php endif ?>
	</li>
</ul>

<?php endforeach ?>

<h2><?php echo t('Permissions') ?></h2>

<form id="formPerm" method="post" action="./">
	<fieldset>
		<table>
			<thead>
				<tr>
					<th><?php t('Permission') ?></th>
					<?php foreach ( $view->roles as $role ): ?>
					<th><?php echo h(t($role['name'])) ?></th>
					<?php endforeach ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $view->permsGroups as $group => $perms ): ?>
				<tr>
					<th>
						<strong><?php echo h(t($group)) ?></strong>
					</th>
					<th><br/></th>
				</tr>
				<?php foreach ( $perms as $perm ): ?>
				<tr>
					<th>
						<?php echo h(t($perm['desc'])) ?>
					</th>
					<?php foreach ( $view->roles as $role ): ?>
					<td>
						<select name="value[<?php echo $perm['id'] ?>][<?php echo $role['id'] ?>]" id="value_<?php echo $perm['id'] ?>_<?php echo $role['id'] ?>">
							<option value="<?php echo perm::yes   ?>"<?php echo $model->POST_html_safe['value'][$perm['id']][$role['id']] == perm::yes   ? ' selected="selected"' : '' ?>>&#10004; <?php echo t('Yes')   ?></option>
							<option value="<?php echo perm::no    ?>"<?php echo $model->POST_html_safe['value'][$perm['id']][$role['id']] == perm::no    ? ' selected="selected"' : '' ?>>&#10008; <?php echo t('No')    ?></option>
							<option value="<?php echo perm::never ?>"<?php echo $model->POST_html_safe['value'][$perm['id']][$role['id']] == perm::never ? ' selected="selected"' : '' ?>>&#10008; <?php echo t('Never') ?></option>
						</select>
					</td>
					<?php endforeach ?>
				</tr>
				<?php endforeach ?>
				<?php endforeach ?>
			</tbody>
		</table>
	</fieldset>
	<fieldset>
		<dl>
			<dt><br/></dt>
			<dd>
				<input type="hidden" name="auth_token" value="<?php echo $model->authToken ?>"/>

				<input type="submit" name="form-submit-3" id="form-submit-3" value="<?php echo $model->t('Save permissions') ?>"/>
			</dd>
		</dl>
	</fieldset>
</form>
<?php else: ?>
<p>
	<em><?php echo t('No roles') ?><em>
</p>
<?php endif ?>
