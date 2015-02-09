<?php
namespace OOCURL;

class Multi
{
    private $multiHandle;

    private $running = false;

    private $status = null;

    public function __construct()
    {
        $this->multiHandle = curl_multi_init();
    }

    public function getStatusCode()
    {
        return $this->status;
    }

    public function isRunning()
    {
        return $this->running;
    }

    public function getResource()
    {
        return $this->multiHandle;
    }

    public function addHandle(SessionHandle $sessionHandle)
    {
        return curl_multi_add_handle($this->multiHandle, $sessionHandle->getResource());
    }

    public function close()
    {
        curl_multi_close($this->multiHandle);
    }

    public function exec()
    {
        do {
            $this->status = curl_multi_exec($this->multiHandle, $this->running);
        } while (CURLM_CALL_MULTI_PERFORM === $this->status);

        return $this->status;
    }

    public function select($timeout = 1.0)
    {
        $res = curl_multi_select($this->multiHandle, $timeout);
        return $res;
    }

    public function fetchResult()
    {
        $info = curl_multi_info_read($this->multiHandle);
        if ($info) {
            return new Multi\Result($info['msg'], $info['result'], new SessionHandle($info['handle']));
        }
        return false;
    }

    /**
     * @param float $timeout
     * @return array
     */
    public function fetchAllResults($timeout = 1.0)
    {
        $results = array();
        do {
            //start/continue...
            if (CURLE_OK !== ($code = $this->exec())) {
                throw new \RuntimeException('Curl exec failed with code '.$code);
            }

            //block... or don't. Who knows?!
            $sel = $this->select($timeout);

            switch (true) {
                case ($sel == -1):
                    usleep(10); //failed... j/k try again later
                    break;
                case ($sel === 0):
                    return $results; //timeout
            }

            //it doesn't matter what curl says. The result might or might not be available so always check.
            while ($result = $this->fetchResult()) {
                $results[] = $result;
            }

        } while ($this->running > 0);

        return $results;
    }
}
