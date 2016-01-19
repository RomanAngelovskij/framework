<?php
namespace FM\db\drivers;

interface DBDriverInterface{

	public function createCommand($command, $Params = []);

	public function useTable($tableName);

	public function addCondition($field, $value, $sign, $concat);

	public function addConditionBundle($bundleConcat, $Conditions, $concat);

	public function execute();

	public function get();
}