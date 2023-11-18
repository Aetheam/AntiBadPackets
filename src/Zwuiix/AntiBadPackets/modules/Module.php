<?php

namespace Zwuiix\AntiBadPackets\modules;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\PacketHandlingException;

abstract class Module
{
    public function __construct(
        protected string $name,
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function flag(): void
    {
        throw new PacketHandlingException("Module {$this->getName()}");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    abstract public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void;
}