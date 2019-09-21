<?php
/**
 * Created by PhpStorm.
 * User: ZXR
 * Date: 2019/9/18
 * Time: 15:28
 */

namespace acidwater;


use pocketmine\item\Armor;
use pocketmine\level\sound\FizzSound;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class hitRainTask extends Task
{

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
       foreach (Server::getInstance()->getOnlinePlayers() as $player){
           if($player->isOp()){
               continue;
           }
           if(Main::getInstance()->canSeeSky($player) && isset(Main::getInstance()->isRain[$player->getLevel()->getName()])){
               $it = $player->getArmorInventory()->getItem(0);
               if($it->getId() == 0 || $it->getId() == 397){
                   foreach (Main::getInstance()->getEffects() as $id => $effect) {
                       if (!$player->hasEffect($effect->getId())) {
                           $player->addEffect($effect);
                       }
                   }
               }
               $items = $player->getArmorInventory()->getContents();
               for ($i = 0;$i < count($items);$i++) {
                   if(isset($items[$i])){
                       $item = $items[$i];
                       if($item instanceof Armor){
                           if($item->applyDamage(1)){
                               $player->getArmorInventory()->setItem($i,$item);
                           }
                       }
                   }
               }
               if(count($items) > 0){
                   $player->getLevel()->addSound(new FizzSound($player->asVector3()));
               }
           }
       }
    }
}