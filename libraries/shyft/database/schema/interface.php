<?php

interface SDatabaseSchemaInterface
{
	public function getField($field);

	public function addField($field, $config = null);

	public function removeField($field);

	public function hasField($field);

	public function getFields();

	public function getUniqueFields();

	public function getFieldMap();
}