<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadMapInfoRequest extends Module
{
    public function __construct()
    {
        parent::__construct("BadMapInfoRequest");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof MapInfoRequestPacket) {
            if(count($packet->clientPixels) >= 50) $this->flag("Too big");
        }
    }
}