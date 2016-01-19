<?php
namespace FM\db\drivers;

use FM\Application;

/**
 * Class mysql
 * @package FM\db\drivers
 * @author Roman Angelovskiy
 */
class mysql implements DBDriverInterface{

	/**
	 * @var array
	 */
	private $__Command = [];

	/**
	 * @var array
	 */
	private $__Binds = [];

	/**
	 * @var int
	 */
	private $__bindNum = 1;

	public function __construct(){
		$this->__initCommand();
	}

	/**
	 * @param string $command MySQL command (SELECT, INSERT, UPDATE etc.)
	 * @param array $Params   Array of fields, values uses in query
	 *
	 * Examples:
	 * ```php
	 * createCommand('SELECT') - SELECT * FROM {current_table}
	 * createCommand('SELECT', ['id' => 1, 'cat' = 1]) //SELECT * FROM {current_table} WHERE `id` = 1 AND `cat` = 1
	 * ```
	 *
	 * @return $this mysql driver instance
	 */
	public function createCommand($command, $Params = []){
		$method = '__' . strtolower($command) . 'Query';
		$this->$method($Params);

		return $this;
	}

	/**
	 * Set table name for use
	 *
	 * @param string $tableName
	 *
	 * @return $this mysql driver instance
	 */
	public function useTable($tableName){
		$this->__Command['table'] = $tableName;

		return $this;
	}

	/**
	 * Generate string MySQL condition and add it to $__Commands['condition'] array
	 *
	 * @param string 			$field  The name of the table field used in the condition
	 * @param string|integer 	$value  Value compared in condition
	 * @param string 			$sign   Logical sign in condition (=, >, < etc.). Can be used operator IN
	 * @param string 			$concat Logical operator for concatenation of conditions
	 *
	 * @return $this mysql driver instance
	 */
	public function addCondition($field, $value, $sign = '=', $concat = ''){
		if (strtolower($sign) == 'in'){
			$this->__Command['condition'][] = $this->__makeInCondition($field, $value, $concat);
			return $this;
		}

		$bindName = $this->__addBind($field, $value);
		if (!empty($this->__Command['condition']) && $concat == ''){
			$concat = 'AND';
		}
		$this->__Command['condition'][] = $concat . ' `' . $field . '` ' . $sign . ' :' . $bindName;

		return $this;
	}

	/**
	 * Generate string MySQL condition in brackets and add it to $__Commands['condition'] array
	 *
	 * @param string $bundleConcat  Logical operator for concatenation of conditions in brackets
	 * 								with previews conditions
	 * @param array $Conditions		List of conditions in brackets
	 * @param $concat				Logical operator for concatenation of conditions
	 *
	 * Example:
	 * ```php
	 * addCondition('cat', 1, '=')
	 * ->addConditionBundle('AND', [['=', 'id', 1], ['=', 'id', 5]], 'OR')
	 * //SELECT * FROM {current_table} WHERE `cat` = 1 AND (`id` = 1 OR `id` = 5)
	 * ```
	 *
	 * @return $this mysql driver instance
	 */
	public function addConditionBundle($bundleConcat, $Conditions, $concat){
		$Bundle = [];
		$first = true;
		foreach ($Conditions as $Condition){
			if (strtolower($Condition[0]) == 'in'){
				$Bundle[] = $this->__makeInCondition($Condition[1], $Condition[2], $concat);
			} else {
				$bindName = $this->__addBind($Condition[1], $Condition[2]);
				$curConcat = $first === true ? '' : $concat;
				$Bundle[] = $curConcat . ' `' . $Condition[1] . '` ' . $Condition[0] . ' :' . $bindName;
			}
			$first = false;
		}

		if (empty($this->__Command['condition'])){
			$bundleConcat = '';
		}
		$this->__Command['condition'][] = $bundleConcat . ' (' . implode(' ', $Bundle) . ')';

		return $this;
	}

	/**
	 * Set number fetched rows
	 *
	 * @param int $limit Number of rows in result
	 *
	 * @return $this mysql driver instance
	 */
	public function limit($limit){
		$this->__Command['limit'] = $limit;

		return $this;
	}

	/**
	 * Set offset for limit operator
	 *
	 * @param integer $offset
	 *
	 * @return $this
	 */public function offset($offset){
		$this->__Command['offset'] = $offset;

		return $this;
	}

	/**
	 * Compile query and execute it
	 *
	 * @return mixed
	 */
	public function execute(){
		if (!empty($this->__Command['condition'])){
			$this->__Command['condition'] = 'WHERE ' . implode(' ', $this->__Command['condition']);
		} else {
			unset($this->__Command['condition']);
		}

		$sql = implode(' ', $this->__Command);

		$sth = Application::$i->dbConnection->pdo()->prepare($sql);

		$result = $sth->execute($this->__Binds);

		switch ($this->__Command['type']){
			case 'INSERT':
				$result = Application::$i->dbConnection->pdo()->lastInsertId();
				break;
			case 'UPDATE':
				$result = $sth->rowCount();
				break;
			default:
		}

		$this->__initCommand();

		return $result;
	}

