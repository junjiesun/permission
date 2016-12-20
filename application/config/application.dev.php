<?php
return [
    /*------------------------------------------------------------------
     * The Application Name
     *------------------------------------------------------------------
     *
     * Here you specifies the name of your application.
     *
     */

    'name' => 'Kerisy Application',

    /*------------------------------------------------------------------
     * Application Timezone
     *------------------------------------------------------------------
     *
     * Specify the default timezone for your application, which will be
     * used by the PHP date and data-time functions.
     *
     */

    'timezone' => 'Asia/Shanghai',

    /*------------------------------------------------------------------
     * Application Debug Mode
     * -----------------------------------------------------------------
     *
     * When debug mode is enabled, detailed error message with stack traces
     * will be shown on every error in your application, you should disable
     * this in production environment.
     *
     */

    'debug' => true,

    /*------------------------------------------------------------------
     * Application Environment
     * -----------------------------------------------------------------
     *
     * Specify what is the environment your application is running on.
     * This config now is mainly used for testing purpose, in unit tests,
     * PHPUnit will set environment to `test` automatically.
     *
     * value: development / test / production
     */

    'environment' => 'test',

    /*------------------------------------------------------------------
     * Application Runtime Path
     *-----------------------------------------------------------------
     *
     * Specify the runtime path that used to store generated temporary
     * files, you should make sure the directory is writable by Kerisy
     * processes.
     *
     */

    'runtime' => APPLICATION_PATH . 'runtime',
    
    'modules' => ['Core', 'User']
];
