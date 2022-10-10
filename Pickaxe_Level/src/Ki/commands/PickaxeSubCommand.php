<?php

namespace Ki\commands;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\{ Command, CommandSender};
use Ki\Pickaxe;
use Ki\form\FormManager;

class PickaxeSubCommand extends Command implements PluginOwned{
  
  private Pickaxe $plugin;
  
  public function __construct(Pickaxe $plugin){
    
		$this->plugin = $plugin;
		parent::__construct("pickaxe", "Mở Giao Điện Kho Cá Nhân", null, ["cup", "cuplevel", "mocup", "mopickaxe"]);
	}
	public function execute(CommandSender $sender, string $label, array $args){
		if($sender instanceof Player){
		  $form = new FormManager($this->getOwningPlugin());
		  $form->giaoDien($sender);
		  
		}
	}
	public function getOwningPlugin() : Pickaxe{
		return $this->plugin;
	} 
}