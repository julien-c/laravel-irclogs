<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Phergie\Irc\Connection;
use Phergie\Irc\Client\React\Client;

class IrcLogCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'irc:log';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Log IRC channel #laravel.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$connection = new Connection;
		$connection->setServerHostname('irc.freenode.net');
		$connection->setUsername('irclogs');
		$connection->setHostname('irc.freenode.net');
		$connection->setServername('irc.freenode.net');
		$connection->setRealname('IRC Logs');
		$connection->setNickname('irclogs');

		$client = new Client;
		$client->addConnection($connection);
		$client->addListener(function($message, $write, $connection, $logger) {
			// Ping Pong:
			if ($message['command'] === 'PING') {
				$write->ircPong('laravel-irclogs');
			}
			
			// Auto-join:
			if (isset($message['code']) && in_array($message['code'], array('RPL_ENDOFMOTD', 'ERR_NOMOTD'))) {
				$write->ircJoin('#laravel');
			}
		});
		$client->run();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}