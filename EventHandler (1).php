<?php

/** 
 

███████╗██╗░░░░░██╗████████╗███████╗
██╔════╝██║░░░░░██║╚══██╔══╝██╔════╝
█████╗░░██║░░░░░██║░░░██║░░░█████╗░░
██╔══╝░░██║░░░░░██║░░░██║░░░██╔══╝░░
███████╗███████╗██║░░░██║░░░███████╗
╚══════╝╚══════╝╚═╝░░░╚═╝░░░╚══════╝*/
namespace elitegames;

use pocketmine\Server;
use pocketmine\player\Player;

use rank\Ranks;
use elitegames\API;
use elitegames\elitegames;
use elitegames\entity\Worker;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\item\ItemBlock;
use elitegames\entity\Assistant;
use elitegames\api\VariablesAPI;
use pocketmine\item\ItemFactory;
use elitegames\task\InterestTask;
use elitegames\entity\travel\Ship;
use elitegames\entity\tile\WorkerChestTile;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;

use pocketmine\item\Armor;
use pocketmine\math\Facing;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\entity\Living;
use pocketmine\world\Position;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\scheduler\ClosureTask;
use elitegames\menu\gui\pages\AnvilMenu;
use pocketmine\inventory\ArmorInventory;
use elitegames\menu\gui\pages\PlayerMenu;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\setActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
//use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\world\ChunkUnloadEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\world\World;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\math\AxisAlignedBB;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use muqsit\pmarmorstand\event\PlayerChangeArmorStandArmorEvent;
use muqsit\pmarmorstand\event\PlayerChangeArmorStandHeldItemEvent;
//use cooldogedev\BedrockEconomy\event\transaction\TransactionSubmitEvent;

class EventHandler implements Listener
{
  
  /** @var Armor Enchants */
  const ARMOR = array
  (
    "Helmet" => array(1, 2, 4, 5, 6, 14, 18),
    "Chestplate" => array(2, 4, 5, 6, 14, 18),
    "Leggings" => array(2, 4, 5, 6, 14, 18),
    "Boots" => array(2, 3, 4, 5, 6, 14, 18)
  );
  
  /** @var Tools Enchants */
  const TOOL = array
  (
    15,
    16,
    17,
    18
  );
  
  /** @var Weapon Enchants */
  const WEAPON = array
  (
    "Sword" => array(11, 12, 13, 15, 18),
    "Bow" => array(7, 8, 9, 10, 1),
    "Axe" => array(13, 15, 18)
  );
  
  /** @var Enchantments Level */
  const ENCHLEVEL = array
  (
    "respiration" => 1,
    "blast_protection" => 8,
    "feather_falling" => 4,
    "thorns" => 2,
    "fire_protection" => 16,
    "protection" => 26,
    "power" => 12,
    "punch" => 8,
    "flame" => 6,
    "infinity" => 1,
    "knockback" => 4,
    "fire_aspect" => 6,
    "sharpness" => 12,
    "projectile_protection" => 8,
    "unbreaking" => 32,
    "silk_touch" => 1,
    "efficiency" => 26,
    "mending" => 1
  );
  
  /** @var Weapon Enchants */
  const ENCHANTMENTIDS = array
  (
    1 => "respiration",
    2 => "blast_protection",
    3 => "feather_falling",
    4 => "thorns",
    5 => "fire_protection",
    6 => "protection",
    7 => "power",
    8 => "punch",
    9 => "flame",
    10 => "infinity",
    11 => "knockback",
    12 => "fire_aspect",
    13 => "sharpness",
    14 => "projectile_protection",
    15 => "unbreaking",
    16 => "silk_touch",
    17 => "efficiency",
    18 => "mending"
  );
  
  /** @var Worker Ids */
  const WORKERIDS = array
  (
    1 => array("1", "0", "Miner"),
    2 => array("4", "0", "Miner"),
    3 => array("16", "0", "Miner"),
    4 => array("14", "0", "Miner"),
    5 => array("15", "0", "Miner"),
    6 => array("21", "0", "Miner"),
    7 => array("56", "0", "Miner"),
    8 => array("129", "0", "Miner"),
    9 => array("1", "1", "Miner"),
    10 => array("1", "3", "Miner"),
    11 => array("1", "5", "Miner"),
    12 => array("244", "7", "Farmer"),
    13 => array("59",  "7", "Farmer"),
    14 => array("141", "7", "Farmer"),
    15 => array("142", "7", "Farmer"),
    16 => array("6", "0", "Lumberjack"),
    17 => array("6", "1", "Lumberjack"),
    18 => array("6", "2", "Lumberjack"),
    19 => array("6", "3", "Lumberjack"),
    20 => array("6", "4", "Lumberjack"),
    21 => array("6", "5", "Lumberjack"),
    22 => array("Entity", "Zombie", "Slayyer"),
    23 => array("Entity", "Skeleton", "Slayyer"),
    24 => array("Entity", "Spider", "Slayyer"),
    25 => array("Entity", "EnderMan", "Slayyer"),
    26 => array("Entity", "Blaze", "Slayyer"),
    27 => array("Entity", "WitherSkeleton", "Slayyer"),
    28 => array("Entity", "Cow", "Slayyer"),
    29 => array("Entity", "Pig", "Slayyer"),
    30 => array("Entity", "Sheep", "Slayyer")
  );
  
  /** @var Instance */
  private static $instance;
  
  /** @var TaskHandler */
  //public $interest;
  
  /** @var array */
  public $builderWandInv = [];
  
  /** @var API */
  public $api;
  
  /** @var elitegames */
  private $source;
  
  /** @var Config */
  public $players;
  
  /** @var Config */
  public $config;
  
  public function __construct(elitegames $source)
  {
    self::$instance = $this;
    $this->source = $source;
    $this->interest = [];
    $this->api = $source->getAPI();
    $this->config = $source->getConfigFile();
  }
  
  public static function getInstance(): EventHandler
  {
    return self::$instance;
  }
  
  public function onPreLogin(PlayerPreLoginEvent $event)
  {
    $array = array("Owner", "Co-Owner", "Staff", "Mod", "Helper", "Partner", "Sroudy", "Mvp", "YouTube");
    $PlayerName = $event->getPlayerInfo()->getUsername();
    $Rank = Ranks::getInstance()->getRankOfPlayer($PlayerName);
    if(in_array($Rank, $array))
    {
      $event->clearAllKickReasons();
    }
    if($PlayerName === "Steve")
    {
      $event->setKickReason(0, "You Must Be Authenticated To Microsoft Players Who don't follow our TOS are not allowed into §bElite§6Games§c! ");
    }
  }
  
