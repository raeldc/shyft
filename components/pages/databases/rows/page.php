<?php

class ComPagesDatabaseRowPage extends SDatabaseRowDefault
{
	public function save()
	{
		$type          = clone $this->getIdentifier();
		$type->package = 'content';
		$type->name    = 'types';

		$type = $this->getService($type)
			->setData(array('type' => $this->type))
			->load();

		// @TODO: Remove dependency to MongoDBRef. Refactor handling of relationships
		$this->type = MongoDBRef::create($type->getDocument()->getName(), $type->id);

		return parent::save();
	}
}