<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionStopBreak;
use pocketmine\player\Player;
use Zwuiix\AntiBadPackets\libs\SenseiTarzan\ExtraEvent\Class\EventAttribute;
use Zwuiix\AntiBadPackets\modules\Module;

class BadPlayerAuthInput extends Module
{
    protected array $ticks = [];
    protected array $death = [];

    public function __construct()
    {
        parent::__construct("BadPlayerAuthInput", true);
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        $player = $networkSession->getPlayer();
        if (!$player instanceof Player) {
            return;
        }

        if ($packet instanceof PlayerAuthInputPacket) {
            $currentTick = $packet->getTick();
            if (!$player->isAlive()) {
                $this->ticks[$player->getId()] = $currentTick;
            } else {
                if (isset($this->ticks[$player->getId()])) {
                    $lastTick = $this->ticks[$player->getId()];
                    $diff = $currentTick - $lastTick;

                    if (($this->death[$player->getId()] ?? null)) {
                        $this->death[$player->getId()] = false;
                        $this->ticks[$player->getId()] = $currentTick;
                    } else {
                        if (($lastTick === $currentTick) || $diff > 1 || $diff < 1) {
                            $this->flag("Invalid tick");
                        } else $this->ticks[$player->getId()] = $currentTick;
                    }
                } else $this->ticks[$player->getId()] = $currentTick;
            }

            $transactionData = $packet->getItemInteractionData()?->getTransactionData();
            if($player->isSurvival() && $transactionData instanceof UseItemTransactionData && $transactionData->getActionType() === UseItemTransactionData::ACTION_BREAK_BLOCK) {
                $blockActions = $packet->getBlockActions();
                if(is_null($blockActions)) $this->flag("Invalid breaking");
            }
        }
    }

    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::HIGH)]
    public function onPlayerDeath(PlayerDeathEvent $event): void
    {
        $this->death[$event->getPlayer()->getId()] = true;
    }
}
