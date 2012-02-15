<?php
  
    /****************************************************************/
    /* Pancake                                                      */
    /* requestWorkerController.thread.php                           */
    /* 2012 Yussuf "pp3345" Khalil                                  */
    /* License: http://creativecommons.org/licenses/by-nc-sa/3.0/   */
    /****************************************************************/
    
    if(PANCAKE_HTTP !== true)
        exit;
        
    // Set user and group
    Pancake_setUser();
        
    while($message = Pancake_IPC::get()) {
        $requestWorkers[$message->id] = $message;
        Pancake_SharedMemory::put($requestWorkers, PANCAKE_REQUEST_WORKER_TYPE.'0001');
    }
?>
