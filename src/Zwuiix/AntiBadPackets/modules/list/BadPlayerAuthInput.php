<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\player\Player;
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
            $player = $networkSession->getPlayer();
            if(!$player instanceof Player) {
                return;
            }

            if(!isset($this->ticks[$networkSession->getDisplayName()]) || $this->ticks[$networkSession->getDisplayName()]["class"] !== $networkSession->getPlayer()) {
                $this->ticks[$networkSession->getDisplayName()] = ["tick" => $packet->getTick(), "class" => $networkSession->getPlayer()];
                return;
            }

            $lastTick = $this->ticks[$networkSession->getDisplayName()]["tick"];
            $currentTick = $packet->getTick();
            $diff = $currentTick - $lastTick;

            if($lastTick === $currentTick || $diff > 1 || $diff < 1) {
                $this->flag();
            } else $this->ticks[$networkSession->getDisplayName()]["tick"] = $currentTick;
        }
    }
}
