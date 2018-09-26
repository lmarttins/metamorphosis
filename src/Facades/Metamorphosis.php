<?php
namespace Metamorphosis\Facades;

use Illuminate\Support\Facades\Facade;
use Metamorphosis\TopicHandler\Producer\Handler;

/**
 * @method static void produce(Handler $producerHandler)
 */
class Metamorphosis extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'metamorphosis';
    }
}