  public function onJoin(PlayerJoinEvent $event)
  {
    $player = $event->getPlayer();
    $playerName = $player->getName();
   
    $event->setJoinMessage("⨻ $playerName");
    
    if($this->getSource()->getConfigFile()->getNested("Join.CreateIsland"))
    {
      if(!$this->api->haselitegames($player))
      {
        $this->api->registerPlayer($player);
        $this->api->createIsland($player);
      }
    }
    if($this->getSource()->getConfigFile()->getNested("Join.TeleportToIsland"))
    {
      $this->api->teleportToIsland($player, 20);
    }
    if(!is_null($this->getSource()->getPlayerFile($playerName)->get("Friends")))
    {
      if(is_array($this->getSource()->getPlayerFile($playerName)->get("Friends")))
      {
        foreach($this->getSource()->getPlayerFile($playerName)->get("Friends") as $friendName)
        {
          $friend = Server::getInstance()->getPlayerExact($friendName);
          if($friend instanceof Player)
          {
            if($friend->isOnline())
            {
              $friend->sendMessage("§7[⨻§7] §a$playerName");
            }
          }
        }
      }
    }
   
  
    if($this->api->getPlayerCurrentPet($player) !== null)
    {
      $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
        function () use ($player): void 
        {
          $pet = $this->api->getPlayerCurrentPet($player);
          $this->api->spawnPet($player, $pet);
        }
      ), 20);
    }
    $inv = $player->getInventory();
    $SkyBlockItemId = $this->config->getNested("SkyBlockItem.Id");
    $SkyBlockItemMeta = $this->config->getNested("SkyBlockItem.Meta");
    $inv->setItem(8, ItemFactory::getInstance()->get($SkyBlockItemId, $SkyBlockItemMeta, 1)->setCustomName("§r §bElite§eGames §r"));
    //$API = API::getBalanceAPI($player);
   // $API->registerBalance();
  }
  
  public function onQuit(PlayerQuitEvent $event)
  {
    $player = $event->getPlayer();
    $playerName = $player->getName();
    $event->setQuitMessage("⩃ $playerName");
      
    if(!is_null($this->getSource()->getPlayerFile($playerName)->get("Friends")))
    {
      if(is_array($this->getSource()->getPlayerFile($playerName)->get("Friends")))
      {
        foreach($this->getSource()->getPlayerFile($playerName)->get("Friends") as $friendName)
        {
          $friend = Server::getInstance()->getPlayerExact($friendName);
          if($friend instanceof Player)
          {
            $friend->sendMessage("§7[⩌§7] §c$playerName");
          }
        }
      }
    }
   // if(isset($this->interest[$playerName]))
    //{
   //   $this->interest[$playerName]->cancel(); 
   // }
    $Variables = VariablesAPI::getInstance();
    $KeyExists = $Variables->hasKey("Pets", $player->getName());
    if($KeyExists)
    {
      $PetArray = $Variables->getVariable("Pets");
      $Pet = $PetArray[$player->getName()];
      unset($PetArray[$player->getName()]);
      $Variables->setVariable("Pets", $PetArray);
      if(!$Pet->isClosed())
      {
        $Pet->flagForDespawn();
      }
    }
    
    $elitegames = $this->getSource();
    //$API = API::getBalanceAPI($player);
    //$API->unregisterBalance();
  }
  
  public function onBreak(BlockBreakEvent $event)
  {
    
    $block = $event->getBlock();
    $player = $event->getPlayer();
    $playerLevel = $this->api->getLevel($player, "Miner");
    $playerfLevel = $this->api->getLevel($player, "Farmer");
    $playerXp = $this->api->changeNumericFormat($this->api->getXp($player, "Miner"), "k");
    $playerfXp = $this->api->changeNumericFormat($this->api->getXp($player, "Farmer"), "k");
    $playerlLevel = $this->api->getLevel($player, "Lumberjack");
    $playerlXp = $this->api->changeNumericFormat($this->api->getXp($player, "Lumberjack"), "k");
    $requiredXp = $this->api->changeNumericFormat(($playerLevel * $this->config->get("XpPerLevel")), "k");
    $requiredfXp = $this->api->changeNumericFormat(($playerfLevel * $this->config->get("XpPerLevel")), "k");
    $requiredlXp = $this->api->changeNumericFormat(($playerlLevel * $this->config->get("XpPerLevel")), "k");
    $playerName = $player->getName();
    if($player->getWorld()->getFolderName() === Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName() || $player->getWorld()->getFolderName() === "Mines")
    {
      if($player->hasPermission("sroudy.builder"))
      {
        $event->uncancel();
      }else{
        $event->cancel();
      }
      switch($block->getId())
      {
        case BlockLegacyIds::STONE:
        case BlockLegacyIds::COAL_ORE:
        case BlockLegacyIds::IRON_ORE:
        case BlockLegacyIds::GOLD_ORE:
        case BlockLegacyIds::LAPIS_ORE:
        case BlockLegacyIds::EMERALD_ORE:
        case BlockLegacyIds::DIAMOND_ORE:
        case BlockLegacyIds::COBBLESTONE:
        case BlockLegacyIds::REDSTONE_ORE:
        case BlockLegacyIds::LIT_REDSTONE_ORE:
        $player->sendTip("§b+1 §d Miner §7(§a §8$playerXp/$requiredXp §7)");
          if($this->api->getLevel($player, "Miner") !== 32)
          {
            $this->api->addXp($player, "Miner", 1);
            if($this->api->getXp($player, "Miner") >= ($this->api->getLevel($player, "Miner") * $this->config->get("XpPerLevel")))
            {
              $this->api->setXp($player, "Miner", 0);
              $this->api->addLevel($player, "Miner", 1);
              $this->api->sendLevelUpMessage($player, "Miner");
             // $player->sendMessage("§b+1 §d Miner §7(§a$playerXp/$requiredXp §7)");
            }
          }
          if($this->api->getObjective($player) === "Mine-Iron")
          {
            if($block->getId() === 15)
            {
              $nextObjective = $this->api->getNextObjective($player);
              $this->api->setObjective($player, $nextObjective);
            }
          }
          $event->uncancel();
          break;
        case BlockLegacyIds::WHEAT_BLOCK:
        case BlockLegacyIds::CARROT_BLOCK:
        case BlockLegacyIds::POTATO_BLOCK:
        case BlockLegacyIds::BEETROOT_BLOCK:
          $player->sendTip("§b+1 §d Farmer §7(§a §8$playerfXp/$requiredfXp §7)");
          if($block->getMeta() === 7)
          {
            if($this->api->getLevel($player, "Farmer") !== 32)
            {
              $this->api->addXp($player, "Farmer", 1);
              if($this->api->getXp($player, "Farmer") >= ($this->api->getLevel($player, "Farmer") * $this->config->get("XpPerLevel")))
              {
                $this->api->setXp($player, "Farmer", 0);
                $this->api->addLevel($player, "Farmer", 1);
                $this->api->sendLevelUpMessage($player, "Farmer");
              }
            }
            if($this->api->getObjective($player) === "Farm-Wheat")
            {
              if($block->getId() === 59)
              {
                $nextObjective = $this->api->getNextObjective($player);
                $this->api->setObjective($player, $nextObjective);
              }
            }
          }
          $event->uncancel();
        case BlockLegacyIds::PUMPKIN:
        case BlockLegacyIds::MELON_BLOCK:
          $player->sendTip("§b+1 §d Farmer §7(§a §8$playerfXp/$requiredfXp §7)");
          if($this->api->getLevel($player, "Farmer") !== 32)
          {
            $this->api->addXp($player, "Farmer", 1);
            if($this->api->getXp($player, "Farmer") >= ($this->api->getLevel($player, "Farmer") * $this->config->get("XpPerLevel")))
            {
              $this->api->setXp($player, "Farmer", 0);
              $this->api->addLevel($player, "Farmer", 1);
              $this->api->sendLevelUpMessage($player, "Farmer");
            }
          }
          $event->uncancel();
          break;
        case BlockLegacyIds::LOG:
          $player->sendTip("§b+1 §d Lumberjack §7(§a §8$playerlXp/$requiredlXp §7)");
          if($this->api->getLevel($player, "Miner") !== 32)
          {
            $this->api->addXp($player, "Lumberjack", 1);
            if($this->api->getXp($player, "Lumberjack") >= ($this->api->getLevel($player, "Lumberjack") * $this->config->get("XpPerLevel")))
            {
              $this->api->setXp($player, "Lumberjack", 0);
              $this->api->addLevel($player, "Lumberjack", 1);
              $this->api->sendLevelUpMessage($player, "Lumberjack");
            }
          }
          if($this->api->getObjective($player) === "Break-Log")
          {
            if($block->getId() === 17 || $block->getId() === 467)
            {
              $player->sendMessage("§a---------------------------------------------\n§l§eQuest Accomplished!\n§r\n§bQuest: §aBreak A Log\n§6Next Quest: §aCraft A WorkBench\n§r\n§a---------------------------------------------");
              $nextObjective = $this->api->getNextObjective($player);
              $this->api->setObjective($player, $nextObjective);
            }
          }
          $event->uncancel();
          break;
      }
    }elseif($player->getLocation()->world->getFolderName() === $this->getSource()->getPlayerFile($playerName)->get("Island"))
    {
      if($this->api->getCoOpRole($player) === "Owner" || $this->api->getCoOpRole($player) === "Co-Owner" || $this->api->hasCoOpPerm($player, "Interact"))
      {
        $x = $block->getPosition()->getX();
        $y = $block->getPosition()->getY();
        $z = $block->getPosition()->getZ();
        $world = $block->getPosition()->getWorld()->getFolderName();
        if($this->api->IsUnbreakable($x, $y, $z, $world))
        {
          $event->cancel();
        }else{
          $event->uncancel();
          if($this->api->getObjective($player) === "Break-Log")
          {
            if($block->getId() === 17 || $block->getId() === 467)
            {
              $nextObjective = $this->api->getNextObjective($player);
              $this->api->setObjective($player, $nextObjective);
            }
          }
        }
      }else{
        $event->cancel();
      }
    }else{
      if(Server::getInstance()->isOp($player->getPlayerInfo()->getUsername()))
      {
        $event->uncancel();
      }else{
        $event->cancel();
      }
    }
    if(!$event->isCancelled())
    {
      $array = array();
      $drops = $event->getDrops();
      foreach($drops as $drop)
      {
        $drop->getName();
        $array[] = $drop;
      }
      $event->setDrops($array);
      if($block->getId() === 54)
      {
        $Position = $block->getPosition();
        $World = $Position->getWorld();
        $Tile = $World->getTile($Position);
        if($Tile instanceof WorkerChestTile)
        {
          $Inv = $Tile->getInv();
          foreach(array_reverse($Inv->getContents(), true) as $_ => $Item)
          {
            $World->dropItem($Position, $Item);
            $Inv->removeItem($Item);
          }
          $BlockFactory = BlockFactory::getInstance();
          $Chest = $BlockFactory->get(54, 0);
          $Item = $Chest->asItem();
          $event->setDrops([$Item->setCustomName("§r §eWorker-Chest §r")]);
        }
      }
    }
    if(!$event->isCancelled())
    {
      $item = $player->getInventory()->getItemInHand();
      if($item->getCustomName() === "§r §eFarmer Hoe §r\n§r §7 §r\n§r - §e40% Chance Of Double Drops §r\n§r - §eHoes 3x3 Area §r\n§r §7 §r\n§r §l§c §r")
      {
        $rand = mt_rand(1, 5);
        if(in_array($rand, array(3, 1)))
        {
          $drops = $event->getDrops();
          $array = array();
          foreach($drops as $drop)
          {
            $array[] = $drop->setCount($drop->getCount() * 2);
          }
          $event->setDrops($array);
        }
      }elseif($item->getCustomName() === "§r §6Lumberjack Axe §r\n§r §7 §r\n§r - §e36% Chance Of Double Drops §r\n§r - §e20% Chance Of Double Chopping §r\n§r §7 §r\n§r §l§c §r")
      {
        $rand = mt_rand(1, 10);
        if(in_array($rand, array(2, 5, 7)))
        {
          $drops = $event->getDrops();
          $array = array();
          foreach($drops as $drop)
          {
            $array[] = $drop->setCount($drop->getCount() * 2);
          }
          $event->setDrops($array);
        }
      }elseif($item->getCustomName() === "§r §bMiner Pickaxe §r\n§r §7 §r\n§r - §e25% Chance Of Double Drops §r\n§r - §eAuto Smelter §r\n§r §7 §r\n§r §l§c §r")
      {
        if(in_array($block->getId(), array(15, 14)))
        {
          if($block->getId() === 14)
          {
            $item = Vanillaitems::GOLD_INGOT();
            $event->setDrops([$item]);
          }elseif($block->getId() === 15)
          {
            $item = Vanillaitems::IRON_INGOT();
            $event->setDrops([$item]);
          }
        }
        $rand = mt_rand(1, 5);
        if($rand === 4)
        {
          $drops = $event->getDrops();
          $array = array();
          foreach($drops as $drop)
          {
            if($drop->getId() !== 54)
            {
              $array[] = $drop->setCount($drop->getCount() * 2);
            }else{
              $array[] = $drop;
            }
          }
          $event->setDrops($array);
        }
      }
    }
    $item = $player->getInventory()->getItemInHand();
    if($item->getId() === 369 && $item->getMeta() === 0 && $item->getCustomName() === "§r §eBuilder Wand §r\n§r §7 §r\n§r §7- Left-Click To Open GUI §r\n§r §7- Right-Click To Use §r\n§r §7 §r\n§r §l§c §r")
    {
      $event->cancel();
    }
  }
  
  public function onUpdate(BlockUpdateEvent $event)
  {
    $block = $event->getBlock();
    if($block->getId() === 65 && $block->getId() === 66)
    {
      $event->cancel();
    }
  }
  
  public function onPlace(BlockPlaceEvent $event)
  {
    $block = $event->getBlock();
    $player = $event->getPlayer();
    $playerName = $player->getName();
    
    if($player->getWorld()->getFolderName() === $this->getSource()->getPlayerFile($playerName)->get("Island"))
    {
      if($this->api->getCoOpRole($player) === "Owner" || $this->api->getCoOpRole($player) === "Co-Owner" || $this->api->hasCoOpPerm($player, "Build"))
      {
        if($block->getId() === BlockLegacyIds::TNT)
        {
          $event->cancel();
        }else{
          $x = $block->getPosition()->getX();
          $y = $block->getPosition()->getY();
          $z = $block->getPosition()->getZ();
          $world = $block->getPosition()->getWorld()->getFolderName();
          if($this->api->IsUnbreakable($x, $y, $z, $world))
          {
            $event->cancel();
          }else{
            $item = $player->getInventory()->getItemInHand();
            if($item->getId() === 122 && $item->getMeta() >= 1)
            {
              if($player->getWorld()->getFolderName() === $this->getSource()->getPlayerFile($playerName)->get("Island"))
              {
                $workers = $this->api->getWorkers($player->getWorld());
                if(count($workers) < 15)
                {
                  $event->cancel();
                  if(!is_null($item->getNamedTag()->getTag("Information")))
                  {
                    $Level = $item->getNamedTag()->getTag("Information")->getInt("Level");
                    $InvSize = $item->getNamedTag()->getTag("Information")->getInt("InvSize");
                  }else{
                    $Level = 1;
                    $InvSize = 3;
                  }
                    $contents = "null";
                    $Upgrades = "Null, Null, Null";
                    $Target = self::WORKERIDS[$item->getMeta()];
                    $nbt = CompoundTag::create()
                      ->setTag("Information", 
                      CompoundTag::create()
                        ->setInt("Level", $Level)
                        ->setInt("InvSize", $InvSize)
                        ->setString("Type", $Target[2])
                        ->setString("Upgrades", $Upgrades)
                        ->setString("Resources", $contents)
                        ->setString("TargetId", $Target[0])
                        ->setString("TargetMeta", $Target[1])
                        ->setString("Owner", $player->getName())
                   );
                   $position = $block->getPosition();
                   $location = new Location((float) ($position->getX() + 0.5), (float) $position->getY(), (float) ($position->getZ() + 0.5), $position->getWorld(), 0.0, 0.0);
                   $WorkerItem = $player->getInventory()->getItemInHand();
                   $player->getInventory()->setItemInHand($WorkerItem->setCount($WorkerItem->getCount() - 1));
                   $entity = new Worker($location, $player->getSkin(), $nbt);
                   $entity->spawnToAll();
                   if($this->api->getObjective($player) === "Aquire-Diamond-Worker")
                   {
                     if($Target[0] === 56)
                     {
                       $nextObjective = $this->api->getNextObjective($player);
                       $this->api->setObjective($player, $nextObjective);
                      }
                   }
                   if($Target[2] === "Miner")
                   {
                     $player->getWorld()->setBlock($position->subtract(0, 1, 0), BlockFactory::getInstance()->get(57, 0));
                     $this->api->addUnbreakable($position->subtract(0, 1, 0), $player->getWorld());
                   }elseif($Target[2] === "Farmer"){
                     $player->getWorld()->setBlock($position, BlockFactory::getInstance()->get(111, 0));
                     $player->getWorld()->setBlock($position->subtract(0, 1, 0), BlockFactory::getInstance()->get(9, 0));
                     $this->api->addUnbreakable($position, $player->getWorld());
                     $this->api->addUnbreakable($position->subtract(0, 1, 0), $player->getWorld());
                   }elseif($Target[2] === "Lumberjack")
                   {
                     $player->getWorld()->setBlock($position->subtract(0, 1, 0), BlockFactory::getInstance()->get(57, 0));
                     $this->api->addUnbreakable($position->subtract(0, 1, 0), $player->getWorld());
                   }elseif($Target[2] === "Slayyer")
                   {
                     $player->getWorld()->setBlock($position->subtract(0, 1, 0), BlockFactory::getInstance()->get(57, 0));
                     $this->api->addUnbreakable($position->subtract(0, 1, 0), $player->getWorld());
                   }
                 }else{
                   $event->cancel();
                   $player->sendMessage("§c⩕ You Can't Place More Than 15 Workers in your island.");
                 }
              }else{
                 $event->cancel();
                 $player->sendMessage("§c⩕ You Can't Place Worker Here");
               }
             }elseif($item->getId() === 54 && $item->getMeta() === 0 && $item->getName() === "§r §eWorker-Chest §r")
             {
               $event->cancel();
               $Inventory = $player->getInventory();
               $Item = $Inventory->getItemInHand();
               $Count = $Item->getCount();
               $Count--;
               $Item->setCount($Count);
               $World = $player->getWorld();
               $BlockFactory = BlockFactory::getInstance();
               $Chest = $BlockFactory->get(54, 0);
               $CanPlace = true;
               for($i = 1; $i <= 4; $i++)
               {
                 for($x = -1; $x <= 1; $x++)
                 {
                   if($x === 0)
                   {
                     continue;
                   }
                   if($i === 1){ $xx = 1; $zz = 0; }
                   if($i === 2){ $xx = -1; $zz = 0; }
                   if($i === 3){ $xx = 0; $zz = 1; }
                   if($i === 4){ $xx = 0; $zz = -1; }
                   $Pos = $block->getPosition();
                   $Poss = new Position($Pos->getX() + $xx, $Pos->getY() , $Pos->getZ() + $zz, $Pos->getWorld());
                   $Position = new Position($Poss->getX() + $x, $Poss->getY(), $Poss->getZ(), $Pos->getWorld());
                   $Tile = $World->getTile($Position);
                   if($Tile instanceof WorkerChestTile)
                   {
                     $CanPlace = false;
                     break;
                   }
                 }
                 
                 for($z = -1; $z <= 1; $z++)
                 {
                   if($z === 0)
                   {
                     continue;
                   }
                   if($i === 1){ $xx = 1; $zz = 0; }
                   if($i === 2){ $xx = -1; $zz = 0; }
                   if($i === 3){ $xx = 0; $zz = 1; }
                   if($i === 4){ $xx = 0; $zz = -1; }
                   $Pos = $block->getPosition();
                   $Poss = new Position($Pos->getX() + $xx, $Pos->getY(), $Pos->getZ() + $zz, $Pos->getWorld());
                   $Position = new Position($Poss->getX(), $Poss->getY(), $Poss->getZ() + $z, $Pos->getWorld());
                   $Tile = $World->getTile($Position);
                   if($Tile instanceof WorkerChestTile)
                   {
                     $CanPlace = false;
                     break;
                   }
                 }
                 
                 if(!$CanPlace)
                 {
                   break;
                 }
               }
               if($CanPlace)
               {
                 $Position = $block->getPosition();
                 $World->setBlock($Position, $Chest);
                 $Old_Tile = $World->getTile($Position);
                 
                 if(!is_null($Old_Tile))
                 {
                   $Old_Tile->close();
                 }
                 
                 $Tile = new WorkerChestTile($World, $Position, $player);
                 $Inventory->setItemInHand($Item);
                 $World->addTile($Tile);
               }
             }else{
               $event->uncancel();
             }
           }
         }
      }else{
        $event->cancel();
      }
    }else{
      if($player->hasPermission("sroudy.builder"))
      {
        $event->uncancel();
      }else{
        $event->cancel();
      }
    }
  }
  
  public function onTraple(EntityTrampleFarmlandEvent $event)
  {
    $event->cancel();
  }
  
  public function onDecay(LeavesDecayEvent $event)
  {
    $event->cancel();
  }
  
  public function onInteract(PlayerInteractEvent $event)
  {
    $item = $event->getItem();
    $block = $event->getBlock();
    $player = $event->getPlayer();
    $playerName = $player->getName();
    $SkyBlockItemId = $this->config->getNested("SkyBlockItem.Id");
    $SkyBlockItemMeta = $this->config->getNested("SkyBlockItem.Meta");
    if($item->getId() === $SkyBlockItemId && $item->getMeta() === $SkyBlockItemMeta)
    {
      $PlayerInfo = API::getPlayerInfo($player);
      $File = $PlayerInfo->getFile();
      $Menu = $File->getMenu();
      if($Menu === "GUI")
      {
        $GUI = $this->getSource()->getGUI();
        $GUI->MainGUI($player);
      }
    }elseif($item->getId() === 369 && $item->getMeta() === 0 && $item->getCustomName() === "§r §eBuilder Wand §r\n§r §7 §r\n§r §7- Left-Click To Open GUI §r\n§r §7- Right-Click To Use §r\n§r §7 §r\n§r §l§c §r")
    {
      if($player->getWorld()->getFolderName() === $this->getSource()->getPlayerFile($player)->get("Island"))
      {
        if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK)
        {
          $GUI = $this->getSource()->getGUI();
          $GUI->BuilderWandMenu($player, $item);
        }elseif($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK)
        {
          if(!is_null($item->getNamedTag()->getTag("WandId")))
          {
          if(array_key_exists($item->getNamedTag()->getString("WandId"), $this->builderWandInv))
          {
            $lessItem = false;
            $Position = $block->getPosition();
            $inv = $this->builderWandInv[$item->getNamedTag()->getString("WandId")];
            $blockItem = $block->asItem();
            if($this->api->hasItem($blockItem, $inv))
            {
              if($event->getFace() === Facing::UP)
              {
                for($x = -10; $x <= 10; $x++)
                {
                  for($z = -10; $z <= 10; $z++)
                  {
                    $Pos = new Vector3($Position->getX() + $x, $Position->getY(), $Position->getZ() + $z);
                    $b_Block = $player->getWorld()->getBlock($Pos->add(0.5, 0, 0.5));
                    if($block->getId() === $b_Block->getId() && $block->getMeta() === $b_Block->getMeta())
                    {
                      $canBuild = $this->api->isConnected(array($b_Block), $block, $player->getWorld(), 1);
                      if($canBuild)
                      {
                        if($player->getWorld()->getBlock($Pos->add(0, 1, 0))->getId() === 0)
                        {
                          if($this->api->hasItem($blockItem, $inv))
                          {
                            $player->getWorld()->setBlock($Pos->add(0, 1, 0), $block);
                            $this->api->removeItem($inv, false, $blockItem);
                            $wandItem = $this->api->matchItem($item, $player->getInventory());
                            $nbt = $wandItem[1]->getNamedTag();
                            $nbt->setString("WandInv", serialize($this->builderWandInv[$item->getNamedTag()->getString("WandId")]->getContents()));
                            $player->getInventory()->setItem($wandItem[0], $wandItem[1]->setNamedTag($nbt));
                          }else{
                            $lessItem = true;
                          }
                        }
                      }
                    }
                  }
                }
              }elseif($event->getFace() === Facing::DOWN)
              {
                for($x = -10; $x <= 10; $x++)
                {
                  for($z = -10; $z <= 10; $z++)
                  {
                    $Pos = new Vector3($Position->getX() + $x, $Position->getY(), $Position->getZ() + $z);
                    $b_Block = $player->getWorld()->getBlock($Pos);
                    if($block->getId() === $b_Block->getId() && $block->getMeta() === $b_Block->getMeta())
                    {
                      $canBuild = $this->api->isConnected(array($b_Block), $block, $player->getWorld(), 1);
                      if($canBuild)
                      {
                        if($player->getWorld()->getBlock($Pos->subtract(0, 1, 0))->getId() === 0)
                        {
                          if($this->api->hasItem($blockItem, $inv))
                          {
                            $player->getWorld()->setBlock($Pos->subtract(0, 1, 0), $block);
                            $this->api->removeItem($inv, false, $blockItem);
                            $wandItem = $this->api->matchItem($item, $player->getInventory());
                            $nbt = $wandItem[1]->getNamedTag();
                            $nbt->setString("WandInv", serialize($this->builderWandInv[$item->getNamedTag()->getString("WandId")]->getContents()));
                            $player->getInventory()->setItem($wandItem[0], $wandItem[1]->setNamedTag($nbt));
                          }else{
                            $lessItem = true;
                          }
                        }
                      }
                    }
                  }
                }
              }elseif($event->getFace() === Facing::NORTH)
              {
                for($x = -10; $x <= 10; $x++)
                {
                  for($y = -10; $y <= 10; $y++)
                  {
                    $Pos = new Vector3($Position->getX() + $x, $Position->getY() + $y, $Position->getZ());
                    $b_Block = $player->getWorld()->getBlock($Pos);
                    if($block->getId() === $b_Block->getId() && $block->getMeta() === $b_Block->getMeta())
                    {
                      $canBuild = $this->api->isConnected(array($b_Block), $block, $player->getWorld(), 1);
                      if($canBuild)
                      {
                        if($player->getWorld()->getBlock($Pos->subtract(0, 0, 1))->getId() === 0)
                        {
                          if($this->api->hasItem($blockItem, $inv))
                          {
                            $player->getWorld()->setBlock($Pos->subtract(0, 0, 1), $block);
                            $this->api->removeItem($inv, false, $blockItem);
                            $wandItem = $this->api->matchItem($item, $player->getInventory());
                            $nbt = $wandItem[1]->getNamedTag();
                            $nbt->setString("WandInv", serialize($this->builderWandInv[$item->getNamedTag()->getString("WandId")]->getContents()));
                            $player->getInventory()->setItem($wandItem[0], $wandItem[1]->setNamedTag($nbt));
                          }else{
                            $lessItem = true;
                          }
                        }
                      }
                    }
                  }
                }
              }elseif($event->getFace() === Facing::SOUTH)
              {
                for($x = -10; $x <= 10; $x++)
                {
                  for($y = -10; $y <= 10; $y++)
                  {
                    $Pos = new Vector3($Position->getX() + $x, $Position->getY() + $y, $Position->getZ());
                    $b_Block = $player->getWorld()->getBlock($Pos);
                    if($block->getId() === $b_Block->getId() && $block->getMeta() === $b_Block->getMeta())
                    {
                      $canBuild = $this->api->isConnected(array($b_Block), $block, $player->getWorld(), 1);
                      if($canBuild)
                      {
                        if($player->getWorld()->getBlock($Pos->add(0, 0, 1))->getId() === 0)
                        {
                          if($this->api->hasItem($blockItem, $inv))
                          {
                            $player->getWorld()->setBlock($Pos->add(0, 0, 1), $block);
                            $this->api->removeItem($inv, false, $blockItem);
                            $wandItem = $this->api->matchItem($item, $player->getInventory());
                            $nbt = $wandItem[1]->getNamedTag();
                            $nbt->setString("WandInv", serialize($this->builderWandInv[$item->getNamedTag()->getString("WandId")]->getContents()));
                            $player->getInventory()->setItem($wandItem[0], $wandItem[1]->setNamedTag($nbt));
                          }else{
                            $lessItem = true;
                          }
                        }
                      }
                    }
                  }
                }
              }elseif($event->getFace() === Facing::WEST)
              {
                for($z = -10; $z <= 10; $z++)
                {
                  for($y = -10; $y <= 10; $y++)
                  {
                    $Pos = new Vector3($Position->getX(), $Position->getY() + $y, $Position->getZ() + $z);
                    $b_Block = $player->getWorld()->getBlock($Pos);
                    if($block->getId() === $b_Block->getId() && $block->getMeta() === $b_Block->getMeta())
                    {
                      $canBuild = $this->api->isConnected(array($b_Block), $block, $player->getWorld(), 1);
                      if($canBuild)
                      {
                        if($player->getWorld()->getBlock($Pos->subtract(1, 0, 0))->getId() === 0)
                        {
                          if($this->api->hasItem($blockItem, $inv))
                          {
                            $player->getWorld()->setBlock($Pos->subtract(1, 0, 0), $block);
                            $this->api->removeItem($inv, false, $blockItem);
                            $wandItem = $this->api->matchItem($item, $player->getInventory());
                            $nbt = $wandItem[1]->getNamedTag();
                            $nbt->setString("WandInv", serialize($this->builderWandInv[$item->getNamedTag()->getString("WandId")]->getContents()));
                            $player->getInventory()->setItem($wandItem[0], $wandItem[1]->setNamedTag($nbt));
                          }else{
                            $lessItem = true;
                          }
                        }
                      }
                    }
                  }
                }
              }elseif($event->getFace() === Facing::EAST)
              {
                for($z = -10; $z <= 10; $z++)
                {
                  for($y = -10; $y <= 10; $y++)
                  {
                    $Pos = new Vector3($Position->getX(), $Position->getY() + $y, $Position->getZ() + $z);
                    $b_Block = $player->getWorld()->getBlock($Pos);
                    if($block->getId() === $b_Block->getId() && $block->getMeta() === $b_Block->getMeta())
                    {
                      $canBuild = $this->api->isConnected(array($b_Block), $block, $player->getWorld(), 1);
                      if($canBuild)
                      {
                        if($player->getWorld()->getBlock($Pos->add(1, 0, 0))->getId() === 0)
                        {
                          if($this->api->hasItem($blockItem, $inv))
                          {
                            $player->getWorld()->setBlock($Pos->add(1, 0, 0), $block);
                            $this->api->removeItem($inv, false, $blockItem);
                            $wandItem = $this->api->matchItem($item, $player->getInventory());
                            $nbt = $wandItem[1]->getNamedTag();
                            $nbt->setString("WandInv", serialize($this->builderWandInv[$item->getNamedTag()->getString("WandId")]->getContents()));
                            $player->getInventory()->setItem($wandItem[0], $wandItem[1]->setNamedTag($nbt));
                          }else{
                            $lessItem = true;
                          }
                        }
                      }
                    }
                  }
                }
              }
              if($lessItem)
              {
                $player->sendMessage("§c⩕ You have used all the blocks");
              }
            }else{
              $player->sendMessage("§c⩕ You don't have that block");
            }
          }else{
            $player->sendMessage("§c⩕ Please select a block by Right-Clicking");
          }
          }else{
            $player->sendMessage("§c⩕ Please select a block by Right-Clicking");
          }
        }
      }else{
        $player->sendMessage("§c⩕ You can't use builder wand here");
      }
    }elseif($item->getId() === 293 && $item->getMeta() === 0 && $item->getCustomName() === "§r §eFarmer Hoe §r\n§r §7 §r\n§r - §e40% Chance Of Double Drops §r\n§r - §eHoes 3x3 Area §r\n§r §7 §r\n§r §l§c §r")
    {
      if(!$event->isCancelled())
      {
        for($x = -1; $x <= 1; $x++)
        {
          for($z = -1; $z <= 1; $z++)
          {
            if($x === 0 && $z === 0)
            {
              continue;
            }
            $dirt = $player->getWorld()->getBlock($block->getPosition()->add($x, 0, $z));
            if(in_array($dirt->getId(), array(2, 3)))
            {
              $player->getWorld()->setBlock($block->getPosition()->add($x, 0, $z), BlockFactory::getInstance()->get(60, 0));
            }
          }
        }
      }
    }
    
    if($player->getWorld()->getFolderName() === $this->getSource()->getPlayerFile($playerName)->get("Island"))
    {
      $role = $this->api->getCoOpRole($player);
      if($this->api->getCoOpRole($player) === "Owner" || $this->api->getCoOpRole($player) === "Co-Owner" || $this->api->hasCoOpPerm($player, "Interact"))
      {
        $x = $block->getPosition()->getX();
        $y = $block->getPosition()->getY();
        $z = $block->getPosition()->getZ();
        $world = $block->getPosition()->getWorld()->getFolderName();
        if($this->api->IsUnbreakable($x, $y, $z, $world))
        {
          $event->cancel();
        }else{
          $event->uncancel();
        }
      }else{
        $event->cancel();
      }
    }else{
      if($player->hasPermission("sroudy.builder"))
      {
        $event->uncancel();
      }else{
        $event->cancel();
      }
    }
    
    if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK)
    {
      if($block->getId() === 58)
      {
        $event->cancel();
        $this->getSource()->getUI()->CraftingMenu($player);
      }elseif($block->getId() === 145)
      {
        $event->cancel();
        new AnvilMenu($player);
      }elseif($block->getId() === 54)
      {
        $Position = $block->getPosition();
        $World = $Position->getWorld();
        $Tile = $World->getTile($Position);
        if($Tile instanceof WorkerChestTile)
        {
          $event->cancel();
          $Tile->openInv($player);
        }
      }
    }
  }
  
  public function onPickupItem(EntityItemPickupEvent $event)
  {
    $player = $event->getEntity();
    $playerName = $player->getName();
    if($player->getLocation()->world->getFolderName() !== $this->getSource()->getPlayerFile($playerName)->get("Island") && $player->getLocation()->world->getFolderName() !== Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName())
    {
      $worldName = $player->getLocation()->world->getFolderName();
      if($this->api->haselitegames($worldName) && $worldName !== Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName())
      {
        if($worldName !== $this->getSource()->getPlayerFile($worldName)->get("Island"))
        {
          if($this->getSource()->getPlayerFile($worldName)->getNested("IslandSettings.CanDropItems"))
          {
            $event->uncancel();
          }else{
            $event->cancel();
          }
        }else{
          $event->uncancel();
        }
      }else{
        $event->uncancel();
      }
    }else{
      $event->uncancel();
    }
  }
  
  public function onDropItem(PlayerDropItemEvent $event)
  {
    $item = $event->getItem();
    $player = $event->getPlayer();
    $playerName = $player->getName();
    if($player->getLocation()->world->getFolderName() !== $this->getSource()->getPlayerFile($playerName)->get("Island") && $player->getLocation()->world->getFolderName() !== Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName())
    {
      $worldName = $player->getLocation()->world->getFolderName();
      if($this->api->haselitegames($worldName) && $worldName !== Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName())
      {
        if($worldName !== $this->getSource()->getPlayerFile($worldName)->get("Island"))
        {
          if($this->getSource()->getPlayerFile($worldName)->getNested("IslandSettings.CanDropItems"))
          {
            $event->uncancel();
          }else{
            $event->cancel();
          }
        }else{
          $event->uncancel();
        }
      }else{
        $event->uncancel();
      }
    }else{
      $event->uncancel();
    }
    $UndropableId = $this->config->getNested("SkyBlockItem.Id");
    $UndropableMeta = $this->config->getNested("SkyBlockItem.Meta");
    if($item->getId() === $UndropableId && $item->getMeta())
    {
      $event->cancel();
    }else{
      $event->uncancel();
    }
    
    if($player->isSneaking())
    {
      /**$Class = get_class($player);
      $reflectionMethod = new \ReflectionMethod($Class, 'removePermanentInventories');
      $reflectionMethod->setAccessible(true);
      $reflectionMethod->invoke($player);*/
    }
  }
  
  public function onTransaction(InventoryTransactionEvent $event)
  {
    $transaction = $event->getTransaction();
    foreach($transaction->getActions() as $action)
    {
      $item = $action->getSourceItem();
      $targetItem = $action->getTargetItem();
      $player = $transaction->getSource();
      if($player instanceof Player)
      {
        if($item->getId() === 1016 && $item->getMeta() === 0)
        {
          $event->cancel();
        }elseif($item->getId() === 340 && $item->getMeta() >= 1)
        {
          $array = $this->api->getItemType($targetItem);
          if(count($array) > 1)
          {
            $a_type = $array[0];
            $b_type = $array[1];
            if($a_type === "Weapon")
            {
              if(in_array($item->getMeta(), self::WEAPON[$b_type], true))
              {
                $inv = $player->getInventory();
                $a_match = $this->api->matchItem($item, $inv);
                $b_match = $this->api->matchItem($targetItem, $inv);
                if(!is_null($a_match) && !is_null($b_match))
                {
                  $can_enchant = false;
                  $has_enchantment = false;
                  $old_enchantments = $targetItem->getEnchantments();
                  $new_enchantmentId = StringToEnchantmentParser::getInstance()->parse(self::ENCHANTMENTIDS[$item->getMeta()]);
                  $new_enchantment = new EnchantmentInstance($new_enchantmentId, 1);
                  foreach($old_enchantments as $old_enchantment)
                  {
                    $a_id = EnchantmentIdMap::getInstance()->toId($old_enchantment->getType());
                    $b_id = EnchantmentIdMap::getInstance()->toId($new_enchantment->getType());
                    $a_level = $old_enchantment->getLevel();
                    $b_level = $new_enchantment->getLevel();
                    if($a_id === $b_id)
                    {
                      $has_enchantment = true;
                      $total_level = $a_level + $b_level;
                      $max_level = self::ENCHLEVEL[self::ENCHANTMENTIDS[$item->getMeta()]];
                      if($max_level >= $total_level)
                      {
                        $can_enchant = true;
                        $enchantment = new EnchantmentInstance($new_enchantmentId, $total_level);
                      }
                      break;
                    }
                  }
                  if($has_enchantment)
                  {
                    if($can_enchant)
                    {
                      $event->cancel();
                      $targetItem->addEnchantment($enchantment);
                      $inv->setItem($b_match[0], $targetItem);
                      $inv->removeItem($item->setCount(1));
                    }
                  }else{
                    $event->cancel();
                    $enchantment = new EnchantmentInstance($new_enchantmentId, 1);
                    $targetItem->addEnchantment($enchantment);
                    $inv->setItem($b_match[0], $targetItem);
                    $inv->removeItem($item->setCount(1));
                  }
                }
              }
            }elseif($a_type === "Armor")
            {
              if(in_array($item->getMeta(), self::ARMOR[$b_type], true))
              {
                $inv = $player->getInventory();
                $a_match = $this->api->matchItem($item, $inv);
                $b_match = $this->api->matchItem($targetItem, $inv);
                if(!is_null($a_match) && !is_null($b_match))
                {
                  $can_enchant = false;
                  $has_enchantment = false;
                  $old_enchantments = $targetItem->getEnchantments();
                  $new_enchantmentId = StringToEnchantmentParser::getInstance()->parse(self::ENCHANTMENTIDS[$item->getMeta()]);
                  $new_enchantment = new EnchantmentInstance($new_enchantmentId, 1);
                  foreach($old_enchantments as $old_enchantment)
                  {
                    $a_id = EnchantmentIdMap::getInstance()->toId($old_enchantment->getType());
                    $b_id = EnchantmentIdMap::getInstance()->toId($new_enchantment->getType());
                    $a_level = $old_enchantment->getLevel();
                    $b_level = $new_enchantment->getLevel();
                    if($a_id === $b_id)
                    {
                      $has_enchantment = true;
                      $total_level = $a_level + $b_level;
                      $max_level = self::ENCHLEVEL[self::ENCHANTMENTIDS[$item->getMeta()]];
                      if($max_level >= $total_level)
                      {
                        $can_enchant = true;
                        $enchantment = new EnchantmentInstance($new_enchantmentId, $total_level);
                      }
                      break;
                    }
                  }
                  if($has_enchantment)
                  {
                    if($can_enchant)
                    {
                      $event->cancel();
                      $targetItem->addEnchantment($enchantment);
                      $inv->setItem($b_match[0], $targetItem);
                      $inv->removeItem($item->setCount(1));
                    }
                  }else{
                    $event->cancel();
                    $enchantment = new EnchantmentInstance($new_enchantmentId, 1);
                    $targetItem->addEnchantment($enchantment);
                    $inv->setItem($b_match[0], $targetItem);
                    $inv->removeItem($item->setCount(1));
                  }
                }
              }
            }
          }else{
            if(count($array) === 1)
            {
              $a_type = $array[0];
              if($a_type === "Tool")
              {
                if(in_array($item->getMeta(), self::TOOL, true))
                {
                  $inv = $player->getInventory();
                  $a_match = $this->api->matchItem($item, $inv);
                  $b_match = $this->api->matchItem($targetItem, $inv);
                  if(!is_null($a_match) && !is_null($b_match))
                  {
                    $can_enchant = false;
                    $has_enchantment = false;
                    $old_enchantments = $targetItem->getEnchantments();
                    $new_enchantmentId = StringToEnchantmentParser::getInstance()->parse(self::ENCHANTMENTIDS[$item->getMeta()]);
                    $new_enchantment = new EnchantmentInstance($new_enchantmentId, 1);
                    foreach($old_enchantments as $old_enchantment)
                    {
                      $a_id = EnchantmentIdMap::getInstance()->toId($old_enchantment->getType());
                      $b_id = EnchantmentIdMap::getInstance()->toId($new_enchantment->getType());
                      $a_level = $old_enchantment->getLevel();
                      $b_level = $new_enchantment->getLevel();
                      if($a_id === $b_id)
                      {
                        $has_enchantment = true;
                        $total_level = $a_level + $b_level;
                        $max_level = self::ENCHLEVEL[self::ENCHANTMENTIDS[$item->getMeta()]];
                        if($max_level >= $total_level)
                        {
                          $can_enchant = true;
                          $enchantment = new EnchantmentInstance($new_enchantmentId, $total_level);
                        }
                        break;
                      }
                    }
                    if($has_enchantment)
                    {
                      if($can_enchant)
                      {
                        $event->cancel();
                        $targetItem->addEnchantment($enchantment);
                        $inv->setItem($b_match[0], $targetItem);
                        $inv->removeItem($item->setCount(1));
                      }
                    }else{
                      $event->cancel();
                      $enchantment = new EnchantmentInstance($new_enchantmentId, 1);
                      $targetItem->addEnchantment($enchantment);
                      $inv->setItem($b_match[0], $targetItem);
                      $inv->removeItem($item->setCount(1));
                    }
                  }
                }
              }
            }
          }
        }elseif($item->getId() === 298 || $item->getId() === 299 || $item->getId() === 300 || $item->getId() === 301)
        {
          $inv = $player->getArmorInventory();
          if($item->getCustomName() === "§r §bMiner Helmet §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $item->getCustomName() === "§r §bMiner Chestplate §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $item->getCustomName() === "§r §bMiner Leggings §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $item->getCustomName() === "§r §bMiner Boots §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r")
          {
            if($inv->getHelmet()->getCustomName() !== "§r §bMiner Helmet §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getChestplate()->getCustomName() !== "§r §bMiner Chestplate §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getLeggings()->getCustomName() !== "§r §bMiner Leggings §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getBoots()->getCustomName() !== "§r §bMiner Boots §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r")
            {
              $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function () use ($player, $inv): void
                {
                  if($inv->getHelmet()->getCustomName() === "§r §bMiner Helmet §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getChestplate()->getCustomName() === "§r §bMiner Chestplate §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getLeggings()->getCustomName() === "§r §bMiner Leggings §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getBoots()->getCustomName() === "§r §bMiner Boots §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r")
                  {
                    $this->api->FullArmorProperty($player, "Miner", "Add");
                  }
                }
              ), 5);
            }
            if($inv->getHelmet()->getCustomName() === "§r §bMiner Helmet §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" && $inv->getChestplate()->getCustomName() === "§r §bMiner Chestplate §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" && $inv->getLeggings()->getCustomName() === "§r §bMiner Leggings §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" && $inv->getBoots()->getCustomName() === "§r §bMiner Boots §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r")
            {
              $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function () use ($player, $inv): void
                {
                  if($inv->getHelmet()->getId() !== "§r §bMiner Helmet §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getChestplate()->getId() !== "§r §bMiner Chestplate §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getLeggings()->getId() !== "§r §bMiner Leggings §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r" || $inv->getBoots()->getId() !== "§r §bMiner Boots §r\n§r §7 §r\n§r §eFull Set Bonus §r\n§r §7- Speed Effect §r\n§r §7- Night Vision Effect §r\n§r §7- Haste Effect §r\n§r §7 §r\n§r §l§c §r")
                  {
                    $this->api->FullArmorProperty($player, "Miner", "Remove");
                  }
                }
              ), 5);
            }
          }elseif($item->getCustomName() === "§r §eFarmer Helmet §r" || $item->getCustomName() === "§r §eFarmer Chestplate §r" || $item->getCustomName() === "§r §eFarmer Leggings §r" || $item->getCustomName() === "§r §eFarmer Boots §r")
          {
            if($inv->getHelmet()->getCustomName() !== "§r §eFarmer Helmet §r" || $inv->getChestplate()->getCustomName() !== "§r §eFarmer Chestplate §r" || $inv->getLeggings()->getCustomName() !== "§r §eFarmer Leggings §r" || $inv->getBoots()->getCustomName() !== "§r §eFarmer Boots §r")
            {
              $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function () use ($player, $inv): void
                {
                  if($inv->getHelmet()->getCustomName() === "§r §eFarmer Helmet §r" && $inv->getChestplate()->getCustomName() === "§r §eFarmer Chestplate §r" && $inv->getLeggings()->getCustomName() === "§r §eFarmer Leggings §r" && $inv->getBoots()->getCustomName() === "§r §eFarmer Boots §r")
                  {
                    $this->api->FullArmorProperty($player, "Farmer", "Add");
                  }
                }
              ), 10);
            }
            if($inv->getHelmet()->getCustomName() === "§r §eFarmer Helmet §r" && $inv->getChestplate()->getCustomName() === "§r §eFarmer Chestplate §r" && $inv->getLeggings()->getCustomName() === "§r §eFarmer Leggings §r" && $inv->getBoots()->getCustomName() === "§r §eFarmer Boots §r")
            {
              $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function () use ($player, $inv): void
                {
                  if($inv->getHelmet()->getCustomName() !== "§r §eFarmer Helmet §r" || $inv->getChestplate()->getCustomName() !== "§r §eFarmer Chestplate §r" || $inv->getLeggings()->getCustomName() !== "§r §eFarmer Leggings §r" || $inv->getBoots()->getId() !== "§r §eFarmer Boots §r")
                  {
                    $this->api->FullArmorProperty($player, "Farmer", "Remove");
                  }
                }
              ), 10);
            }
          }elseif($item->getCustomName() === "§r §6Lumberjack Helmet §r" || $item->getCustomName() === "§r §6Lumberjack Chestplate §r" || $item->getCustomName() === "§r §6Lumberjack Leggings §r" || $item->getCustomName() === "§r §6Lumerbjack Boots §r")
          {
            if($inv->getHelmet()->getCustomName() !== "§r §6Lumberjack Helmet §r" || $inv->getChestplate()->getCustomName() !== "§r §6Lumberjack Chestplate §r" || $inv->getLeggings()->getCustomName() !== "§r §6Lumberjack Leggings §r" || $inv->getBoots()->getCustomName() !== "§r §6Lumberjack Boots §r")
            {
              $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function () use ($player, $inv): void
                {
                  if($inv->getHelmet()->getCustomName() === "§r §6Lumberjack Helmet §r" && $inv->getChestplate()->getCustomName() === "§r §6Lumberjack Chestplate §r" && $inv->getLeggings()->getCustomName() === "§r §6Lumberjack Leggings §r" && $inv->getBoots()->getCustomName() === "§r §6Lumberjack Boots §r")
                  {
                    $this->api->FullArmorProperty($player, "Lumberjack", "Add");
                  }
                }
              ), 5);
            }
            if($inv->getHelmet()->getCustomName() === "§r §6Lumberjack Helmet §r" && $inv->getChestplate()->getCustomName() === "§r §6Lumberjack Chestplate §r" && $inv->getLeggings()->getCustomName() === "§r §6Lumberjack Leggings §r" && $inv->getBoots()->getCustomName() === "§r §6Lumberjack Boots §r")
            {
              $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function () use ($player, $inv): void
                {
                  if($inv->getHelmet()->getCustomName() !== "§r §6Lumberjack Helmet §r" || $inv->getChestplate()->getCustomName() !== "§r §6Lumberjack Chestplate §r" || $inv->getLeggings()->getCustomName() !== "§r §6Lumberjack Leggings §r" || $inv->getBoots()->getCustomName() !== "§r §6Lumberjack Boots §r")
                  {
                    $this->api->FullArmorProperty($player, "Lumberjack", "Remove");
                  }
                }
              ), 5);
            }
          }
        }
      }
    }
  }
  
  
  public function onSneak(PlayerToggleSneakEvent $event)
  {  
    $player = $event->getPlayer();
    /**$Source = $this->getSource();
    $UI = $Source->getUI();
    $UI->MainUI($player);*/
    /**$entity = new Ship($player->getLocation());
    $entity->spawnToAll();*/
  }
  
  public function onJump(PlayerJumpEvent $event)
  {  
    $player = $event->getPlayer();
  }
  
  public function onMove(PlayerMoveEvent $event)
  {
    $player = $event->getPlayer();
        $block = $player->getWorld()->getBlock($player->getPosition());
        if ($block->getId() == BlockLegacyIds::END_PORTAL) {
            Server::getInstance()->dispatchCommand($player, "is tp");
            if($this->api->getObjective($player) === "Go-In-Portal")
        {
          $nextObjective = $this->api->getNextObjective($player);
          $this->api->setObjective($player, $nextObjective);
        }
      }
        if ($block->getId() == BlockLegacyIds::PORTAL) {
            Server::getInstance()->dispatchCommand($player, "hub");
        }
    }
  
  public function onEntityDamage(EntityDamageEvent $event)
  {
    $entity = $event->getEntity();
        if (!$entity instanceof Player) {
            return;
        }

        //Void Money Loss Event

        if($event->getCause() === EntityDamageEvent::CAUSE_VOID)
        {

            $defaultWorld = $entity->getWorld()->getSpawnLocation();
            $entity->teleport($defaultWorld);
            $event->cancel();
            $senderMoney = EconomyAPI::getInstance()->myMoney($entity);
            if(WenCore::getInstance()->getConfig()->get("float-false") === true)
            {
                if (!is_float($senderMoney)) {
                    return;
                }    
            }
            
            if(WenCore::getInstance()->getConfig()->get("VOID-MONEY") === true)
            {
                switch (WenCore::getInstance()->getConfig()->get("Type")) {
                    case "all":
                        $entity->sendMessage("§c§lINFO > §r§bYou Fell In Void And Lost §e$" . $senderMoney);
                        EconomyAPI::getInstance()->reduceMoney($entity, $senderMoney);
                        break;
                    case "half":
                        $entity->sendMessage("§c§lINFO > §r§bYou Fell In Void And Lost §e$" . $senderMoney / 2);
                        EconomyAPI::getInstance()->reduceMoney($entity, $senderMoney / 2);
                        break;
                    case "amount":
                        $entity->sendMessage("§c§lINFO > §r§bYou Fell In Void And Lost §e$" . (float)WenCore::getInstance()->getConfig()->get("Money-Loss"));
                        EconomyAPI::getInstance()->reduceMoney($entity, (float)WenCore::getInstance()->getConfig()->get("Money-Loss"));
                        break;
                    case "percent":
                        $entity->sendMessage("§c§lINFO > §r§bYou Fell In Void And Lost §e$" . ((float)WenCore::getInstance()->getConfig()->get("Money-Loss") / 100) * $senderMoney);
                        EconomyAPI::getInstance()->reduceMoney($entity, ((float)WenCore::getInstance()->getConfig()->get("Money-Loss") / 100) * $senderMoney);
                        break;
                }
            }
        }
    }
    /** @priority LOWEST */
    public function onEntityDamageG(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof Player) {
            return;
        }

        $aabb = new AxisAlignedBB(107.00, (float) World::Y_MIN, -74.00, 151.00, (float) World::Y_MAX, -30.00);
        if ($entity->getPosition()->getWorld()->getFolderName() === "") {
            if ($aabb->isVectorInXZ($entity->getPosition())) {
                $event->uncancel();
                return;
            }
            $event->cancel();
        }

        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            $victim = $event->getEntity();
            $level = $victim->getWorld()->getFolderName();
            $worlds = ["normal", "WEN"];
            if (in_array($level, $worlds))
                return;
            if (!$damager instanceof Player or !$victim instanceof Player)
                return;
            if ($damager->getInventory()->getItemInHand()->getId() !== 0)
                return;
            
            $event->cancel();
            $form = new SimpleForm(function (Player $player, ?int $result) use ($victim) {
                if ($result === null) return;
                switch ($result) {
                    case 0:
                        #$sender = $event->getEntity();
                        $name = $victim->getName();
                        Server::getInstance()->dispatchCommand($player, "trade request \"$name\"");
                        break;
                    case 1:
                        #$sender = $event->getEntity();
                        $name = $victim->getName();
                        Server::getInstance()->dispatchCommand($player, "trade accept \"$name\"");
                        break;
                    case 2:
                        $name = $victim->getName();
                        Server::getInstance()->dispatchCommand($player, "is accept \"$name\"");
                        break;
                    case 3:
                        #$sender = $event->getEntity();
                        $name = $victim->getName();
                        Server::getInstance()->dispatchCommand($player, "is invite \"$name\"");
                        break;
                   // case 2:
                  //      #$sender = $event->getEntity();
                      //  $name = $victim->getName();
                      ///  Server::getInstance()->dispatchCommand($player, "is visit \"$uuid\"");
                      //  break;
                }
            });
            
            $form->setTitle("§l§ePROFILE");
            $form->addButton("§l§bREQUEST TRADE\n§l§9»» §r§oTap to request", 1, "https://cdn-icons-png.flaticon.com/512/4258/4258259.png");
            $form->addButton("§l§bACCEPT TRADE\n§l§9»» §r§oTap to request", 1, "https://cdn-icons-png.flaticon.com/512/1582/1582114.png");
            $form->addButton("§l§bAccept ISLAND\n§l§9»» §r§oTap to visit", 1, "https://cdn-icons-png.flaticon.com/512/9470/9470087.png");
            $form->addButton("§l§bInvite ISLAND\n§l§9»» §r§oTap to request", 1, "https://cdn-icons-png.flaticon.com/512/1503/1503147.png");
           // $form->addButton("§l§bVISIT ISLAND\n§l§9»» §r§oTap to request", 1, "https://i.imgur.com/HNAHnLE.png");
            $damager->sendForm($form);
        }
        
      $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
        function() use($player): void 
        {
          if($player->isOnline())
          {
            $helmet = $player->getArmorInventory()->getHelmet();
            $chestplate = $player->getArmorInventory()->getChestplate();
            $leggings = $player->getArmorInventory()->getLeggings();
            $boots = $player->getArmorInventory()->getBoots();
            if($helmet->getId() !== 0)
            {
              if($helmet instanceof Armor)
              {
                $helmet->setDamage(0);
                $player->getArmorInventory()->setHelmet($helmet);
              }
            }
            if($chestplate->getId() !== 0)
            {
              if($chestplate instanceof Armor)
              {
                $chestplate->setDamage(0);
                $player->getArmorInventory()->setChestplate($chestplate);
              }
            }
            if($leggings->getId() !== 0)
            {
              if($leggings instanceof Armor)
              {
                $leggings->setDamage(0);
                $player->getArmorInventory()->setLeggings($leggings);
              }
            }
            if($boots->getId() !== 0)
            {
              if($boots instanceof Armor)
              {
                $boots->setDamage(0);
                $player->getArmorInventory()->setBoots($boots);
              }
            }
          }
        }), 5);           
    
    if(!$event->isCancelled())
    {
      if($event instanceof EntityDamageByEntityEvent)
      {
        $damager = $event->getDamager();
        if($damager instanceof Player)
        {
          $item = $damager->getInventory()->getItemInHand();
          if($item->getId() === 388 && $item->getMeta() === 0 && $item->getCustomName() === "§r §aEmerald Blade §r\n§r §7 §r\n§r §7Emerald Blade Gets Stronger As More Money §r\n§r §7You Carry In Your Purse §r\n§r §7 §r\n§r §l§6 §r")
          {
           
            if($damage < 1)
            {
              $damage = 1;
            }
            $event->uncancel();
            $event->setBaseDamage((float) $damage);
          }else{
            $event->uncancel();
          }
        }
      }
  ///   $this->api->damageParticle($player->getPosition(), $event->getFinalDamage());
    }
    }
  
 // public function onTeleport(EntityTeleportEvent $event)
  //{
   // $Entity = $event->getEntity();
   // if($Entity instanceof Player)
   // {
     // $elitegames = $this->getSource();
     /// $PlayerName = $Entity->getName();
    //  $Ship = $elitegames->getShip();
     // if(!is_null($Ship))
    ///  {
       // $Ship->unRide($Entity);
      //  $Ship->throwOff($Entity);
    // /  $Ship->unsetSeat($Entity);
     // }
   // }
 // }
  
  public function onBucketFill(PlayerBucketFillEvent $event)
  {
    $bucket = $event->getItem();
    if($bucket->getMeta() === 10)
    {
      $name = "Lava Bucket";
    }elseif($bucket->getMeta() === 8)
    {
      $name = "Water Bucket";
    }
    $bucket->setCustomName("§r $name §r\n§r §l §r");
    $event->setItem($bucket);
  }
  
  public function onBucketEmpty(PlayerBucketEmptyEvent $event)
  {
    $bucket = $event->getItem();
    $bucket->setCustomName("§r Bucket §r\n§r §l §r");
    $event->setItem($bucket);
  }
  
  public function onArmodStandArmorChange(PlayerChangeArmorStandArmorEvent $event)
  {
    $player = $event->getCauser();
    $PlayerInfo = API::getPlayerInfo($player);
    $PlayerIsland = $PlayerInfo->getIsland();
    $PlayerWorld = $player->getWorld()->getFolderName();
    if($PlayerIsland !== $PlayerWorld)
    {
      $event->cancel();
    }
  }
  
  public function onArmodStandItemChange(PlayerChangeArmorStandHeldItemEvent $event)
  {
    $player = $event->getCauser();
    $PlayerInfo = API::getPlayerInfo($player);
    $PlayerIsland = $PlayerInfo->getIsland();
    $PlayerWorld = $player->getWorld()->getFolderName();
    if($PlayerIsland !== $PlayerWorld)
    {
      $event->cancel();
    }
  }
  
  public function onChunkUnload(ChunkUnloadEvent $event)
  {
    $World = $event->getWorld();
    $ChunkX = $event->getChunkX();
    $ChunkZ = $event->getChunkZ();
    $Entities = $World->getChunkEntities($ChunkX, $ChunkZ);
    foreach($Entities as $Entity)
    {
      if($Entity instanceof Living)
      {
        if($Entity->getName() === "MonsterAI")
        {
          $Entity->flagForDespawn();
        }elseif($Entity->getName() === "Worker")
        {
          $event->cancel();
        }elseif($Entity instanceof Ship)
        {
          $event->cancel();
        }
      }
    }
  }
  
  public function onSpawn(EntitySpawnEvent $event)
  {
    $Entity = $event->getEntity();
    if($Entity instanceof Ship)
    {
      $elitegames = $this->getSource();
      $elitegames->setShip($Entity);
    }
  }
  
  public function onSubmitTransaction(TransactionSubmitEvent $event)
  {
    $this->getSource()->getScheduler()->scheduleDelayedTask(new ClosureTask(
      function(): void
      {
        $API = API::getBalanceAPI("");
        $API->updateBalance();
      }
    ), 20);
  }
  
  public function onDataPacketRecieve(DataPacketReceiveEvent $event)
  {
    $Pk = $event->getPacket();
    if($Pk instanceof LoginPacket)
    {
      if($Pk->protocol >= 390)
      {
        $Pk->protocol = ProtocolInfo::CURRENT_PROTOCOL;
      }
    }elseif($Pk instanceof InteractPacket)
    {
      $Action = $Pk->action;
      if($Action === 3)
      {
        $Origin = $event->getOrigin();
        $Player = $Origin->getPlayer();
        $elitegames = $this->getSource();
        $Ship = $elitegames->getShip();
        if(!is_null($Ship))
        {
          $Ship->unRide($Player);
          $Ship->unsetSeat($Player);
        }
      }
    }
  }
  
  /**
   * @return elitegames
   */
  public function getSource(): elitegames
  {
    $elitegames = API::getSource();
    return $elitegames;
  }
  
}