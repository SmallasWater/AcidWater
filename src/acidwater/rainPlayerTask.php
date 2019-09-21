<?php


namespace acidwater;


use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class rainPlayerTask extends Task
{

    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $pk = new LevelEventPacket();
            $pk->evid = LevelEventPacket::EVENT_START_RAIN;
            $pk->data = 100000;
            if(isset(Main::getInstance()->isRain[$player->getLevel()->getFolderName()])){
                $player->dataPacket($pk);
            }else{
                $pk->evid = LevelEventPacket::EVENT_STOP_RAIN;
                $player->dataPacket($pk);
            }
        }
    }
}
