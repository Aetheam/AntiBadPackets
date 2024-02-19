<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiBadPackets\modules\Module;

class BadMobEquipment extends Module
{
    public function __construct()
    {
        parent::__construct("BadMobEquipment");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        try {
            if($packet instanceof MobEquipmentPacket) {
                $item = TypeConverter::getInstance()->netItemStackToCore($packet->item->getItemStack());
                if(count($item->getEnchantments()) >= 40 || count($item->getCanDestroy()) >= 100 || count($item->getCanPlaceOn()) >= 100) {
                    $this->flag("Too big");
                }
            }elseif ($packet instanceof MobArmorEquipmentPacket) {
                foreach([$packet->head, $packet->chest, $packet->legs, $packet->feet] as $i => $item) {
                    $item = TypeConverter::getInstance()->netItemStackToCore($item->getItemStack());
                    if(count($item->getEnchantments()) >= 40 || count($item->getCanDestroy()) >= 100 || count($item->getCanPlaceOn()) >= 100) {
                        $this->flag("Too big");
                        break;
                    }
                }
            }
        }catch (\Error $error) {}
    }
}