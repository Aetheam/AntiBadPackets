<?php

namespace Zwuiix\AntiBadPackets;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Zwuiix\AntiBadPackets\libs\SenseiTarzan\ExtraEvent\Component\EventLoader;
use Zwuiix\AntiBadPackets\listener\ServerListener;
use Zwuiix\AntiBadPackets\modules\list\BadClientCacheBlob;
use Zwuiix\AntiBadPackets\modules\list\BadCraftingEvent;
use Zwuiix\AntiBadPackets\modules\list\BadEmoteList;
use Zwuiix\AntiBadPackets\modules\list\BadInventoryTransaction;
use Zwuiix\AntiBadPackets\modules\list\BadLogin;
use Zwuiix\AntiBadPackets\modules\list\BadMapInfoRequest;
use Zwuiix\AntiBadPackets\modules\list\BadMobEquipment;
use Zwuiix\AntiBadPackets\modules\list\BadPlayerAuthInput;
use Zwuiix\AntiBadPackets\modules\list\BadPurchaseReceipt;
use Zwuiix\AntiBadPackets\modules\list\BadResourcePack;
use Zwuiix\AntiBadPackets\modules\list\BadSetActorData;
use Zwuiix\AntiBadPackets\modules\list\BadSign;
use Zwuiix\AntiBadPackets\modules\list\BadSubChunkRequest;
use Zwuiix\AntiBadPackets\modules\list\BadText;
use Zwuiix\AntiBadPackets\modules\list\BadTickSync;
use Zwuiix\AntiBadPackets\modules\ModuleManager;

class AntiBadPackets extends PluginBase
{
    use SingletonTrait;

    /**
     * @return void
     */
    protected function onLoad(): void
    {
        $this::setInstance($this);
    }

    /**
     * @return void
     */
    protected function onEnable(): void
    {
        ModuleManager::getInstance()->registers([
           new BadClientCacheBlob(),
           new BadEmoteList(),
           new BadInventoryTransaction(),
           new BadLogin(),
           new BadMapInfoRequest(),
           new BadMobEquipment(),
           new BadPlayerAuthInput(),
           new BadPurchaseReceipt(),
           new BadResourcePack(),
           new BadSetActorData(),
           new BadSign(),
           new BadSubChunkRequest(),
           new BadText(),
        ]);
        EventLoader::loadEventWithClass($this, ServerListener::class);
        $this->getLogger()->debug("Successfully loaded!");
    }
}