<?php
declare(strict_types=1);

namespace FreezyBee\SmokeTester\Http;

use Iterator;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SessionSection extends \Nette\Http\SessionSection
{
    /** @var array */
    protected $mockData = [];

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Iterator
    {
        return new \ArrayIterator($this->mockData);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value): void
    {
        $this->mockData[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function &__get($name)
    {
        if ($this->warnOnUndefined && !array_key_exists($name, $this->mockData)) {
            trigger_error("The variable '$name' does not exist in session section");
        }

        return $this->mockData[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($name): bool
    {
        return isset($this->mockData[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function __unset($name): void
    {
        unset($this->mockData[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiration($time, $variables = null): self
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeExpiration($variables = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function remove(): void
    {
        $this->mockData = [];
    }
}
