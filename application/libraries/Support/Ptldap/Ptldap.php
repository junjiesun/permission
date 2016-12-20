<?php
/*	
 * With reference to https://github.com/adldap/adLDAP
 * 
 * */
namespace Lib\Support\Ptldap;

use Lib\Support\Ptldap\Interfaces\ConnectionInterface;
use Lib\Support\Ptldap\Connections\Ldap;

class Ptldap 
{
	/**
     * Host.
     *
     * @var string
     */
	protected $hostname;
	
	/**
     * Port used to talk to the domain controllers.
     *
     * @var string
     */
	protected $port = ConnectionInterface::PORT;
	
	/**
     * The base dn for your domain.
     *
     * @var string
     */
    protected $baseDn;
	
	/**
     * Holds the current ldap connection.
     *
     * @var ConnectionInterface
     */
    protected $ldapConnection;
	
	/**
     * When a query returns a referral, follow it.
     *
     * @var int
     */
    protected $followReferrals = 0;
	
    /**
     * Optional account with higher privileges for searching
     * This should be set to a domain admin account.
     *
     * @var string
     */
    private $adminUsername = '';
    /**
     * Account with higher privileges password.
     *
     * @var string
     */
    private $adminPassword = '';
	
	/**
     * Constructor.
     *
     * Tries to bind to the AD domain over LDAP or LDAPs
     *
     * @param array $options
     *
     */
    public function __construct(array $options = [])
	{
		// $this->init($options);
	}
	
	/**
     *
     * * LDAP lnitialize
     *
     * @param array $options
     *
     */
	public function init(array $options = [])
	{
		if ( array_key_exists('hostname', $options) )
		{
            $this->hostname = $options['hostname'];
        }
		
		if ( array_key_exists('port', $options) )
		{
            $this->port = $options['port'];
        }
		
		if ( array_key_exists('baseDn', $options) )
		{
            $this->baseDn = $options['baseDn'];
        }
		
		if ( array_key_exists('adminUsername', $options) )
		{
            $this->adminUsername = $options['adminUsername'];
        }
		
		if ( array_key_exists('adminPassword', $options) )
		{
            $this->adminPassword = $options['adminPassword'];
        }
			
		$this->ldapConnection = new Ldap();
		$this->connect();
	}
	
	/**
     * Destructor.
     *
     * Closes the current LDAP connection if it exists.
     */
    public function __destruct()
    {
        if ($this->ldapConnection instanceof ConnectionInterface)
        {
            $this->ldapConnection->close();
        }
    }
	
    /**
     * Connects and Binds to the Domain Controller.
     *
     * @return bool
     *
     */
    public function connect()
    {
        // Create the LDAP connection
        $this->ldapConnection->connect($this->hostname, $this->port);

        // Set the LDAP options
        $this->ldapConnection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->ldapConnection->setOption(LDAP_OPT_REFERRALS, $this->followReferrals);

        return $this->authenticate( $this->adminUsername, $this->adminPassword );
    }

    public function authenticate( $username, $password )
    {
        if ( empty($username) || empty($password) )
		{
			throw new \Exception("Warning: please make sure parameters");
		}

        $this->ldapConnection->bind($username, $password);

        if (!$this->ldapConnection->isBound())
        {
            $error = $this->ldapConnection->getLastError();
			throw new \Exception("LDAP bind error: " . $error);
        }

        return true;
    }

	public function validation( String $username, String $password )
	{
		$isSuccess = false;
		$obj = array(
			'dn' => null,
			'user' => null
		);
		
		if ( empty($this->ldapConnection) )
		{
			throw new \Exception("Error: LDAP service has not created");
		}
		
		if ( empty($username) || empty($password) )
		{
			return $isSuccess;
		}
	 
		$filter = sprintf("(&(objectClass=mailUser)(mail=%s)(accountStatus=active))", $username);
		$fields = array('dn', 'cn', 'mail');								

		$result = $this->ldapConnection->search( $this->baseDn, $filter, $fields );
		$retData = $this->ldapConnection->getEntries( $result );

		array_walk( $retData, function( $value, $key ) use( &$obj )
		{
			if ( !empty($value['dn']) )
			{
				$obj['dn'] = $value['dn'];
				$obj['user'] = array(
					'name' => empty($value['cn'][0]) ?: $value['cn'][0],
					'email' => empty($value['mail'][0]) ?: $value['mail'][0]
				);
			}
		});
		
		if ( $obj['dn'] !== null )
		{
			$isSuccess = $this->ldapConnection->bind( $obj['dn'], $password );
		}

		return array(
			'isSuccess' => (bool)$isSuccess,
			'user' => $isSuccess ? $obj['user'] : array()
		);
	}
	
	public function search()
	{
		
	}
	
	public function all()
	{
		$returnData = array();	
			
		$filter = "(&(objectClass=mailUser)(uid=*)(accountStatus=active))";
		$fields = array('dn', 'cn', 'mail');								

		$result = $this->ldapConnection->search( $this->baseDn, $filter, $fields );
		$retData = $this->ldapConnection->getEntries( $result );
		
		array_walk( $retData, function( $value, $key ) use( &$returnData )
		{
			if ( !empty($value['dn']) )
			{
				$returnData[] = array(
					'name' => empty($value['cn'][0]) ?: $value['cn'][0],
					'email' => empty($value['mail'][0]) ?: $value['mail'][0]
				);
			}
		});
		
		return $returnData;
	}
	
	public function __call(String $method, Array $args)
	{
		return "Didn't find method: " . $method;
	}

	
}
