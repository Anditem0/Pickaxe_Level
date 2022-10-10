<?php 
namespace Ki\form;

use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\item\ItemFactory;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects; 

use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\CustomForm;

use Ki\Pickaxe;

class FormManager{
   
  
  public function giaoDien(Player $player){
    $form = new SimpleForm(function (Player $player, $data = null){
      if($data === null){
        return false;
      }
      switch ($data) {
        case 0:
          $this->givePickaxe($player);
          break;
        
        case 1:
          $this->proFile($player);
          break;
        case 2:
          $this->feature($player);
          break;
        case 3:
          $this->topPickaxe($player);
          break;
        case 4:
          break;
      }
    });
    $form->setTitle("§l§8【§c Pickaxe Level §r§8⚒ §l§8】");
    $form->setContent("§c↣ §eVui lòng lựa chọn§r");
    $form->addButton("§l§8===【§c Nhận Cúp §8】===",1,"https://cdn-icons-png.flaticon.com/512/2437/2437373.png");
    $form->addButton("§l§8===【§c Thông Tin Cúp §8】===",1,"https://cdn-icons-png.flaticon.com/128/942/942748.png");
    $form->addButton("§l§8===【§c Tính Năng §8】===",1,"https://cdn-icons-png.flaticon.com/512/1933/1933975.png");
    $form->addButton("§l§8===【§c Top Cúp §8】===",1,"https://cdn-icons-png.flaticon.com/128/2282/2282594.png");
    $form->addButton("§l§8===【§c ⇐ Thoát §8】===",1,"https://cdn-icons-png.flaticon.com/128/1828/1828427.png");
    $form->sendToPlayer($player);
  }
  //nhận Cúp
  public function givePickaxe(Player $player){ 
    $name = $player->getName();
    $level = Pickaxe::getInstance()->data->getNested("$name.level");
    $config = Pickaxe::getInstance()->getConfig();
    $efficiency = 0;
    $fortune = 0;
    $levelfortune =  $level - $config->getNested("Pickaxe.Eff-Level.Max-Efficiency");
    
    if($level >= $config->getNested("Pickaxe.Eff-Level.Max-Efficiency")){
      $efficiency = $config->getNested("Pickaxe.Eff-Level.Max-Efficiency");
    }
    if($level >= 0 && $level <= $config->getNested("Pickaxe.Eff-Level.Max-Efficiency")){
      $efficiency = $level;
      }
    if($levelfortune >= 0 && $levelfortune <= $config->getNested("Pickaxe.Eff-Level.Max-Fortune")){
      $fortune = $levelfortune;
    }
    if($levelfortune < 0){
      $fortune = 0;
    }
    if($levelfortune > $config->getNested("Pickaxe.Eff-Level.Max-Fortune")){
      $fortune = $config->getNested("Pickaxe.Eff-Level.Max-Fortune");
    }
    $inv = $player->getInventory();
    $item = ItemFactory::getInstance()->get(278, 0, 1);
    $item->getNamedTag()->setString($name, $name);
    $item->setCustomName("§r§l§a〖§f⚒§a〗§a ".$config->getNested("Server-Name")."§a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
    $enchant = EnchantmentIdMap::getInstance()->fromId("15");
    $item->addEnchantment(new EnchantmentInstance($enchant, $efficiency)); 
    if($levelfortune > 0){
       $item->addEnchantment(new EnchantmentInstance("fortune", $fortune));
       }
    $inv->addItem($item);
    $player->sendMessage("§l§c➢§a Bạn đã nhận thành công Cúp Level §e".$level." \n§aHiệu Suất:§c ".$efficiency."\n§aGia Tài:§c ".$fortune);
    Pickaxe::getInstance()->data->setNested("$name.efficiency", $efficiency);
    Pickaxe::getInstance()->data->setNested("$name.fortune", $fortune);
    Pickaxe::getInstance()->data->save();
  }
  
  //Thông tin
  public function proFile(Player $player){
    $form = new SimpleForm(function(Player $player, $data = null){
      if($data == null){
        $this->giaoDien($player);
      }
    });
      $name = $player->getName();
      $data = Pickaxe::getInstance()->data;
      $config = Pickaxe::getInstance()->getConfig();
      $level = $data->getNested("$name.level");
      $exp = $data->getNested("$name.exp");
      $hast = $data->getNested("$name.hast");
      $hp = $data->getNested("$name.hp");
      $efficiency = $data->getNested("$name.efficiency");
      $fortune = $data->getNested("$name.fortune");
      $cook = "§cChưa mở";
      $repair = "§cChưa Mở";
      $blockmoney = "§cChưa Mở";
      $silk = "§cChưa Mở";
      if($level >= $config->getNested("Pickaxe.Feature-Level.Cook")){
        $cook = "Đã mở";
      }
      if($level >= $config->getNested("Pickaxe.Feature-Level.Repair")){
        $repair = "Đã Mở";
      }
      if($level >= $config->getNested("Pickaxe.Feature-Level.Block-Money")){
        $blockmoney = "Đã Mở";
      }
      if($level >= $config->getNested("Pickaxe.Feature-Level.Silk")){
        $silk = "Đã Mở";
      }
      if($config->getNested("Hp-Feature") == false){
        $hp = "§cĐã Tắt";
      }
    
    $form->setTitle("§l§8【§c Thông Tin Cúp §8】");
    $form->setContent("§aNgười Sở hữu: §e".$player->getName()."\n §aCấp: ".$level."\n§a Kinh Nhiệm: ".$exp."\n§a HP: ".$hp."\n §aHiệu Xuất: ".$efficiency."\n §aGia Tài: ".$fortune."\nTính Năng:\n §aTự Nung: ".$cook."\n§a Tự Sửa Chữa: ".$repair."\n§a Đập Được Tiền: ".$blockmoney."\n §aMềm Mại: ".$silk);
    $form->sendToPlayer($player);
  }
  
  
  //tính Năng
  public function feature(Player $player){
    $form = new SimpleForm(function(Player $player, $data = null){
      $name = $player->getName();
      if($data == null){
        $this->giaoDien($player);
      }
      switch ($data) {
        case 0:
          if(Pickaxe::getInstance()->data->getNested("$name.level") >= Pickaxe::getInstance()->getConfig()->getNested("Pickaxe.Feature-Level.Cook")){
	          if(Pickaxe::getInstance()->cook[$name] == false){
	            Pickaxe::getInstance()->cook[$name] = true;
	            $player->sendMessage("Đã mở Tự Động Nung Quặng");
              }else{
          	  $player->sendMessage("Bạn đã tắt Tự Đông Nung Quặng");
                Pickaxe::getInstance()->cook[$name] = false;
              }
          }else{
            $player->sendMessage("Bạn Chưa Đủ Cấp Độ Để Mở Kĩ Năng");
          }
          
          
          break;
        case 1:
          if(Pickaxe::getInstance()->data->getNested("$name.level") >= Pickaxe::getInstance()->getConfig()->getNested("Pickaxe.Feature-Level.Repair")){
          	if( Pickaxe::getInstance()->data->getNested("$name.repair") == false){
	             Pickaxe::getInstance()->data->setNested("$name.repair", true);
	             $player->sendMessage("Bạn Đã Bật Tự Động Sữa Chữa");
              }else{
              	 $player->sendMessage("Bạn Đã Tắt Tự Động Sữa Chữa");
                   Pickaxe::getInstance()->data->setNested("$name.repair", false);
              }
          }else {
            $player->sendMessage("Bạn Chưa Đủ Cấp Độ Để Bật Tính Năng");
          }
          break;
        case 2:
          if( Pickaxe::getInstance()->data->getNested("$name.level") >= Pickaxe::getInstance()->getConfig()->getNested("Pickaxe.Feature-Level.Block-Money") ){
          	if(Pickaxe::getInstance()->data->getNested("$name.blockmoney") == false){
	             Pickaxe::getInstance()->data->setNested("$name.blockmoney", true);
	             $player->sendMessage("Bạn Đã Bật Đào Block Ra Money");
             }else{ 
             	 $player->sendMessage(" Bạn Đã Tắt Đào Block Ra Money");
                  Pickaxe::getInstance()->data->setNested("$name.blockmoney", false);
             }
          }else{ 
            $player->sendMessage("Bạn Chưa Đủ Cấp Độ Để Bật Tính Năng");
          }
          break;
        case 3:
          if( Pickaxe::getInstance()->data->getNested("$name.level") >= Pickaxe::getInstance()->getConfig()->getNested("Pickaxe.Feature-Level.Silk")){
          	if(Pickaxe::getInstance()->silk[$name] == false){
		          Pickaxe::getInstance()->silk[$name] = true;
		          $player->sendMessage("Bạn Đã Bật Mềm Mãi");
		      }else{
			      $player->sendMessage("Bạn Đã Tắt Mềm Mãi");
                  Pickaxe::getInstance()->silk[$name] = false;
              }
          }else{
            $player->sendMessage("Bạn Chưa Đủ Cấp Độ Để Bật Tính Năng");
          }
          break;
        case 4:
        $hast = Pickaxe::getInstance()->data->getNested("$name.hast");
          if(Pickaxe::getInstance()->data->getNested("$name.level") >= Pickaxe::getInstance()->getConfig()->getNested("Pickaxe.Feature-Level.hast1")){
          	 if(Pickaxe::getInstance()->hast[$name] == false){
		           Pickaxe::getInstance()->hast[$name] = true;
		           $player->sendMessage("Bạn Đã Bật Đào Nhanh ".$hast);
		       }else{
			       $player->sendMessage("Bạn Đã Tắt Đào Nhanh ".$hast);
                   Pickaxe::getInstance()->hast[$name] = false;
               }
          }else {
            $player->sendMessage("Bạn Chưa Đủ Cấp Độ Để Bật Tính Năng");
          }
          break;
        case 5:
          break;
        Pickaxe::getinstance()->data->save();
      }
    });
      $config = Pickaxe::getInstance()->getConfig();
      $data = Pickaxe::getInstance()->data;
      $name = $player->getName();
      $level = $data->getNested("$name.level");
    //tự sửa chữa 
      if($level >= $config->getNested("Pickaxe.Feature-Level.Repair") ){
        $repair = "§eĐã Mở Khóa";
      }else {
        $repair = "§cChưa Mở Khóa";
      }
      if($data->getNested("$name.repair") == true){
        $repair = "§aĐang Mở";
      }
      
      //tự nấu
      if($level >= $config->getNested("Pickaxe.Feature-Level.Cook") ){
        $cook = "§eĐã Mở Khóa";
      }else{
        $cook = "§cChưa Mở Khóa";
        }
      if(Pickaxe::getInstance()->cook[$name] == true){
        $cook = "§aĐang Mở";
                }
      
      //đào block ra money
      if($level >= $config->getNested("Pickaxe.Feature-Level.Block-Money") ){
        $blockmoney = "§eĐã Mở Khóa";
      }else{
        $blockmoney = "§cChưa Mở Khóa";
        }
      if($data->getNested("$name.blockmoney") == true){
        $blockmoney= "§aĐang Mở";
        }
      
      //mềm mại
      if($level >= $config->getNested("Pickaxe.Feature-Level.Silk") ){
        $silk = "§eĐã Mở Khóa";
      }else{
        $silk = "§cChưa Mở Khóa";
      }
      if(Pickaxe::getInstance()->silk [$name] == true){
        $silk = "§aĐang Mở";
      }
      
      if($level >= $config->getNested("Pickaxe.Feature-Level.hast1") ){
        $hast = "§eĐã Mở Khóa";
      }else{
        $hast = "§cChưa Mở Khóa";
      }
      if(Pickaxe::getInstance()->hast[$name] == true){
        $hast = "§aĐang Mở";
      }
    $form->setTitle("§l§8【§c Tính Năng Của Cúp §8】");
    $form->setContent("§c*§a Ấn Vào Để Mở Hoặc Tắt");
    $form->addButton("§l§8===【§c Tự Động Nung §8】===\n".$cook." ",1," https://cdn-icons-png.flaticon.com/128/1593/1593986.png");
    $form->addButton("§l§8===【§c Tự Động Sữa Chữa §8】===\n".$repair." ",1,"https://cdn-icons-png.flaticon.com/128/3166/3166134.png");
    $form->addButton("§l§8===【§c Đào Block Ra Money §8】===\n".$blockmoney." ",1,"https://cdn-icons-png.flaticon.com/128/639/639365.png");
    $form->addButton("§l§8===【§c Mềm Mại §8】===\n".$silk." ",1,"https://cdn-icons-png.flaticon.com/128/2106/2106263.png");
    $form->addButton("§l§8===【§c Đào Nhanh §8】===\n".$hast." ",1,"https://cdn-icons-png.flaticon.com/128/601/601102.png");
    $form->addButton("§l§8===【§c ⇐ Thoát §8】===",1,"https://cdn-icons-png.flaticon.com/128/1828/1828427.png");
    $form->sendToPlayer($player);
  }
  
  //Top Cúp
  public function topPickaxe(Player $player){
    $form = new CustomForm(function(Player $player, $data = null){
      if($data == null){
        $this->giaoDien($player);
      }
      
    });
    $data = Pickaxe::getInstance()->data->getAll();
      if(count($data) > 0){
        arsort($data);
        $i = 1;
      }
      $message = "§c§l* §aTop Người Đứng Đầu:\n";
      $name = $player->getName();
      foreach ($data as $name => $datas){
        $message .= "    §l§3TOP " . $i . ": §6" . $name . " §d→ §f" . $datas["level"] . " §2Cấp ".$datas["level"]." §bKinh Nghiệm\n";
        				if($i >= 10){
        					break;
        				}
        				++$i;
        		}
        
    $form->setTitle("§l§8【§c TOP LEVEL §8】");
    $form->addLabel($message);
    $form->sendToPlayer($player);
  }
  
}
