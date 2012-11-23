<?php

class SDatabaseQueryBuilderMongo extends SDatabaseQueryBuilderAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'update_operators' => array(
				'add'          => true,
				'unset'        => true,
				'append'       => true,
				'merge'        => true,
				'extractFirst' => true,
				'extractLast'  => true,
				'deleteField'  => true,
				'pull'         => true,
			),
			'condition_operators' => array(
				'exists'     => true,
				'missing'    => '_conditionMissing',
				'modulo'     => true,
				'size'       => true,
				'type'       => true,
				'hasAll'     => true,
				'notIn'      => true,
			),
		));

		parent::_initialize($config);

		// Override the default logic operators
		$config->logic_operators = array(
			'openAnd' => '_logicOpenAnd',
			'openOr'  => '_logicOpenOr',
			'close'   => '_logicClose'
		);

		// Remove LIKE
		unset($config->condition_operators->like);
	}

	protected function _conditionMissing($condition)
	{
		$condition->operator = 'exists';
		$condition->value = false;
	}

	protected function _logicOpenAnd()
	{
		$condition = $this->_newCondition();
		$condition->open = true;
		$condition->conditions = new ArrayIterator();
		$condition->logic = 'and';
	}

	protected function _logicOpenOr()
	{
		$condition = $this->_newCondition();
		$condition->open = true;
		$condition->conditions = new ArrayIterator();
		$condition->logic = 'or';
	}
}