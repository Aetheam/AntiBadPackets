<?php

namespace Zwuiix\AntiBadPackets\listener;

use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketReceiveEvent;
use Zwuiix\AntiBadPackets\libs\SenseiTarzan\ExtraEvent\Class\EventAttribute;
use Zwuiix\AntiBadPackets\modules\ModuleManager;

class ServerListener
{
    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::HIGH)]
    public function onDataReceive(DataPacketReceiveEvent $event): void
    {
        foreach (ModuleManager::getInstance()->getAll() as $module) {
            $module->inboundPacket($event->getOrigin(), $event->getPacket());
        }
    }
}