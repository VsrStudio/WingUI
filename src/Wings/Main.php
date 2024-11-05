<?php

namespace Wings;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\utils\Config;

class Main extends PluginBase {

    public $taskwingdevil;
    public $taskwingthienthan;
    public $wingdevil = [];
    public $wingthienthan = [];
    public $wingfajar = [];
    public $wingterrain = [];
    public $wingdarkpurp = [];
    public $wingphoniex = [];
    public $wingforcefield = [];
    public $wingtenny = [];

    /** @var Config */
    public $config;
    public $checker;

    public function onEnable(): void {
        // Inisialisasi tugas
        $this->taskwingdevil = new DevilWing($this);
        $this->taskwingthienthan = new AngleWing($this);
        $this->taskwingfajar = new FajarWing($this);
        $this->taskwingterrain = new TerrainWing($this);
        $this->taskwingdarkpurp = new DarkPurpWing($this);
        $this->taskwingphoniex = new PhoniexWing($this);
        $this->taskwingforcefield = new ForceFieldWing($this);
        $this->taskwingtenny = new TennyWing($this);

        // Memuat file konfigurasi
        $this->saveResource("time.yml");
        $this->config = new Config($this->getDataFolder() . "time.yml", Config::YAML);
        $this->checker = $this->config->get("time-update");
        $this->getServer()->getLogger()->info("§aPlugin WingUI Made by AmlxP, Thx");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        if ($cmd->getName() === "wing") {
            if (!$sender instanceof Player) {
                $sender->sendMessage("§l§9WingAP§e> §r§cIngame only!");
                return true;
            }
            $this->showWingForm($sender);
            return true;
        }
        return false;
    }

    private function showWingForm(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if (!is_null($data)) {
                $this->handleWingSelection($player, $data);
            }
        });

        $form->setTitle("§7- Wings -");
        $form->setContent("§7Tap the §bbutton §7for use!");
        $form->addButton("§l§6Devil\n§r§7Perms: devil.wing");
        $form->addButton("§l§fAngel\n§r§7Perms: angel.wing");
        $form->addButton("§l§cYTFajar§fBlitz7\n§r§7Perms: fajar.wing");
        $form->addButton("§l§cRedst§4circuit\n§r§7Perms: terrain.wing");
        $form->addButton("§l§0Dark§dPurp\n§r§7Perms: darkpurp.wing");
        $form->addButton("§l§6Phon§eiex\n§r§7Perms: phoniex.wing");
        $form->addButton("§l§7ForceF§8ield\n§r§7Perms: forcefield.wing");
        $form->addButton("§l§fTenny\n§r§7Perms: tenny.wing");
        $form->addButton("§4§lDEACTIVE");
        $form->addButton("§c§lEXIT");

        $player->sendForm($form);
    }

    private function handleWingSelection(Player $player, int $data): void {
        $name = $player->getName();
        $permissions = [
            "devil.wing" => &$this->wingdevil,
            "thienthan.wing" => &$this->wingthienthan,
            "fajar.wing" => &$this->wingfajar,
            "terrain.wing" => &$this->wingterrain,
            "darkpurp.wing" => &$this->wingdarkpurp,
            "phoniex.wing" => &$this->wingphoniex,
            "forcefield.wing" => &$this->wingforcefield,
            "tenny.wing" => &$this->wingtenny
        ];

        // Array untuk menampung permission dan task
        $wings = array_keys($permissions);
        $wingKey = $wings[$data] ?? null;

        if ($wingKey !== null) {
            if (!$player->hasPermission($wingKey)) {
                $player->sendMessage("§l§9WingAP§e> §r§cYou Don't Have Permission");
                return;
            }

            // Menangani aktifasi/deaktivasi sayap
            foreach ($permissions as $perm => &$wingArray) {
                if ($wingArray === $permissions[$wingKey]) {
                    if (in_array($name, $wingArray)) {
                        unset($wingArray[array_search($name, $wingArray)]);
                        $player->sendMessage("§l§9WingAP§e> §r§cWing Deactive");
                    } else {
                        $wingArray[] = $name;
                        $player->sendMessage("§l§9WingAP§e> §r§aWing Active");
                    }
                } else {
                    if (in_array($name, $wingArray)) {
                        unset($wingArray[array_search($name, $wingArray)]);
                    }
                }
            }
        }
    }
}
