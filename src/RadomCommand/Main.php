<?php

/*                             Copyright (c) 2017-2018 TeaTech All right Reserved.
 *
 *      ████████████  ██████████           ██         ████████  ██           ██████████    ██          ██
 *           ██       ██                 ██  ██       ██        ██          ██        ██   ████        ██
 *           ██       ██                ██    ██      ██        ██          ██        ██   ██  ██      ██
 *           ██       ██████████       ██      ██     ██        ██          ██        ██   ██    ██    ██
 *           ██       ██              ████████████    ██        ██          ██        ██   ██      ██  ██
 *           ██       ██             ██          ██   ██        ██          ██        ██   ██        ████
 *           ██       ██████████    ██            ██  ████████  ██████████   ██████████    ██          ██
**/

namespace RadomCommand;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;
use pocketmine\Player;
use RadomCommand\CallbackTask;


class Main extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener
{
	public $door = array();
	
	
	public function onLoad()
	{
		$this->getLogger()->info("§eLoading Plugin......");
	}
	
	public $config,$ALL;
	public function onEnable()
	{
		$this->getLogger()->info("§aPlugin Enable!");
		$this->getLogger()->info("§eAuthor: §b§lTeaclon");
		
		if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, 
		[
			"开启功能" => true,
			"概率" => 5,
			"最大概率" => 10,
			"达到概率运行" => 10,
			"时间" => 1,
			"提示" => "时间以分钟为单位.",
			"指令" => 
			[
				"say 我叫小希",
				"say 很高兴成为这个服务器的第591代机器人",
			]
		]);
		$this->ALL = $this->config->getAll();
		
		$this->getLogger()->notice("§b检测配置文件中......");
		if(!$this->config->get("时间") > 0)
		{
			$this->getLogger()->warning("§e有概率执行某任务的时间出现问题, 正在尝试修改为默认值!");
			$this->config->setNested("时间", 1);
			$this->config->save();
			$this->getLogger()->warning("§aOK!");
		}
		else
		{
			$this->getLogger()->info("§e无异常.");
		}
		
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this,"RadomCommands"]),$this->config->get("时间") * 20 * 60);
	}
	
	
	
	
	
	public function RadomCommands()
	{
		if($this->ALL["开启功能"] == true)
		{
			$mt = mt_rand($this->ALL["概率"], $this->ALL["最大概率"]);
			if($mt == $this->ALL["达到概率运行"])
			{
				$radomcmd = $this->ALL["指令"];
				$this->drawRandomCommands($radomcmd);
			}
			else
			{
				$this->getLogger()->info("概率: {$mt}");
			}
		}
	}
	
	
	
	public function drawRandomCommands(array $cmds, $amount = 1)
	{
    	return $this->getServer()->dispatchCommand(new \pocketmine\command\ConsoleCommandSender, $cmds[array_rand($cmds, $amount)]);
    }
	
}


class CallbackTask extends \pocketmine\scheduler\Task
{
	
	protected $callable;
	protected $args;
	
	public function __construct(callable $callable, array $args = [])
	{
		$this->callable = $callable;
		$this->args = $args;
		$this->args[] = $this;
	}
	
	
	public function getCallable()
	{
		return $this->callable;
	}
	
	
	public function onRun(int $currentTicks)
	{
		call_user_func_array($this->callable, $this->args);
	}
	
	
	
	
}
?>

?>