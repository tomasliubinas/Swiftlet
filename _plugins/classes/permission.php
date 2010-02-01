<?php
/**
 * @package Swiftlet
 * @copyright 2009 ElbertF http://elbertf.com
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU Public License
 */

if ( !isset($model) ) die('Direct access to this file is not allowed');

/**
 * Permissions
 * @abstract
 */
class perm
{
	public
		$ready
		;

	const
		roleOwnerId = 1,
		yes         = 1,
		no          = 0,
		never       = -1
		;

	private
		$model,
		$contr
		;

	/**
	 * Initialize
	 * @param object $model
	 */
	function __construct($model)
	{
		$this->model = $model;
		$this->contr = $model->contr;

		/**
		 * Check if the permissions table exists
		 */
		if ( in_array($model->db->prefix . 'perms', $model->db->tables) )
		{
			$model->db->sql('
				SELECT
					p.`name`  AS `permission`,
					pr.`name` AS `role`,
					prx.`value`
				FROM      `' . $model->db->prefix . 'perms_roles_users_xref` AS prux
				LEFT JOIN `' . $model->db->prefix . 'perms_roles`            AS pr   ON prux.`role_id` = pr.`id`
				LEFT JOIN `' . $model->db->prefix . 'perms_roles_xref`       AS prx  ON pr.`id`        = prx.`role_id` 
				LEFT JOIN `' . $model->db->prefix . 'perms`                  AS p    ON prx.`perm_id`  = p.`id`
				WHERE
					p.`name`  IS NOT NULL AND
					pr.`name` IS NOT NULL AND
					prux.`user_id` = ' . ( int ) $model->session->get('user id') . '
				', FALSE);

			if ( $r = $model->db->result )
			{
				$perms = array();

				foreach ( $r as $d )
				{
					if ( empty($perms[$d['permission']]) || $perms[$d['permission']] != -1 )
					{
						$perms[$d['permission']] = $d['value'];
					}
				}

				foreach ( $perms as $name => $value )
				{			
					$model->session->put('perm ' . $name, ( $model->session->get('user id owner') or $value == 1 ) ? 1 : 0);
				}			
			}

			$this->ready = TRUE;
		}
	}

	/**
	 * Check if the current user has permssion
	 * @param string $name
	 * @return bool
	 */
	function check($name)
	{
		$model = $this->model;

		return $model->session->get('user is owner') or $model->session->get('perm ' . $name);
	}

	/**
	 * Create a new permission
	 * @param string $group
	 * @param string $name
	 * @param string $description
	 * @return integer
	 */
	function create($group, $name, $description)
	{
		$model = $this->model;

		$model->db->sql('
			INSERT IGNORE INTO `' . $model->db->prefix . 'perms` (
				`name`,
				`desc`,
				`group`
				)
			VALUES (
				"' . $model->db->escape($name) . '",
				"' . $model->db->escape($description) . '",
				"' . $model->db->escape($group) . '"
				)
			;');
	}

	/**
	 * Delete a permission
	 * @param string $name
	 * @return bool
	 */
	function delete($name)
	{
		$model = $this->model;

		$model->db->sql('
			SELECT
				`id`
			FROM `' . $model->db->prefix . 'perms`
			WHERE
				`name` = "' . $model->db->escape($name) . '"
			LIMIT 1
			;');

		if ( $model->db->result && $id = $model->db->result[0]['id'] )
		{
			$model->db->sql('
				DELETE
					p, prx
				FROM      `' . $model->db->prefix . 'perms`            AS p
				LEFT JOIN `' . $model->db->prefix . 'perms_roles_xref` AS prx ON p.`id` = prx.`perm_id`
				WHERE
					p.`id` = ' . ( int ) $id . '
				;');

			return !empty($model->db->result);
		}
	}
}
