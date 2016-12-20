<?php
namespace Lib\Model;

use Kerisy\Database\Configuration;
use Kerisy\Database\PGDriver;
use \Kerisy\Database\Connection;

/**
 * Created by PhpStorm.
 * User: haoyanfei
 * Date: 16/6/3
 * Time: 下午1:49
 *
 *  SELECT select_list
 *   FROM table_expression
 *   [ ORDER BY ... ]
 *   [ LIMIT { number | ALL } ] [ OFFSET number ]
 *
 */
class PGModel extends Connection
{
    public $connection;
    public $driver;
    public $configure;
    protected $table;
    protected $debug = true;
    public $model;

    public function __construct()
    {
        $this->setDatabaseConfigure();
        $configure = new Configuration($this->debug);
        $configure->setParameters($this->configure);

        parent::__construct(new PGDriver(), $configure);
    }

    public function setDatabaseConfigure()
    {
        $this->configure = config('database')->get('pgsql');
//        $this->configure = [
//            'host' => 'localhost',
//            'dbname' => 'putao_store',
//            'port' => 5432,
//            'username' => 'postgres',
//            'password' => '1',
//            'prefix' => 'mall_',
//            'charset' => 'utf8',

//            'host' => 'localhost',
//            'dbname' => 'putao_mall',
//            'port' => 3306,
//            'username' => 'root',
//            'password' => '123456',
//            'prefix' => 'mall_',
//            'charset' => 'utf8',
//        ];
    }


}