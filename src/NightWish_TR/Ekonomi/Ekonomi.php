<?php

declare(strict_types=1);

namespace NightWish_TR\Ekonomi;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as R;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Ekonomi extends PluginBase{

    /** @var Config */
    public $d;

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->saveResource("Config.yml");
   $this->getLogger()->info("Ekonomi eklentisi aktif ediliyor...");
		$this->d = new Config($this->getDataFolder()."Config.yml", Config::YAML);
	}
	public function onCommand(CommandSender $g, Command $kmt, string $lbl, array $args) : bool{
		if(strtolower($kmt->getName()) == "param"){
			if($g instanceof Player){
				if(!($this->d->get($g->getName()) == null)){
					$this->myMoney($g);
				}else{
					$g->sendMessage("§cHiç paran yok!");
				}
			}else{
				$g->sendMessage("Lütfen bu komutu oyun içinde kullanın.");
			}
		}

		if(strtolower($kmt->getName()) == "paraver"){
			if($g->isOp()){
				if(!(empty($args[0]) || empty($args[1]))){
         if(is_numeric($args[1])){
           $para = (int)$args[1];
				 	  $this->addMoney($this->getServer()->getPlayer($args[0]), $para);
					  $g->sendMessage("§8» §7Başarıyla §e ".$para." §7para verildi!");
         }else{
           $g->sendMessage("Lütfen sayısal bir değer giriniz.");
         }
				}else{
					$g->sendMessage("§cKullanım: §7/paraver <oyuncu> <paramiktarı>");
				}
			}else{
				$g->sendMessage("§8» §cBu komutu kullanmak için yetkilendirilmediniz.");
			}
		}

		if(strtolower($kmt->getName()) == "parakes"){
			if($g->isOp()){
				if(!(empty($args[0]) || empty($args[1]))){
					if(!($this->d->get($this->getServer()->getPlayer($args[0])->getName()) == null)){
           $para = (int)$args[1];
						$this->reduceMoney($this->getServer()->getPlayer($args[0]), $para);
						$g->sendMessage("§8» §e".$para. "§7TL başarıyla kesildi!");
					}else{
						$g->sendMessage("§8» §cBu oyuncunun kaydı veritabanında bulunamadı.");
					}
				}else{
					$g->sendMessage("§cKullanım: §7/parakes <oyuncu> <jetonmiktarı>");
				}
			}else{
				$g->sendMessage("§8» §cBu komutu kullanmak için yetkilendirilmediniz.");
			}
		}

		if(strtolower($kmt->getName()) == "parabak"){
			if(!(empty($args[0]))){
				$this->seeMoney($this->getServer()->getPlayer($args[0]), $g);
			}else{
				$g->sendMessage("§cKullanım: §7/parabak <oyuncu>");
			}
		}
		return true;
	}

       /*>>>>>>>>>>>>>>>>>>>>>>>>>> Events <<<<<<<<<<<<<<<<<<<<<<<<<<<< */

	public function myMoney(Player $g){
		$para = $this->d->get($g->getName());
	     $g->sendMessage("§8» §7Param §e".$para." §7TL");
	}

	public function seeMoney(Player $g, Player $p){
		$para = $this->d->get($g->getName());
		$sonuc = $para == null ? "§8» §cBu oyuncunun hiç parası yok." : "§8» §e".$g->getName().R::GREEN." §7Parası: §e".$para." §7TL";
		$p->sendMessage($sonuc);
	}

	public function addMoney(Player $g, int $para = 0){
		$miktar = $this->d->get($g->getName());
		if($miktar == null){
			$miktar = 0;
		}
		$this->d->set($g->getName(), $miktar+$para);
		$this->d->save();
		$g->sendMessage("§8» §7Hesabınıza §e".$para." §7TL para eklendi.");
	}

	public function reduceMoney(Player $g, int $para = 0){
		$this->d->set($g->getName(), $this->d->get($g->getName())-$para);
		$this->d->save();
		$g->sendMessage("§8» §7Hesabınızdan §e".$para." §7TL para kesildi.");
	}
}