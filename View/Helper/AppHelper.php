<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {
    /**
	* Get Enum Values
	* Snippet v0.1.3
	* http://cakeforge.org/snippet/detail.php?type=snippet&id=112
	*
	* Gets the enum values for MySQL 4 and 5 to use in selectTag()
	*/
	function getEnumValues($columnName=null, $respectDefault=false) {
		if ($columnName==null)
		{
			return array();
		}

		//no field specified
		//Get the name of the table
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$tableName = $db->fullTableName($this, false);//Get the values for the specified column (database and version specific, needs testing)
		$result = $this->query("SHOW COLUMNS FROM {$tableName} LIKE '{$columnName}'");//figure out where in the result our Types are (this varies between mysql versions)
		$types = null;

		if (isset($result[0]['COLUMNS']['Type'] ) )
		{
			$types = $result[0]['COLUMNS']['Type'];
			$default = $result[0]['COLUMNS']['Default'];
		}

		//MySQL 5
		elseif (isset($result[0][0]['Type'] ) )
		{
			$types = $result[0][0]['Type'];
			$default = $result[0][0]['Default'];
		}

		//MySQL 4
		else {
			return array();
		}

		//types return not accounted for
		//Get the values
		$values = explode('\',\'', preg_replace('/(enum)\(\'(.+?)\'\)/', '\\2', $types) );

		if ($respectDefault)
		{
			$assoc_values = array("$default"=>Inflector::humanize($default));

			foreach ($values as $value )
			{
				if ($value==$default)
				{
					continue;
				}

				$assoc_values[$value] = Inflector::humanize($value);
			}
		}
		else
		{
			$assoc_values = array();

			foreach ($values as $value )
			{
				$assoc_values[$value] = Inflector::humanize($value);
			}
		}

		return $assoc_values;
	}

	//end getEnumValues
}
