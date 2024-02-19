<?php

namespace Zwuiix\AntiBadPackets\modules\list;

use pocketmine\network\mcpe\JwtException;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\login\AuthenticationData;
use pocketmine\network\mcpe\protocol\types\login\ClientData;
use pocketmine\network\mcpe\protocol\types\login\JwtChain;
use pocketmine\network\PacketHandlingException;
use pocketmine\Server;
use pocketmine\ServerProperties;
use Zwuiix\AntiBadPackets\modules\Module;

class BadLogin extends Module
{
    public function __construct()
    {
        parent::__construct("BadLogin");
    }

    /**
     * @param NetworkSession $networkSession
     * @param ServerboundPacket $packet
     * @return void
     */
    public function inboundPacket(NetworkSession $networkSession, ServerboundPacket $packet): void
    {
        if($packet instanceof LoginPacket) {
            if (Server::getInstance()->getConfigGroup()->getConfigBool(ServerProperties::XBOX_AUTH, true)) {
                $authData = $this->fetchAuthData($packet->chainDataJwt);
                $clientData = $this->parseClientData($packet->clientDataJwt);
                $detectedOs = match ($authData->titleId) {
                    "896928775" => DeviceOS::WINDOWS_10,
                    "2047319603" => DeviceOS::NINTENDO,
                    "1739947436" => DeviceOS::ANDROID,
                    "2044456598" => DeviceOS::PLAYSTATION,
                    "1828326430" => DeviceOS::XBOX,
                    "1810924247" => DeviceOS::IOS,
                    default => "Unknown",
                };

                if ($detectedOs !== $clientData->DeviceOS) $this->flag();
                if ($detectedOs === DeviceOS::ANDROID && $clientData->DeviceModel !== strtoupper($clientData->DeviceModel)) $this->flag();
                if ($clientData->ThirdPartyName !== $authData->displayName && $detectedOs !== DeviceOS::PLAYSTATION && $detectedOs !== DeviceOS::NINTENDO) $this->flag();
                if ($clientData->PlayFabId === "") $this->flag();
                if ($clientData->SkinColor === "") $this->flag();
            }
        }
    }

    /**
     * @param JwtChain $chain
     * @return AuthenticationData
     */
    protected function fetchAuthData(JwtChain $chain) : AuthenticationData{
        /** @var AuthenticationData|null $extraData */
        $extraData = null;
        foreach($chain->chain as $k => $jwt){
            //validate every chain element
            try{
                [, $claims, ] = JwtUtils::parse($jwt);
            }catch(JwtException $e){
                throw PacketHandlingException::wrap($e);
            }

            if($k === 0 && (!isset($claims["exp"]) || isset($claims["iat"]) || isset($claims["iss"]))) {
                $this->flag();
            }

            if(isset($claims["extraData"])){
                if($extraData !== null){
                    throw new PacketHandlingException("Found 'extraData' more than once in chainData");
                }

                if(!is_array($claims["extraData"])){
                    throw new PacketHandlingException("'extraData' key should be an array");
                }
                $mapper = new \JsonMapper();
                $mapper->bEnforceMapType = false; //TODO: we don't really need this as an array, but right now we don't have enough models
                $mapper->bExceptionOnMissingData = true;
                $mapper->bExceptionOnUndefinedProperty = true;
                try{
                    /** @var AuthenticationData $extraData */
                    $extraData = $mapper->map($claims["extraData"], new AuthenticationData());
                }catch(\JsonMapper_Exception $e){
                    throw PacketHandlingException::wrap($e);
                }
            }
        }
        if($extraData === null){
            throw new PacketHandlingException("'extraData' not found in chain data");
        }
        return $extraData;
    }

    /**
     * @throws PacketHandlingException
     */
    protected function parseClientData(string $clientDataJwt) : ClientData{
        try{
            [, $clientDataClaims, ] = JwtUtils::parse($clientDataJwt);
        }catch(JwtException $e){
            throw PacketHandlingException::wrap($e);
        }

        $mapper = new \JsonMapper();
        $mapper->bEnforceMapType = false; //TODO: we don't really need this as an array, but right now we don't have enough models
        $mapper->bExceptionOnMissingData = true;
        $mapper->bExceptionOnUndefinedProperty = true;
        try{
            $clientData = $mapper->map($clientDataClaims, new ClientData());
        }catch(\JsonMapper_Exception $e){
            throw PacketHandlingException::wrap($e);
        }
        return $clientData;
    }
}
