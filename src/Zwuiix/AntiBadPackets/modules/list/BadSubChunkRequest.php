<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\SubChunkRequestPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadSubChunkRequest extends Module
{
    public function __construct()
    {
        parent::__construct("BadSubChunkRequest");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof SubChunkRequestPacket) {
            if(count($packet->getEntries()) >= 50) {
                $this->flag();
            }
        }
    }
}