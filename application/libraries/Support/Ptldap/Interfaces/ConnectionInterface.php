<?php

namespace Lib\Support\Ptldap\Interfaces;

/**
 * The Connection interface used for making
 * connections. Implementing this interface
 * on connection classes helps unit and functional
 * testing classes that require a connection.
 *
 * Interface ConnectionInterface
 */
interface ConnectionInterface
{
    /**
     * The SSL LDAP protocol string.
     *
     * @var string
     */
    const PROTOCOL_SSL = 'ldaps://';

    /**
     * The non-SSL LDAP protocol string.
     *
     * @var string
     */
    const PROTOCOL = 'ldap://';

    /**
     * The LDAP SSL Port number.
     *
     * @var string
     */
    const PORT_SSL = '636';

    /**
     * The non SSL LDAP port number.
     *
     * @var string
     */
    const PORT = '389';

    /**
     * Get the current connection.
     *
     * @return mixed
     */
    public function getConnection();
	
	/**
     * Returns true / false if the current
     * connection is bound.
     *
     * @return bool
     */
    public function isBound();

    /**
     * Connects to the specified hostname using the
     * specified port.
     *
     * @param string $hostname
     * @param int    $port
     *
     * @return mixed
     */
    public function connect($hostname, $port = 389);

    /**
     * Binds to the current connection using
     * the specified username and password. If sasl
     * is true, the current connection is bound using
     * SASL.
     *
     * @param string $username
     * @param string $password
     * @param bool   $sasl
     *
     * @return mixed
     */
    public function bind($username, $password, $sasl = false);

    /**
     * Closes the current connection.
     *
     * @return mixed
     */
    public function close();

    /**
     * @param string $dn
     * @param string $filter
     * @param array  $fields
     *
     * @return mixed
     */
    public function search($dn, $filter, array $fields);
	
	/**
     * Sets an option on the current connection.
     *
     * @param int   $option
     * @param mixed $value
     *
     * @return mixed
     */
    public function setOption($option, $value);
	
	/**
     * Retrieve the entries from a search result.
     *
     * @param $searchResult
     *
     * @return mixed
     */
    public function getEntries($searchResult);
	
	/**
     * Retrieve the last error on the current
     * connection.
     *
     * @return string
     */
    public function getLastError();

}
