<?php

namespace Thunk\Verbs\Events;

use Illuminate\Contracts\Container\Container;
use ReflectionMethod;
use Thunk\Verbs\Support\Reflector;

class Bus
{
	protected array $listeners = [];
	
	public function __construct(
		protected Container $container
	) {
	}
	
	public function registerListener(object $listener): void
	{
		foreach (Reflector::getListeners($listener) as $listener) {
			foreach ($listener->events as $event_type) {
				$this->listeners[$event_type][] = $listener;
			}
		}
	}
	
	public function dispatch(Event $event): void
	{
		foreach ($this->getListeners($event) as $listener) {
			$listener->handle($event, $this->container);
		}
	}
	
	public function replay(Event $event): void
	{
		foreach ($this->getListeners($event) as $listener) {
			$listener->replay($event, $this->container);
		}
	}
	
	/** @return \Thunk\Verbs\Events\Listener[] */
	protected function getListeners(Event $event): array
	{
		$listeners = $this->listeners[$event::class] ?? [];
		
		if (method_exists($event, 'onFire')) {
			$onFire = Listener::fromReflection($event, new ReflectionMethod($event, 'onFire'));
			array_unshift($listeners, $onFire);
		}
		
		return $listeners;
	}
}
