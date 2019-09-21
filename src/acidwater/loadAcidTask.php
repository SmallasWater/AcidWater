<?php

namespace acidwater;



use pocketmine\scheduler\Task;
use pocketmine\Server;

class loadAcidTask extends Task
{

    private $level;
    private $time;
    function __construct(string $level,int $time)
    {
        $this->level = $level;
        $this->time = $time;
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        if(!isset(Main::getInstance()->isRain[$this->level])){
            $this->time--;
            if($this->time == 300){
                $level = Server::getInstance()->getLevelByName($this->level);
                if($level != null){
                    foreach ($level->getPlayers() as $player){
                        $player->sendMessage("§6>> [酸雨] §c5 分钟后降下酸雨...");
                    }
                }
            }
            if($this->time <= 0){
                Main::getInstance()->isRain[$this->level] = true;
                $level = Server::getInstance()->getLevelByName($this->level);
                if($level != null){
                    foreach ($level->getPlayers() as $player){
                        $player->sendMessage("§6>> [酸雨] §c这个世界开始下酸雨了...请注意躲避 或者准备一个好一点的帽子");
                    }
                }

                $this->time = Main::getInstance()->getConfig()->get("acid-rain-load(s)");
            }
        }else{
            $this->time--;
            if($this->time <= 0){
                unset(Main::getInstance()->isRain[$this->level]);
                $level = Server::getInstance()->getLevelByName($this->level);
                if($level != null){
                    foreach ($level->getPlayers() as $player){
                        $player->sendMessage("§6>> [酸雨] §a你成功度过了本次酸雨");
                    }
                }
                $this->time = Main::getInstance()->getConfig()->get("acid-rain-time(s)");
            }
        }
    }
}