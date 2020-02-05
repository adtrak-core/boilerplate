<?php


namespace PGMB\Upgrader;


use PGMB\BackgroundProcessing\BackgroundProcess;

interface DistributedUpgrade extends Upgrade {
	public function task($item);
	public function __construct(BackgroundProcess $upgrade_process);
}
