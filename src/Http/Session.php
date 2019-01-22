<?php
declare(strict_types=1);

namespace FreezyBee\SmokeTester\Http;

use Iterator;
use SessionHandlerInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Session extends \Nette\Http\Session
{
    /** @var bool */
    private $initialized = false;

    /** @var string */
    private $name = '';

    /** @var SessionSection[] */
    private $sessionSections = [];

    /**
     * {@inheritdoc}
     */
    public function start(): void
    {
        $this->initialized = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted(): bool
    {
        return $this->initialized;
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        $this->initialized = false;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        $this->sessionSections = [];
        $this->initialized = false;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateId(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return random_bytes(20);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSection($section, $class = SessionSection::class): SessionSection
    {
        return $this->sessionSections[$section] ?? $this->sessionSections[$section] = new $class($this, $section);
    }

    /**
     * {@inheritdoc}
     */
    public function hasSection($section): bool
    {
        return !empty($this->sessionSections[$section]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Iterator
    {
        return new \ArrayIterator($this->sessionSections);
    }

    /**
     * {@inheritdoc}
     */
    public function clean(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): self
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiration($time): self
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCookieParameters($path, $domain = null, $secure = null, $samesite = null): self
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParameters(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setSavePath($path): self
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHandler(SessionHandlerInterface $handler): self
    {
        return $this;
    }
}
