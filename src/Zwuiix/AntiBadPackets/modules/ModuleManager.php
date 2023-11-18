<?php

namespace Zwuiix\AntiBadPackets\modules;

use pocketmine\utils\SingletonTrait;

final class ModuleManager
{
    use SingletonTrait;

    /**
     * @var Module[]
     */
    protected array $modules = [];

    /**
     * @param Module[] $modules
     * @return void
     */
    public function registers(array $modules): void
    {
        foreach ($modules as $module) {
            $this->modules[$module->getName()] = $module;
        }
    }

    /**
     * @param Module $module
     * @return void
     */
    public function register(Module $module): void
    {
        $this->modules[$module->getName()] = $module;
    }

    /**
     * @return Module[]
     */
    public function getAll(): array
    {
        return $this->modules;
    }
}