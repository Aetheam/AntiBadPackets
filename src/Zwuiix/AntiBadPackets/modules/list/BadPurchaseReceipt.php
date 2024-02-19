<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PurchaseReceiptPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadPurchaseReceipt extends Module
{
    public function __construct()
    {
        parent::__construct("BadPurchaseReceipt");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof PurchaseReceiptPacket) {
            if(count($packet->entries) >= 50) $this->flag("Too big");
        }
    }
}