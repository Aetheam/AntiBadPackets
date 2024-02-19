<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\EmoteListPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadEmoteList extends Module
{
    public function __construct()
    {
        parent::__construct("BadEmoteList");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof EmoteListPacket) {
            if(count($packet->getEmoteIds()) >= 50) $this->flag("Too big");
        }
    }
}