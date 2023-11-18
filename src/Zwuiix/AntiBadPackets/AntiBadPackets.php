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

    const MINIMUM_API_VERSION = "5.8.2";

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
        if ($this->getServer()->getApiVersion() < $this::MINIMUM_API_VERSION) {
            $this->getLogger()->warning(sprintf("Sorry, you are using a version prior to %s, please update your pocketmine version to make this plugin work.", $this::MINIMUM_API_VERSION));
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        ModuleManager::getInstance()->registers([
           new BadClientCacheBlob(),
           new BadCraftingEvent(),
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
           new BadTickSync(),
        ]);
        EventLoader::loadEventWithClass($this, ServerListener::class);
        $this->getLogger()->debug("Successfully loaded!");
    }
}