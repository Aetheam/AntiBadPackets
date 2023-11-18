<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\BookEditPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadSign extends Module
{
    public function __construct()
    {
        parent::__construct("BadSign");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if($packet instanceof BookEditPacket) {
            if ($packet->type == BookEditPacket::TYPE_SIGN_BOOK) {
                if (($networkSession->getPlayer()->getXuid()) !== $packet->xuid) {
                    $this->flag();
                }
            }
        }
    }
}