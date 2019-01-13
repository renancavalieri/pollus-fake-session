<?php

/**
 * Fake Session
 * @license https://opensource.org/licenses/MIT MIT
 * @author Renan Cavalieri <renan@tecdicas.com>
 */

namespace Pollus\FakeSession;

use Pollus\SessionWrapper\SessionInterface;

/**
 * Fake session object, intended to be used on tests
 */
class FakeSession implements SessionInterface
{
    /**
     * @var null|array
     */
    public $current_session_data;
    
    /**
     * @var null|array
     */
    public $session;
    
    /**
     * @var bool
     */
    public $started = false;
    
    /**
     * @var string
     */
    public $id;
    
    /**
     * @var string
     */
    public $name = "PHPFAKESESSION";
    
    /**
     * {@inheritDoc}
     */
    public function abort(): bool
    {
        $this->session = $this->current_session_data;
        $this->started = false;
    }

    /**
     * {@inheritDoc}
     */
    public function commit(): bool
    {
        $this->current_session_data = $this->session;
        $this->started = false;
    }

    /**
     * {@inheritDoc}
     */
    public function createId(?string $prefix = null): string
    {
        if ($prefix !== null)
        {
            return $prefix . md5(uniqid(rand(), true));
        }
        return md5(uniqid(rand(), true));
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(): bool
    {
        if ($this->started === false)
        {
            trigger_error("Trying to destroy uninitialized session", E_USER_WARNING);
        }
        
        $this->started = false;
        $this->session = null;
        $this->current_session_data = null;
        $this->id = null;
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc(): bool
    {
       return true;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        if ($this->has($key))
        {
            return $this->session[$key];
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function has($key): bool
    {
        if (isset($this->session[$key]))
        {
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function id(?string $session_id = null): string
    {
        if ($session_id !== null)
        {
            return $this->id = $session_id;
        }
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function name(string $name = null): string
    {
        if ($name !== null && $this->started)
        {
            trigger_error("Cannot change session name when session is active", E_USER_WARNING);
        }
        else if ($name !== null)
        {
            $this->name = $name;
        }
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function regenerateId(bool $delete_old_session = false): bool
    {
        $this->id($this->createId());
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): bool
    {
        $this->session = $this->current_session_data;
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setCookieParams(int $lifetime, ?string $path = null, ?string $domain = null, bool $secure = false, bool $httponly = false)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function start(array $options = array()): bool
    {
        if ($this->started === true)
        {
            trigger_error("A session had already been started - ignoring", E_USER_NOTICE);
        }
        $this->started = true;
        
        if ($this->id === null)
        {
            $this->id = $this->createId();
        }
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function status(): int
    {
        if ($this->started)
        {
            return PHP_SESSION_ACTIVE;
        }
        return PHP_SESSION_NONE;
    }

    /**
     * {@inheritDoc}
     */
    public function unset(): bool
    {
        $this->session = null;
    }
}
