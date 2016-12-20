<?php

namespace Lib\Support\Ptldap\Connections;

use Lib\Support\Ptldap\Interfaces\ConnectionInterface;

/**
 * The LDAP Connection.
 *
 * Class LDAP
 */
class Ldap implements ConnectionInterface
{

    /**
     * Stores the bool to tell the connection
     * whether or not to use SSL.
     *
     * To use SSL, your server must support LDAP over SSL.
     * http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl
     *
     * @var bool
     */
    protected $useSSL = false;
	
	/**
     * Stores the bool whether or not
     * the current connection is bound.
     *
     * @var bool
     */
    protected $bound = false;
	
	/**
     * Stores the bool whether or not
     * to suppress errors when calling
     * LDAP methods.
     *
     * @var bool
     */
    protected $suppressErrors = true;

    /**
     * The current LDAP connection.
     *
     * @var resource
     */
    protected $connection;

    /**
     * Returns the current LDAP connection.
     *
     * @return resource
     */
    public function getConnection()
    {
        return $this->connection;
    }
	
	/**
     * Returns true / false if the
     * current connection instance is
     * bound.
     *
     * @return bool
     */
    public function isBound()
    {
        return $this->bound;
    }

    /**
     * Connects to the specified hostname
     * using the PHP ldap protocol.
     *
     * @param string $hostname
     * @param string $port
     *
     * @return resource
     */
    public function connect($hostname, $port = '389')
    {
        $protocol = $this::PROTOCOL;

        if ($this->useSSL)
        {
            $protocol = $this::PROTOCOL_SSL;
        }

        return $this->connection = ldap_connect($protocol.$hostname, $port);
    }

    /**
     * Closes the current LDAP connection if
     * it exists.
     *
     * @return bool
     */
    public function close()
    {
        $connection = $this->getConnection();

        if ($connection)
        {
            ldap_close($connection);
        }

        return true;
    }

    /**
     * Performs a search on the current connection
     * with the specified distinguished name, filter
     * and fields.
     *
     * @param string $dn
     * @param string $filter
     * @param array  $fields
     *
     * @return resource
     */
    public function search($dn, $filter, array $fields)
    {
        if ($this->suppressErrors)
        {
            return @ldap_search($this->getConnection(), $dn, $filter, $fields);
        }

        return ldap_search($this->getConnection(), $dn, $filter, $fields);
    }
	
	/**
     * Binds to the current LDAP connection. If SASL
     * is true, we'll set up a SASL bind instead.
     *
     * @param string $username
     * @param string $password
     * @param bool   $sasl
     *
     * @return bool
     */
    public function bind($username, $password, $sasl = false)
    {
        if ($sasl)
        {
            if ($this->suppressErrors)
            {
                return $this->bound = @ldap_sasl_bind($this->getConnection(), null, null, 'GSSAPI');
            }

            return $this->bound = ldap_sasl_bind($this->getConnection(), null, null, 'GSSAPI');
        }
        else
        {
            if ($this->suppressErrors)
            {
                return $this->bound = @ldap_bind($this->getConnection(), $username, $password);
            }

            return $this->bound = ldap_bind($this->getConnection(), $username, $password);
        }
    }
	
	/**
     * Sets an option and value on the current
     * LDAP connection.
     *
     * @param int   $option
     * @param mixed $value
     *
     * @return bool
     */
    public function setOption($option, $value)
    {
        if ($this->suppressErrors)
        {
            return @ldap_set_option($this->getConnection(), $option, $value);
        }

        return ldap_set_option($this->getConnection(), $option, $value);
    }
	
	/**
     * Retrieves and returns the results of an
     * LDAP search into an array format.
     *
     * @param $searchResults
     *
     * @return array
     */
    public function getEntries($searchResults)
    {
        if ($this->suppressErrors)
        {
            return @ldap_get_entries($this->getConnection(), $searchResults);
        }
        else
        {
            return ldap_get_entries($this->getConnection(), $searchResults);
        }
    }
	
	/**
     * Returns the last error from
     * the current LDAP connection.
     *
     * @return string
     */
    public function getLastError()
    {
        if ($this->suppressErrors) {
            return @ldap_error($this->getConnection());
        }

        return ldap_error($this->getConnection());
    }

}
