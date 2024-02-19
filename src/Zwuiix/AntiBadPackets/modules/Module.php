<?php

namespace Zwuiix\AntiBadPackets\modules;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\PacketHandlingException;

abstract class Module
{
    public function __construct(
        protected string $name,
        protected bool $listener = false
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isListener(): bool
    {
        return $this->listener;
    }

    public function flag(string $reason = "Unknown"): void
    {
        throw new PacketHandlingException("Module {$this->getName()}: {$reason}");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    abstract public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void;
}