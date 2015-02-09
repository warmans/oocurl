<?php
namespace OOCURL;

/**
 * Single curl session
 *
 * @package OOCURL
 */
class Session
{
    /**
     * @var resource
     */
    private $handle;

    public function __construct($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @param $uri
     * @param array $options
     *
     * @return Session
     */
    public static function create($uri, array $options = array())
    {
        $handle = new static(curl_init($uri));
        $handle->setOptions($options);
        return $handle;
    }

    /**
     * @param int $name
     * @param mixed $value
     * @return bool
     */
    public function setOption($name, $value)
    {
        if (defined($name)) {
            throw new \RuntimeException('Unknown option was specified: ' . $name);
        }

        return curl_setopt($this->handle, $name, $value);
    }

    /**
     * @param array $options
     * @return bool
     */
    public function setOptions(array $options)
    {
        if (false === curl_setopt_array($this->handle, $options)) {
            throw new \RuntimeException('At least one invalid option was specified');
        }
        return true;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->handle;
    }

    /**
     * close this session
     */
    public function close()
    {
        curl_close($this->handle);
    }
}
