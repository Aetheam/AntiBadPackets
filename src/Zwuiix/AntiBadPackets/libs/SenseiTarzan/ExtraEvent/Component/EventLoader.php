<?php

namespace Zwuiix\AntiBadPackets\libs\SenseiTarzan\ExtraEvent\Component;

use pocketmine\event\Event;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Zwuiix\AntiBadPackets\libs\SenseiTarzan\ExtraEvent\Class\EventAttribute;

class EventLoader
{

    public static function loadEventWithClass(PluginBase $plugin, object|string $class): void
    {
        try {
            $reflectClass = new ReflectionClass($class);
        } catch (ReflectionException) {
            $plugin->getLogger()->warning($class . "no existe ");
            return;
        }
        try {
            $instance = is_object($class) ? $class : $reflectClass->newInstanceWithoutConstructor();

        } catch (ReflectionException) {
            $plugin->getLogger()->warning($class . "can't create new instance without constructor");
            return;
        }
        foreach ($reflectClass->getMethods() as $method) {
            $attributes = $method->getAttributes($eventClass = EventAttribute::class);
            if (empty($attributes)) continue;
            $attribute = array_filter($attributes, fn(\ReflectionAttribute $attribute) => $attribute->getName() === $eventClass);
            $attribute = ($attribute[array_key_first($attribute)] ?? null)?->newInstance();
            if (!($attribute instanceof EventAttribute)) continue;
            $eventType = self::getEventsHandledBy($method);
            if ($eventType === null) continue;
            $plugin->getServer()->getPluginManager()->registerEvent($eventType, $method->getClosure($instance), $attribute->getPriority(), $plugin, $attribute->isHandleCancelled());
            $plugin->getLogger()->debug("[libEventAttribute]the ". TextFormat::GOLD . $method->getName() . TextFormat::WHITE.  "($eventType) method of ". $reflectClass->getName() . " has been initialized");
        }
        unset($instance);
    }

    private static function getEventsHandledBy(ReflectionMethod $method): ?string
    {
        if ($method->isStatic()) {
            return null;
        }

        $parameters = $method->getParameters();
        if (count($parameters) !== 1) {
            return null;
        }

        $paramType = $parameters[0]->getType();
        if (!$paramType instanceof \ReflectionNamedType || $paramType->isBuiltin()) {
            return null;
        }

        /** @phpstan-var class-string $paramClass */
        $paramClass = $paramType->getName();
        $eventClass = new ReflectionClass($paramClass);
        if (!$eventClass->isSubclassOf(Event::class)) {
            return null;
        }

        return $eventClass->getName();
    }

}