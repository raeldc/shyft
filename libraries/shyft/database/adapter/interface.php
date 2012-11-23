<?php

interface SDatabaseAdapterInterface
{
	/**
	 * Get the connection
	 *
	 * Provides access to the underlying database connection. Useful for when
	 * you need to call a proprietary method such as postgresql's lo_* methods
	 *
	 * @return resource
	 */
	public function getConnection();

	/**
	 * Set the connection
	 *
	 * @param 	resource 	The connection resource
	 * @return  KDatabaseAdapterAbstract
	 */
	public function setConnection($resource);

	/**
	 * @TODO: Standardize it to SELECT instead of FIND.
     * Preform a select query.
     *
     * @param	string  	A full SQL query to run. Data inside the query should be properly escaped.
     * @param	integer 	The result maode, either the constant KDatabase::RESULT_USE or KDatabase::RESULT_STORE
     * 						depending on the desired behavior. By default, KDatabase::RESULT_STORE is used. If you
     * 						use KDatabase::RESULT_USE all subsequent calls will return error Commands out of sync
     * 						unless you free the result first.
     * @param 	string 		The column name of the index to use.
     * @return  mixed 		If successfull returns a result object otherwise FALSE
     */
	public function find($query, $mode = KDatabase::RESULT_STORE, $key = '');

	/**
     * Insert a row of data into a table.
     *
     * @param KDatabaseQueryInsert The query object.
     * @return bool|integer  If the insert query was executed returns the number of rows updated, or 0 if
     * 					     no rows where updated, or -1 if an error occurred. Otherwise FALSE.
     */
	public function insert(KDatabaseQueryInsert $query);

	/**
     * Update a table with specified data.
     *
     * @param  KDatabaseQueryUpdate The query object.
     * @return integer  If the update query was executed returns the number of rows updated, or 0 if
     * 					no rows where updated, or -1 if an error occurred. Otherwise FALSE.
     */
	public function update(KDatabaseQueryUpdate $query);

	/**
     * Delete rows from the table.
     *
     * @param  KDatabaseQueryDelete The query object.
     * @return integer 	Number of rows affected, or -1 if an error occured.
     */
	public function delete(KDatabaseQueryDelete $query);
}