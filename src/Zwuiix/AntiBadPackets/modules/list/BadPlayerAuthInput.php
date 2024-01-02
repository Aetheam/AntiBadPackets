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
    private array $lastPackets = [];
    private array $balance = [];
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

            if(!$player->isAlive()) {
                $this->ticks[$networkSession->getDisplayName()] = $packet->getTick();
                $this->lastPackets[$networkSession->getDisplayName()] = null;
                return;
            }
            if(!isset($this->balance[$networkSession->getDisplayName()])) {
                $this->balance[$networkSession->getDisplayName()] = 0;
            }
            if(true) {
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

            $currentTime = microtime(true) * 1000;
            if(is_null($this->lastPackets[$networkSession->getDisplayName()])){
                $this->lastPackets[$networkSession->getDisplayName()] = $currentTime;
                return;
            }

            $timeDiff = round(($currentTime - $this->lastPackets[$networkSession->getDisplayName()]) / 50, 2);
            $this->balance[$networkSession->getDisplayName()] -= 1;
            $this->balance[$networkSession->getDisplayName()] += $timeDiff;

            $this->lastPackets[$networkSession->getDisplayName()] = $currentTime;

            $ping = $networkSession->getPing() ?? 0;
            $compensation = $ping <= 85 ? -6.5 : ($ping >= 200 ? -15 : -10);
            if($this->balance[$networkSession->getDisplayName()] <= $compensation){
                $this->flag();
            }
        }
    }
}
