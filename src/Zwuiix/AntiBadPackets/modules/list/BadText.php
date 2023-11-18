<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadText extends Module
{

    public function __construct()
    {
        parent::__construct("BadText");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if($packet instanceof TextPacket) {
            if(
                count($packet->parameters) >= 5 ||
                $packet->xboxUserId !== $networkSession->getPlayer()->getXuid() ||
                $packet->needsTranslation ||
                $packet->type !== TextPacket::TYPE_CHAT ||
                $packet->sourceName !== $networkSession->getDisplayName()
            ) {
               $this->flag();
            }
        }
    }
}