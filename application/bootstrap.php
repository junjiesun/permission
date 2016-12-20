<?php
/**
 *
 *
 * @author          Kaihui Wang <hpuwang@gmail.com>
 * @copyright      (c) 2015 putao.com, Inc.
 * @since           16/5/31
 */
defined('APPLICATION_PATH') || define('APPLICATION_PATH', __DIR__ . '/');
defined('CONFIG_PATH') || define('CONFIG_PATH', APPLICATION_PATH . '/config/');
defined('ADMIN_GROUP') || define('ADMIN_GROUP', 1);
defined('PUBLIC_GROUP') || define('PUBLIC_GROUP', 2);

//defined('KERISY_ENV') || define('KERISY_ENV', getenv('KERISY_ENV')?:'production');
defined('KERISY_ENV') || define('KERISY_ENV', getenv('KERISY_ENV')?:'development');
//defined('KERISY_ENV') || define('KERISY_ENV', getenv('KERISY_ENV')?:'test');

defined('CLI_MODE') || define('CLI_MODE', PHP_SAPI === 'cli' );

require_once APPLICATION_PATH . "hook.php";

$app = CLI_MODE && in_array($_SERVER['argv'][1],['server','phpcs','jobserver','monitorserver']) ? new Kerisy\Core\Application\Web() :  new Kerisy\Core\Application\Console();

return $app;