<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadPlayerAuthInput extends Module
{
    private array $ticks = [];
    public function __construct()
    {
        parent::__construct("BadPlayerAuthInput");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if($packet instanceof PlayerAuthInputPacket) {
            if(!isset($this->ticks[$networkSession->getDisplayName()])) {
                $this->ticks[$networkSession->getDisplayName()] = $packet->getTick();
            }

            $this->ticks[$networkSession->getDisplayName()]++;
            if($packet->getTick() !== $this->ticks[$networkSession->getDisplayName()]) {
                $diff = abs($packet->getTick() - $this->ticks[$networkSession->getDisplayName()]);
                if($networkSession->getPlayer()->isAlive() && $networkSession->getPlayer()->getHealth() > 1 && $diff >= 2) {
                    $this->flag();
                }else $this->ticks[$networkSession->getDisplayName()] = $packet->getTick();
            }
        }
    }
}