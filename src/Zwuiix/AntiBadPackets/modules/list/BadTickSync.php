<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\TickSyncPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadTickSync extends Module
{
    private array $tickSync = [];

    public function __construct()
    {
        parent::__construct("BadTickSync");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if($packet instanceof TickSyncPacket) {
            if($this->addTickSync($networkSession) >= 4) {
                $this->flag();
            }
        }
    }

    /**
     * @param NetworkSession $networkSession
     * @return float
     */
    public function addTickSync(NetworkSession $networkSession): float
    {
        if(!isset($this->tickSync[$networkSession->getDisplayName()])) {
            $this->tickSync[$networkSession->getDisplayName()] = [];
        }
        array_unshift($this->tickSync[$networkSession->getDisplayName()], microtime(true));
        return $this->getTickSync($networkSession);
    }

    /**
     * @param NetworkSession $networkSession
     * @return float
     */
    public function getTickSync(NetworkSession $networkSession): float
    {
        if(!isset($this->tickSync[$networkSession->getDisplayName()])) {
            $this->tickSync[$networkSession->getDisplayName()] = [];
        }
        return round(count(array_filter($this->tickSync[$networkSession->getDisplayName()], static function (float $t): bool {
                return (microtime(true) - $t) <= 1.0;
            })) / 1.0, 1);
    }
}