<?php

/**
 * \ingroup bs
 *
 * Provides an interface to database table ORGANIZATION that holds organizations.
 *
 * Columns of table organization are: <code>id, name, short_name, disambiguation, organization_kind_code, last_updated_on</code>.
 *
 * Column <code>id</code> is a read-only column automaticaly generated on create.
 *
 * Primary key is column <code>id</code>.
 */
class Paragraph
{
	/// instance holding a list of table columns and table handling functions
	private $entity;

	/**
	 * Initialize information about the underlying database table.
	 */
	public function __construct()
	{
		$this->entity = new Entity(array(
			'name' => 'paragraph',
			'columns' => array('code', 'name', 'description'),
			'pkey_columns' => array('code')
		));
	}

	/**
	 * Read the organization(s) that satisfy given parameters.
	 *
	 * \param $params An array of pairs <em>column => value</em> specifying the organizations to select.
	 *
	 * \return An array of organizations that satisfy all prescribed column values.
	 *
	 * \ex
	 * \code
	 * read(array('name' => 'Lesní správa Lány'))
	 * \endcode returns
	 * \code
	 * Array
	 * (
	 *     [0] => Array
	 *         (
	 *             [id] => 1
	 *             [name] => Lesní správa Lány
	 *             [short_name] => Lesní správa Lány
	 *             [disambiguation] => 
	 *             [last_updated_on] => 2011-06-24 00:30:09.234649
	 *         )
	 *
	 * )
	 * \endcode
	 */
	public function read($params)
	{
		return $this->entity->read($params);
	}

	/**
	 * Create a organization(s) from given values.
	 *
	 * \param $data An array of pairs <em>column => value</em> specifying the organization to create. Alternatively, an array of such organization specifications.
	 * If \c last_updated_on column is ommitted, it is set to the current timestamp.	 
	 *
	 * \return An array of primary key values of created organization(s).
	 *
	 * \ex
	 * \code
	 * create(array( 'name' => 'Lesní správa Lány', 'short_name' => 'Lesní správa Lány'))
	 * \endcode creates a new organization and returns e.g.
	 * \code
	 * Array
	 * (
	 *     [id] => 1
	 * )
	 * \endcode
	 */
	public function create($data)
	{
		return $this->entity->create($data);
	}

	/**
	 * Update the given values of the organizations that satisfy given parameters.
	 *
	 * \param $params An array of pairs <em>column => value</em> specifying the organizations to update. Only the organizations that satisfy all prescribed column values are updated.
	 * If the parameter contain \c last_updated_on column, then only the organizations with older value in their \c last_updated_on column are updated.
	 * \param $data An array of pairs <em>column => value</em> to set for each updated organization.
	 *
	 *
	 * \return An array of primary key values of updated organizations.
	 */
	public function update($params, $data)
	{
		return $this->entity->update($params, $data);
	}

	/**
	 * Delete the organization(s) that satisfy given parameters.
	 *
	 * \param $params An array of pairs <em>column => value</em> specifying the organizations to delete. Only the organizations that satisfy all prescribed column values are deleted.
	 *
	 * \return An array of primary key values of deleted organizations.
	 */
	public function delete($params)
	{
		return $this->entity->delete($params);
	}
}

?>
