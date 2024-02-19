<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientCacheBlobStatusPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadClientCacheBlob extends Module
{
    public function __construct()
    {
        parent::__construct("BadClientCacheBlob");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof ClientCacheBlobStatusPacket) {
            if(count($packet->getHitHashes()) >= 50) $this->flag();
            if(count($packet->getMissHashes()) >= 50) $this->flag();
        }
    }
}