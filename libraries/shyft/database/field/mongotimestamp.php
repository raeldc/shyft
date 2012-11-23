<?php

class SDatabaseFieldMongotimestamp extends SDatabaseFieldMongodate
{
	protected function _process($value, KDatabaseRowAbstract $row)
	{
		if ($value instanceof MongoDate)
		{
			list($usec, $time) = explode(' ', $value);
			return strftime('%F %T', $time);
		}

		return $value;
	}
}
