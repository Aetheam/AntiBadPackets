<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\CraftingEventPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadCraftingEvent extends Module
{
    public function __construct()
    {
        parent::__construct("BadCraftingEvent");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if ($packet instanceof CraftingEventPacket) {
            if(($input = count($packet->input)) >= 50) {
                $this->flag();
            }
            if(($output = count($packet->output)) >= 50) {
                $this->flag();
            }

            foreach ($packet->input as $itemStackWrapper) {
                $item = TypeConverter::getInstance()->netItemStackToCore($itemStackWrapper->getItemStack());
                if(count($item->getEnchantments()) >= 100 || count($item->getCanDestroy()) >= 100 || count($item->getCanPlaceOn()) >= 100) {
                    $this->flag();
                }
            }
            foreach ($packet->output as $itemStackWrapper) {
                $item = TypeConverter::getInstance()->netItemStackToCore($itemStackWrapper->getItemStack());
                if(count($item->getEnchantments()) >= 100 || count($item->getCanDestroy()) >= 100 || count($item->getCanPlaceOn()) >= 100) {
                    $this->flag();
                }
            }
        }
    }
}