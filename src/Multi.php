<?php
namespace OOCURL;

/**
 * Executes multiple sessions in parallel.
 *
 * @package OOCURL
 */
class Multi
{
    /**
     * @var resource
     */
    private $multiHandle;

    /**
     * @var int
     */
    private $running = 0;

    /**
     * @var int|null
     */
    private $status = null;

    public function __construct()
    {
        $this->multiHandle = curl_multi_init();
    }

    /**
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function getNumRunning()
    {
        return $this->running;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->multiHandle;
    }

    /**
     * @param Session $session
     * @return int
     */
    public function addSession(Session $session)
    {
        return curl_multi_add_handle($this->multiHandle, $session->getResource());
    }

    /**
     * Close multi session.
     */
    public function close()
    {
        curl_multi_close($this->multiHandle);
    }

    /**
     * @return int|null
     */
    public function exec()
    {
        do {
            $this->status = curl_multi_exec($this->multiHandle, $this->running);
        } while (CURLM_CALL_MULTI_PERFORM === $this->status);

        return $this->status;
    }

    /**
     * @param float $timeout
     * @return int
     */
    public function select($timeout = 1.0)
    {
        $res = curl_multi_select($this->multiHandle, $timeout);
        return $res;
    }

    /**
     * Attempt to get a result instance. May be called multiple times.
     *
     * @return null|Multi\Result
     */
    public function fetchResult()
    {
        $info = curl_multi_info_read($this->multiHandle);
        if ($info) {
            return new Multi\Result($info['msg'], $info['result'], new Session($info['handle']));
        }
        return null;
    }

    /**
     * @param float $timeout the timeout applied to the select which may be called multiple times.
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

        } while ($this->getNumRunning() > 0);

        return $results;
    }
}
