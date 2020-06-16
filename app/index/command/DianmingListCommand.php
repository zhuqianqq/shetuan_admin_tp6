<?php
namespace app\index\command;
use app\index\controller\CronJob;

class DianmingListCommand extends BaseCommand
{
    /**
     * @var string 指令名称
     */
    protected $scriptName = "dianming_list";

    /**
     * 执行入口(处理业务逻辑)
     */
    protected function _execute()
    {
       CronJob::DianMingList();
       //$this->output->writeln('dianming_list');
    }
}


