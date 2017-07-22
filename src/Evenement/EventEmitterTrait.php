<?php declare(strict_types=1);

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evenement;

trait EventEmitterTrait
{
    protected $listeners = [];
    protected $onceListeners = [];

    public function on(string $event, callable $listener)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    public function once(string $event, callable $listener)
    {
        if (!isset($this->onceListeners[$event])) {
            $this->onceListeners[$event] = [];
        }

        $this->onceListeners[$event][] = $listener;

        return $this;
    }

    public function removeListener(string $event, callable $listener)
    {
        if (isset($this->listeners[$event])) {
            $index = \array_search($listener, $this->listeners[$event], true);
            if (false !== $index) {
                unset($this->listeners[$event][$index]);
                if (\count($this->listeners[$event]) === 0) {
                    unset($this->listeners[$event]);
                }
            }
        }

        if (isset($this->onceListeners[$event])) {
            $index = \array_search($listener, $this->onceListeners[$event], true);
            if (false !== $index) {
                unset($this->onceListeners[$event][$index]);
                if (\count($this->onceListeners[$event]) === 0) {
                    unset($this->onceListeners[$event]);
                }
            }
        }
    }

    public function removeAllListeners(string $event = null)
    {
        if ($event !== null) {
            unset($this->listeners[$event]);
        } else {
            $this->listeners = [];
        }

        if ($event !== null) {
            unset($this->onceListeners[$event]);
        } else {
            $this->onceListeners = [];
        }
    }

    public function listeners(string $event): array
    {
        return array_merge(
            isset($this->listeners[$event]) ? $this->listeners[$event] : [],
            isset($this->onceListeners[$event]) ? $this->onceListeners[$event] : []
        );
    }

    public function emit(string $event, array $arguments = [])
    {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $listener(...$arguments);
            }
        }

        if (isset($this->onceListeners[$event])) {
            $keys = array_keys($this->onceListeners[$event]);
            foreach ($keys as $key) {
                ($this->onceListeners[$event][$key])(...$arguments);
                unset($this->onceListeners[$event][$key]);
            }

            if (count($this->onceListeners[$event]) === 0) {
                unset($this->onceListeners[$event]);
            }
        }
    }
}
