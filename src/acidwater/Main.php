<?php

namespace acidwater;


use pocketmine\block\Air;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{
    private static $instance;
    public $isRain;

    public function onEnable()
    {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $c = $this->getConfig();

        if($c->get("acid-rain")){
            $this->getScheduler()->scheduleRepeatingTask(new hitRainTask(),20);
            $this->getScheduler()->scheduleRepeatingTask(new rainPlayerTask(),20);
            $level = Server::getInstance()->getDefaultLevel();
            $path = $level->getFolderName();
            $p1 = dirname($path);
            $p2 = $p1."/worlds/";
            $dFile = scandir($p2, 1);
            foreach ($dFile as $dirFile) {
                if($dirFile != '.' && $dirFile != '..' && $dirFile != $path && is_dir($p2.$dirFile)) {
                    if(!in_array($dirFile,$c->get("rain-worlds"))){
                        $this->getScheduler()->scheduleRepeatingTask(new loadAcidTask($dirFile,$c->get("acid-rain-load(min)")),20);
                    }
                }
            }
        }
        if($c->get("acid-water")){
            $this->getScheduler()->scheduleRepeatingTask(new class($c) extends Task
            {

                private $c;
                function __construct(Config $c)
                {
                    $this->c = $c;
                }

                function isInWater(Player $p)
                {
                    foreach ($p->getBlocksAround() as $block) {
                        if ($block->getId() == 9 || $block->getId() == 8) {
                            return true;
                        }
                    }
                    return false;
                }
                public function onRun(int $currentTick)
                {
                    foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                        if ($this->isInWater($player) && !in_array($player->getLevel()->getFolderName(),$this->c->get("water-worlds"))) {
                            if(!$player->isOp()){
                                foreach (Main::getInstance()->getEffects() as $id => $effect) {
                                    if (!$player->hasEffect($effect->getId())) {
                                        $player->addEffect($effect);
                                    }
                                }
                            }
                        }
                    }

                }
            }, 20);

        }
    }

    /**
     * @return EffectInstance[]
    */
    public function getEffects():array {
        $e = [];
        $c = $this->getConfig();
        foreach ($c->get("effects") as $id){
            $e[] = new EffectInstance(Effect::getEffect($id), $c->get("effect-duration")* 20, $c->get("effect-amplifier"), $c->get("effect-visible"));
        }
        return $e;
    }
    public function canSeeSky(Player $p):bool {
        for ($i = $p->y + 1; $i <= 255; $i++) {
            if (!($p->getLevel()->getBlock(new Vector3($p->x,$i,$p->z) ,false) instanceof Air)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return mixed
     */
    public static function getInstance():Main
    {
        return self::$instance;
    }




}