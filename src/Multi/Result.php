<?php
namespace OOCURL\Multi;

use OOCURL\SessionHandle;

class Result
{
    /**
     * @var int
     */
    private $msg;

    /**
     * @var int
     */
    private $result;

    /**
     * @var SessionHandle
     */
    private $sessionHandle;

    /**
     * @param int $msg
     * @param int $result
     * @param SessionHandle $handle
     */
    public function __construct ($msg, $result, SessionHandle $handle)
    {
        $this->msg = $msg;
        $this->result = $result;
        $this->sessionHandle = $handle;
    }

    public function isResultOk()
    {
        return ($this->result === CURLE_OK);
    }

    public function getMsgCode()
    {
        return $this->msg;
    }

    public function getResultCode()
    {
        return $this->result;
    }

    public function getBody()
    {
        return curl_multi_getcontent($this->sessionHandle->getResource());
    }

    public function close()
    {
        $this->sessionHandle->close();
    }
}
