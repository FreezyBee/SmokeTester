<?php
declare(strict_types=1);

namespace FreezyBee\SmokeTester\Http;

use Nette\Http\UrlScript;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Request extends \Nette\Http\Request
{
    /** @var string */
    private $mockRawBody;

    /**
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param string $rawBody
     */
    public function __construct(string $method, string $uri, array $headers = [], string $rawBody = '')
    {
        $url = new UrlScript($uri);
        $url->setScriptPath('/');
        $this->mockRawBody = $rawBody;

        parent::__construct($url, null, null, null, null, $headers, $method);
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setRawBody(string $content): self
    {
        $this->mockRawBody = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getRawBody(): string
    {
        return $this->mockRawBody;
    }
}