	/**
	 * Compile query and return result
	 *
	 * @param bool $returnArray If true then return all rows data like
	 * 							array in other case return Object
	 * @param bool $initCommand
	 *
	 * @return mixed
	 */
	public function get($returnArray = false, $initCommand = true){
		$Condition = [];
		if (!empty($this->__Command['condition'])){
			$Condition = $this->__Command['condition'];
			$this->__Command['condition'] = 'WHERE ' . implode(' ', $this->__Command['condition']);
		} else {
			unset($this->__Command['condition']);
		}

		$limit = $this->__makeLimit();

		$sql = implode(' ', $this->__Command) . $limit;

		$sth = Application::$i->dbConnection->pdo()->prepare($sql);

		$sth->execute($this->__Binds);

		if ($initCommand === true){
			$this->__initCommand();
		} else {
			$this->__Command['condition'] = $Condition;
		}

		$mode = $returnArray === false ? \PDO::FETCH_CLASS : \PDO::FETCH_ASSOC;

		return $sth->fetchAll($mode);
	}

	public function count(){
		$fields = $this->__Command['selectedFields'];
		$this->__Command['selectedFields'] = 'COUNT(*) AS `cnt`';
		$count = $this->get(true, false)[0]['cnt'];

		$this->__Command['selectedFields'] = $fields;

		return (int) $count;
	}

	/**
	 * Clear array with commands and binded parameters
	 */
	private function __initCommand(){
		$this->__Binds = [];

		$this->__Command = [
			'type' => '',
			'selectedFields' => '*',
			'table' => '',
			'update' => '',
			'insert' => '',
			'condition' => [],
			'limit' => '',
			'offset' => ''
		];
	}

	/**
	 * Initialized condition for SELECT statement
	 *
	 * @param array $Condition List of conditions (will be combined by AND)
	 */
	private function __selectQuery($Condition){
		$this->__Command['type'] = 'SELECT';
		$this->__Command['table'] = 'FROM ' . $this->__Command['table'];

		if (!empty($Condition)){
			foreach($Condition as $field => $val){
				$this->addCondition($field, $val, '=', 'AND');
			}
		}
	}

	/**
	 * Initialized condition for INSERT statement
	 *
	 * @param array $Data Array with data which will be inserted (keys - field names)
	 */
	private function __insertQuery($Data){
		$this->__Command['type'] = 'INSERT';
		$this->__Command['table'] = 'INTO ' . $this->__Command['table'];

		foreach($Data as $field => $val){
			$bindParam = $this->__addBind($field, $val);
			$Fields[] = '`' . $field . '`';
			$Values[] = ':' . $bindParam;
		}

		unset($this->__Command['condition']);
		unset($this->__Command['selectedFields']);

		$this->__Command['insert'] = '(' . implode(', ', $Fields) . ') VALUES (' . implode(', ', $Values) . ')';
	}

	/**
	 * Initialized condition for UPDATE statement
	 *
	 * @param array $Data Array with data which will be updated (keys - field names)
	 */
	private function __updateQuery($Data){
		$this->__Command['type'] = 'UPDATE';
		$this->__Command['table'] = $this->__Command['table'];

		foreach($Data as $field => $val){
			$bindParam = $this->__addBind($field, $val);
			$Fields[] = '`' . $field . '` = :' . $bindParam;
		}

		unset($this->__Command['selectedFields']);

		$this->__Command['update'] = 'SET ' . implode(', ', $Fields);
	}

	/**
	 * Generate string for MySQL condition with IN operator
	 *
	 * @param string $field
	 * @param array  $Values
	 * @param string $concat
	 *
	 * @return string
	 * @throws \Exception
	 */
	private function __makeInCondition($field, $Values, $concat){
		if (!is_array($Values)){
			throw new \Exception('$Values must be array');
		}

		foreach ($Values as $value){
			$bindParam = $this->__addBind($field, $value);
			$in[] = ':' . $bindParam;
		}

		$curConcat = empty($this->__Command['condition']) ? '' : $concat . ' ';

		return $curConcat . $field . ' IN (' . implode(', ', $in) . ')';
	}

	/**
	 * @param $field
	 * @param $value
	 *
	 * @return string
	 */
	private function __addBind($field, $value){
		$this->__Binds[$field . '_' . $this->__bindNum] = $value;
		$bindParam = $field . '_' . $this->__bindNum;
		$this->__bindNum++;

		return $bindParam;
	}

	/**
	 * Generate MySQL statement LIMIT {offset},{limit}
	 *
	 * @return string
	 */
	private function __makeLimit(){
		if (empty($this->__Command['limit'])){
			return '';
		}

		$this->__Command['offset'] = !empty($this->__Command['offset']) ? (int) $this->__Command['offset'] : 0;
		$limit = ' LIMIT ' . $this->__Command['offset'] . ',' . $this->__Command['limit'];

		unset($this->__Command['offset']);
		unset($this->__Command['limit']);

		return $limit;
	}
}