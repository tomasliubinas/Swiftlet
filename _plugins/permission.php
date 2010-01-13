<?php
/**
 * @package Swiftlet
 * @copyright 2009 ElbertF http://elbertf.com
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU Public License
 */

if ( !isset($model) ) die('Direct access to this file is not allowed');

switch ( $hook )
{
	case 'info':
		$info = array(
			'name'         => 'perm',
			'version'      => '1.0.0',
			'compatible'   => array('from' => '1.2.0', 'to' => '1.2.*'),
			'dependencies' => array('session', 'user'),
			'hooks'        => array('init' => 4, 'install' => 1)
			);

		break;
	case 'install':
		$model->db->sql('
			CREATE TABLE `' . $model->db->prefix . 'perms` (
				`id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255)     NOT NULL,
				UNIQUE `name` (`name`),
				PRIMARY KEY (`id`)
				)
			;');

		$model->db->sql('
			CREATE TABLE `' . $model->db->prefix . 'perms_roles` (
				`id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255)     NOT NULL,
				UNIQUE `name` (`name`),
				PRIMARY KEY (`id`)
				)
			;');

		$model->db->sql('
			INSERT INTO `' . $model->db->prefix . 'perms_roles` (
				`name`
				)
			VALUES (
				"Administrator"
				)
			;');

		$model->db->sql('
			CREATE TABLE `' . $model->db->prefix . 'perms_roles_xref` (
				`perm_id` INT(10) UNSIGNED NOT NULL,
				`role_id` INT(10) UNSIGNED NOT NULL,
				`value`   INT(1)               NULL,
				UNIQUE `perm_user` (`perm_id`, `role_id`)
				)
			;');

		$model->db->sql('
			CREATE TABLE `' . $model->db->prefix . 'perms_roles_users_xref` (
				`role_id` INT(10) UNSIGNED NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				UNIQUE `role_user` (`role_id`, `user_id`)
				)
			;');

		break;
	case 'init':
		if ( !empty($model->session->ready) )
		{
			require($contr->classPath . 'permission.php');

			$model->perm = new perm($model);
		}

		break;
}