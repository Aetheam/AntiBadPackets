<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadSetActorData extends Module
{
    public function __construct()
    {
        parent::__construct("BadSetActorData");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof SetActorDataPacket) {
            if (count($packet->metadata) >= 50) {
                $this->flag();
            }
        }
    }
}