<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\Server;
use Zwuiix\AntiBadPackets\modules\Module;

class BadResourcePack extends Module
{
    public function __construct()
    {
        parent::__construct("BadResourcePack");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof ResourcePackClientResponsePacket) {
            if(count($packet->packIds) > count(Server::getInstance()->getResourcePackManager()->getPackIdList())) {
                $this->flag();
            }
        }
    }
}