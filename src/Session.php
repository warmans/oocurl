<?php
namespace OOCURL;

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

    public static function create($uri, array $options = array())
    {
        $handle = new static(curl_init($uri));
        $handle->setOptions($options);
        return $handle;
    }

    public function setOption($name, $value)
    {
        if (defined($name)) {
            throw new \RuntimeException('Unknown option was specified: ' . $name);
        }

        return curl_setopt($this->handle, $name, $value);
    }

    public function setOptions($options)
    {
        if (false === curl_setopt_array($this->handle, $options)) {
            throw new \RuntimeException('At least one invalid option was specified');
        }
        return true;
    }

    public function getResource()
    {
        return $this->handle;
    }

    public function close()
    {
        curl_close($this->handle);
    }
}
