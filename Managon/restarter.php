<?php

namespace Managon;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\PluginBase;

class restarter extends PluginBase{
    private $count;
	public function onEnable(){
		$this->getLogger()->info("Started!!");
		if(!file_exists($this->getDataFolder())){
	    @mkdir($this->getDataFolder(), 0744, true);
  }
        $this->config = new Config($this->getDataFolder(). "Config.json", Config::JSON, array("time" => 40,));
        $this->interval();
	}
	public function interval(){
		Server::getInstance()->getScheduler()->scheduleDelayedTask(new interval($this), 40);
	}
	public function start(){
		Server::getInstance()->getScheduler()->scheduleRepeatingTask(new Kick($this), 20);
		$this->count = $this->config->get("time") * 1200;
	}

	public function getCount(){
		return $this->count;
	}

	public function minusCount($value){
		$this->count = $this->count - $value * 20;
	}
    
    public function sendMess($number){
    	Server::getInstance()->broadcastMessage("[Rsr] Server will restart in $number seconds");
    }
}

class interval extends PluginTask{
	public function __construct(PluginBase $owner){
		parent::__construct($owner);
	}
	public function onRun($tick){
		$this->getOwner()->start();
}
}


class Kick extends PluginTask{
	public function __construct(PluginBase $owner){
		parent::__construct($owner);
	}
	public function onRun($tick){
		if($this->getOwner()->getCount() === 0){
			Server::getInstance()->reload();
		    foreach(Server::getInstance()->getOnlinePlayers() as $player){
			       $player->kick("Server Restart..");
		       }
		$this->getOwner()->getLogger()->info("[Rsr] All players kicked");
		$this->getOwner()->getLogger()->info("[Rsr] Done!! Server restarted!");
	   }
	   $count = $this->getOwner()->getCount();
	   switch($count){
	   	case 600:
	   	case 200:
	   	case 100:
	   	case 80:
	   	case 60:
	   	case 40:
	   	case 20:
	   	 $this->getOwner()->sendMess($count / 20);
	   }
       $this->getOwner()->minusCount(1);
   }
}