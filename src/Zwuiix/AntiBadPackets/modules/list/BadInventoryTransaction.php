<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use Zwuiix\AntiBadPackets\modules\Module;

class BadInventoryTransaction extends Module
{
    public function __construct()
    {
        parent::__construct("BadInventoryTransaction");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof InventoryTransactionPacket) {
            foreach ($packet->trData->getActions() as $action) {
                foreach ([$action->oldItem, $action->newItem] as $item) {
                    $item = TypeConverter::getInstance()->netItemStackToCore($item->getItemStack());
                    if (count($item->getEnchantments()) >= 100 || count($item->getCanDestroy()) >= 100 || count($item->getCanPlaceOn()) >= 100) {
                        $this->flag();
                    }
                }
            }
        }
    }
}