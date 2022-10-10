<?php 
namespace Ki;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\item\ItemFactory;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\StringToEnchantmentParser;

use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use onebone\economyapi\EconomyAPI;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects; 

use Ki\commands\PickaxeSubCommand;

class Pickaxe extends PluginBase implements Listener{
  public $silk = [];
  public $cook = [];
  public $hast = [];
  
  
  public static $instance;
    
  public static function getInstance() : self {
  		return self::$instance;
  	}
  	
  public function onLoad() : void {
  		self::$instance = $this;
  	}
  	
  public function onEnable():void{
    $this->getServer()->getPluginManager()->registerEvents($this ,$this);
    $this->data = new Config($this->getDataFolder()."data.yml",Config::YAML);
    $this->saveDefaultConfig();
    $this->getServer()->getCommandMap()->register("/pickaxe", new PickaxeSubCommand($this));
  }
  
  public function onJoin(PlayerJoinEvent $ev){
    
    $player = $ev->getPlayer();
    $name = $player->getName();
    
    if(!$this->data->exists($name)){
      $inv = $player->getInventory();
      $item = ItemFactory::getInstance()->get(278, 0, 1);
      $item->setCustomName("§r§l§a〖§f⚒§a〗§a ".$this->getConfig()->getNested("Server-Name")." §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
      $item->getNamedTag()->setString($name, $name);
      $inv->addItem($item); 
      $player->sendMessage("§l§6【⚒】§c ".$this->getConfig()->getNested("Server-Name")." đã trao cho bạn Cúp của server, hãy cùng đồng hành với nó nhé để nhận nhiều chức năng!");
      $this->data->set($name, [
        "exp" => 0,
        "level" => 1,
        "hp" => 20,
        "hast" => 0,
        "fortune" => 0,
        "efficiency" => 1,
        "repair" => false,
        "block-money" => false
        ]);
        $this->data->save();
    }
    
    $this->hast[$name] = false;
    $this->silk[$name] = false;
     $this->cook[$name] = false;
    if($this->getConfig()->getNested("Hp-Feature") == true){
      $ev->getPlayer()->setMaxHealth($this->data->getNested("$name.hp"));
    }
    
  }
  
  public function onQuit(PlayerQuitEvent $ev) {
    $this->data->save();
  }
  
  public function onBreak(BlockBreakEvent $ev){ 
    $player = $ev->getPlayer();
    $name = $player->getName();
    $level = $this->data->getNested("$name.level");
    $config = $this->getConfig();
    $id = $ev->getBlock()->getId();
    $data = $this->data->getAll();
    $tag = false;
    foreach($data as $name => $datas){
    	 if($ev->getItem()->getNamedTag()->getTag($name)){
    	      $tag = true;
               break;
         }
    }
    
    if($player->getInventory()->getItemInHand()->getId() == 278 && $tag === true){
      if($player->getInventory()->getItemInHand()->getId() == 0){
        return true;
      }
      if(!$ev->getItem()->getNamedTag()->getTag($name)){
      	$player->sendMessage(" Cúp Này Không Phải Của Bạn");
      	return true;
      }
         if(!$ev->isCancelled()){
           //tự đông sửa chữa
          if($this->data->getNested("$name.repair") == true){
              $item = $player->getInventory()->getItemInHand();
              $item->setDamage(0);	
              $player->getInventory()->setItemInHand($item);
          }
            
          //tự nungforeach
          
          if($this->cook[$name] == true){
            foreach($this->getConfig()->getNested("Block-Cook") as $blockore => $ores){
              if($id == $blockore){
                
                $ev->setDrops([ItemFactory::getInstance()->get($ores,0,1)]);
              }
              
            }
            
          }
          $item = $player->getInventory()->getItemInHand();
          $enchant = EnchantmentIdMap::getInstance()->fromId("16");
          if($player->getInventory()->getItemInHand()->getId() == 278){ 
          	if($this->silk[$name] == true){ 
          	    
          	    $item->addEnchantment(new EnchantmentInstance($enchant, 1));
                  $player->getInventory()->setItemInHand($item);
              }else{ 
              	 if($item->getEnchantment($enchant) !== Null){
  	            	 $item->removeEnchantment($enchant);
  	                 $player->getInventory()->setItemInHand($item);
                   }
              }
          }
          //đập block được money
          if($this->data->getNested("$name.blockmoney") == true){
              $hx = mt_rand(1, 2);
              if($hx == 1){
                EconomyAPI::getInstance()->addMoney($player, 1);
                
            }
          }
          //Enchant Đào Nhanh
          if($this->hast[$name] == true){
            if($level >= $config->getNested("Pickaxe.Feature-Level.Hast1")){
              $this->data->setNested("$name.hast", 1);
              
            }
            if($level >= $config->getNested("Pickaxe.Feature-Level.Hast2")){
              $this->data->setNested("$name.hast", 2);
              
            }
            if($level >= $config->getNested("Pickaxe.Feature-Level.Hast3")){
              $this->data->setNested("$name.hast", 3);
              
            }
            if($level >= $config->getNested("Pickaxe.Feature-Level.Hast4")){
              $this->data->setNested("$name.hast", 4);
                
            }
            if($level >= $config->getNested("Pickaxe.Feature-Level.Hast5")){
              $this->data->setNested("$name.hast", 5);
              
            }
            if($player instanceof Player){
  	          if(!$player->getEffects()->has(VanillaEffects::haste())){
  	             $effcet = new EffectInstance(VanillaEffects::haste(), 20*10, $this->data->getNested("$name.hast"), false);
  	             $player->getEffects()->add($effcet);
  	             $this->data->save();
               }
            }
          }
          //công điểm
          foreach ($config->getNested("Exp-Block") as $block => $exp){
            if($id == $block){
              $this->data->setNested("$name.exp", $this->data->getNested("$name.exp") + $exp);
              $this->data->save();
              
            }
          }
          
          //lên cấp
          $nextlevel = $this->data->getNested("$name.level") * $config->getNested("Max-Level");
          if($this->data->getNested("$name.exp") == $nextlevel){
            $this->data->setNested("$name.level", $this->data->getNested("$name.level")+ 1);
            $money = $config->getNested("Money-NextLevel") * $this->data->getNested("$name.level");
            EconomyAPI::getInstance()->addMoney($player, $money);
            $player->sendMessage("Cúp đã lên cấp và nhận được ".$money."money hãy /pickaxe để đổi cúp mới");
            $this->data->setNested("$name.exp", 0);
            if($this->data->getNested("$name.levl") >= $config->getNested("Pickaxe.Feature-Level.Min-HP ") && $this->data->getNested("$name.level") <= $config->getNested("Pickaxe.Feature-Level.Max-HP ")){
              $this->data->setNested("$name.hp", + 1);
            }
            $this->data->save();
          }
        }else {
           $player->sendMessage("Bạn Không Được đập block ở đây");
         }
            
      }
  }
}
