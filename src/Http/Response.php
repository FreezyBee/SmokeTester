<?php
declare(strict_types=1);

namespace FreezyBee\SmokeTester\Http;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Response extends \Nette\Http\Response
{
    /** @var string */
    private $content = '';

    /** @var array */
    private $headers = [];

    /**
     * @param $name
     * @param $value
     * @return self
     */
    public function setHeader($name, $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        $code = $this->getCode();
        return $code >= 200 && $code < 400;
    }

    /**
     * @return bool
     */
    public function isRedirected(): bool
    {
        $code = $this->getCode();
        return $code >= 300 && $code < 400;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->headers['Location'] ?? null;
    }
}
