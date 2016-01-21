<?php
namespace FM\db;

use FM\Application;
use FM\BaseModel;

/**
 * Class DBModel
 * @package FM\db
 * @author Roman Angelovskiy
 */
class DBModel extends BaseModel{

	/**
	 * Instance of PDO initialized in \FM\db\Conncetion
	 *
	 * @var \FM\db\Conncetion
	 */
	public $db;

	private $__tableName;

	private $__DataSet;

	public function __construct($condition = []){
		$this->db = Application::$i->dbConnection;

		$this->__tableName = static::tableName();

		if (!empty($condition)){
			$this->__DataSet = $this->select($condition)->get();
			var_dump_pre($this->__DataSet);
		}
	}

	/**
	 * Declare the table name associated with current model. Method can be
	 * override in child class
	 *
	 * @return string
	 */
	public static function tableName(){
		$childClass = get_called_class();

		return strtolower(substr($childClass, strrpos($childClass, '\\')+1));
	}

	/**
	 * Prepare SELECT query
	 *
	 * @param array $Condition
	 *
	 * @return $this
	 */
	public function select($Condition = []){
		$result = $this->db->driver()
			->createCommand('select', static::tableName(), $Condition);

		return $this;
	}

	/**
	 * Prepare INSERT query
	 *
	 * @param $Data
	 *
	 * @return int Last inserted autoincrement value
	 */
	public function insert($Data){
		$insertId = $this->db->driver()
			->createCommand('insert', static::tableName(), $Data)
			->execute();

		return (int) $insertId;
	}

	/**
	 * Prepare UPDATE query. Use where(), whereOr(), whereAnd() methods for set
	 * 						 conditions. Use save() to apply changes
	 *
	 * @param $Data
	 *
	 * @return $this
	 */
	public function update($Data){
		$result = $this->db->driver()
			->useTable(static::tableName())
			->createCommand('update', static::tableName(), $Data);

		return $this;
	}

	public function delete(){ //TODO

	}

	/**
	 * Add condition in query. If condition already exists,
	 * prepend AND operator.
	 *
	 * @param string $sign
	 * @param string $field
	 * @param string|integer $value
	 *
	 * @return $this
	 */
	public function where($sign, $field, $value){
		$this->db->driver()->addCondition($field, $value, $sign);

		return $this;
	}

	/**
	 * Add condition in query and prepend AND operator.
	 *
	 * @param array  $Condition		Array with new condition.
	 * 								If $Condition array contains other arrays it will be
	 * 								interpreted like conditions in brackets. Example:
	 * 								[['=', 'id', 1], ['=', 'cat', 2]]
	 * 								(`id` = 1 AND `cat` = 2)
	 * @param string $bundleConcat	Logical operator prepend to first bracket
	 *
	 * @return $this
	 */
	public function whereAnd($Condition, $bundleConcat = 'AND'){
		if (is_array($Condition[0])){
			$this->db->driver()->addConditionBundle($bundleConcat, $Condition, 'AND');
			return $this;
		}

		$this->db->driver()->addCondition($Condition[1], $Condition[2], $Condition[0], 'AND');

		return $this;
	}

	/**
	 * Add condition in query and prepend OR operator.
	 *
	 * @param array  $Condition		Array with new condition.
	 * 								If $Condition array contains other arrays it will be
	 * 								interpreted like conditions in brackets. Example:
	 * 								[['=', 'id', 1], ['=', 'cat', 2]]
	 * 								(`id` = 1 AND `cat` = 2)
	 * @param string $bundleConcat	Logical operator prepend to first bracket
	 *
	 * @return $this
	 */
	public function whereOr($Condition, $bundleConcat = 'AND'){
		if (is_array($Condition[0])){
			$this->db->driver()->addConditionBundle($bundleConcat, $Condition, 'OR');
			return $this;
		}

		$this->db->driver()->addCondition($Condition[1], $Condition[2], $Condition[0], 'OR');

		return $this;
	}

	/**
	 * Set limit number returned rows
	 *
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function limit($limit){
		$this->db->driver()->limit($limit);
		return $this;
	}

	/**
	 * Set start position for limit
	 * @param int $offset
	 *
	 * @return $this
	 */
	public function offset($offset){
		$this->db->driver()->offset($offset);
		return $this;
	}

	/**
	 * Returns result SELECT query
	 *
	 * @param bool $returnArray If true then return all rows data like
	 * 							array in other case return Object
	 *
	 * @return mixed
	 */
	public function get($returnArray = false){
		return $this->db->driver()->get($returnArray);
	}

	/**
	 * Apply changes for UPDATE query
	 *
	 * @return int Returns the number of rows affected by UPDATE statement
	 */
	public function save(){
		return $this->db->driver()->execute();
	}

	/**
	 * Returns count of rows in query result
	 *
	 * @return int
	 */
	public function count(){
		return $this->db->driver()->count();
	}
}