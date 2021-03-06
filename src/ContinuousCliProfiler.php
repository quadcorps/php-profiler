<?php

namespace Xhgui\Profiler;

use MongoId;
use Xhgui\Profiler\Saver\MongoSaver;

/**
 * Class ContinuousCliProfiler
 *
 * This profiler can be used to profile CLI PHP scripts that runs continuously i.e. in a while(true) loop
 *
 * To ensure that XHGui stores the data correctly we need to manipluate some of the data:
 *
 * 1. Reset the request times in $_SERVER
 * 2. Process the profile data to remove dots (.) in the keys
 * 3. Generate a unique id for the document to save each profile as a new record
 *
 *
 * @package Xhgui\Profiler
 * @author Andrew Kew (andrew@quadcorps.co.uk)
 */
class ContinuousCliProfiler extends Profiler {
    public function stop()
    {
        $this->setRequestTime($_SERVER);

        $data = $this->disable();

        $data['profile'] = ProfilerHelper::processProfileData($data['profile']);

        $this->generateDocumentKey($data);

        $this->save($data);

        return $data;
    }

    /**
     * This private method resets the request time to current time.
     * The reason this is needed is because if you are running a script in a loop continuously
     * and want to profile this script in the loop then you need to reset the request time on every iteration
     *
     * @param $server array This is the $_SERVER PHP global variable
     */
    private function setRequestTime(array &$server) {
        //need to adjust request times because with php script there is a single request only
        $server['REQUEST_TIME'] = time();
        $server['REQUEST_TIME_FLOAT'] = microtime(true);
    }

    /**
     * This private method is used to generate a new id (primary key) for MongoDB saver only
     * This is so that every iteration gets inserted into MongoDB because they need to be unique
     * to create a new document for each iteration
     *
     * @param array $data
     */
    private function generateDocumentKey(array &$data) {
        if ($this->getSaver() instanceof MongoSaver) {
            //reset id every time else we dont insert a new record
            $data['_id'] = new MongoId();
        }
    }
}