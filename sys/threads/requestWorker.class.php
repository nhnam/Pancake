<?php

    /****************************************************************/
    /* Pancake                                                      */
    /* requestWorker.class.php                                      */
    /* 2012 - 2013 Yussuf Khalil                                    */
    /* See LICENSE file for license information                     */
    /****************************************************************/

    namespace Pancake;

    if(PANCAKE !== true)
        exit;

    /**
    * A request worker processes requests.
    */
    class RequestWorker extends Thread {
        static private $instances = 0;
        static private $codeProcessed = false;
        public $id = 0;
        public $socket = null;
        public $socketName = "";
        public $localSocket = null;

        /**
        * Creates a new RequestWorker
        *
        * @return RequestWorker
        */
        public function __construct() {
        	if(!self::$codeProcessed) {
        		$hash = md5(serialize(Config::get('main'))
        				. serialize((array) Config::get('moody'))
        				. serialize(Config::get('vhosts'))
        				. serialize(Config::get('fastcgi'))
        				. serialize(Config::get('ajp13'))
                        . serialize(Config::get('tls'))
        				. md5_file('vHostInterface.class.php')
        				. md5_file('threads/single/requestWorker.thread.php')
        				. md5_file('natives/Moody/' . \PHP_MAJOR_VERSION . \PHP_MINOR_VERSION . '.cphp')
        				. md5_file('FastCGI.class.php')
        				. md5_file('workerFunctions.php')
        				. md5_file('authenticationFile.class.php')
        				. md5_file('AJP13.class.php')
        				. \PHP_MINOR_VERSION
        				. \PHP_RELEASE_VERSION
        				. VERSION
						. DEBUG_MODE);
        		if(!(file_exists('compilecache/requestWorker.thread.hash')
        		&& file_get_contents('compilecache/requestWorker.thread.hash') == $hash)) {
        			require_once 'threads/codeProcessor.class.php';

        			$codeProcessor = new CodeProcessor('threads/single/requestWorker.thread.php', 'compilecache/requestWorker.thread.cphp');
	        		$codeProcessor->run();
	        		file_put_contents('compilecache/requestWorker.thread.hash', $hash);
					unset($codeProcessor);
        		}
        		self::$codeProcessed = true;
        		unset($hash);
        	}

            // Add instance
            $this->id = self::$instances++;

            $this->doGracefulExit = true;

            $this->socketName = Config::get('main.tmppath') . mt_rand() . "_rworker_local";
            if(strlen($this->socketName) > 107) {
                $this->socketName = '/tmp/' . mt_rand() . "_rworker_panso";
            }
            
            $this->socket = Socket(\AF_UNIX, \SOCK_STREAM, 0);
            Bind($this->socket, \AF_UNIX, $this->socketName);
            Listen($this->socket);
            $this->localSocket = Socket(\AF_UNIX, \SOCK_STREAM, 0);
            Connect($this->localSocket, \AF_UNIX, $this->socketName);
			SetBlocking($this->localSocket, false);
            $socket = Accept($this->socket);
            Close($this->socket);
            $this->socket = $socket;

            $this->codeFile = 'compilecache/requestWorker.thread.cphp';
            $this->friendlyName = 'RequestWorker #' . ($this->id + 1);
        }

        public function __destruct() {
            Close($this->socket);
            Close($this->localSocket);
        }
    }
?>
