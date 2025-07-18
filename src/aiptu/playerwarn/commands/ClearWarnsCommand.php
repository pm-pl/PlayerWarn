<?php

/*
 * Copyright (c) 2023-2025 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/PlayerWarn
 */

declare(strict_types=1);

namespace aiptu\playerwarn\commands;

use aiptu\playerwarn\PlayerWarn;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\utils\TextFormat;
use function count;

class ClearWarnsCommand extends Command implements PluginOwned {
	use PluginOwnedTrait {
		__construct as setOwningPlugin;
	}

	public function __construct(
		private PlayerWarn $plugin
	) {
		$this->setOwningPlugin($plugin);
		parent::__construct(
			'clearwarns',
			'Clear warnings for a player',
			'/clearwarns <player>',
		);
		$this->setPermission('playerwarn.command.clearwarns');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
		if (!$this->testPermission($sender)) {
			return false;
		}

		if (count($args) < 1) {
			throw new InvalidCommandSyntaxException();
		}

		$playerName = $args[0];
		$warns = $this->plugin->getWarns();
		$hasWarnings = $warns->hasWarnings($playerName);

		if (!$hasWarnings) {
			$sender->sendMessage(TextFormat::YELLOW . "No warnings found for {$playerName}.");
			return false;
		}

		$warningCount = $warns->getWarningCount($playerName);
		$warns->removeWarns($playerName);

		$sender->sendMessage(TextFormat::GREEN . "Cleared {$warningCount} warning(s) for {$playerName}.");

		return true;
	}
}
