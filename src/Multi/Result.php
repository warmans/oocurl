<?php
namespace OOCURL\Multi;

use OOCURL\Session;

/**
 * Result of a multi exec. Can be used to obtain the result body.
 *
 * @package OOCURL\Multi
 */
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
     * @var Session
     */
    private $session;

    /**
     * @param int $msg
     * @param int $result
     * @param Session $session
     */
    public function __construct ($msg, $result, Session $session)
    {
        $this->msg = $msg;
        $this->result = $result;
        $this->session = $session;
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
        return curl_multi_getcontent($this->session->getResource());
    }

    public function close()
    {
        $this->session->close();
    }
}
