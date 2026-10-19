<?php

namespace App\Console\Commands;

use App\Enums\AmqpEventEnum;
use App\Jobs\FactoryEventJob;
use App\Jobs\FulfillmentEventJob;
use App\Jobs\ManufacturerEventJob;
use App\Jobs\ProductCategoryEventJob;
use App\Jobs\ProductEventJob;
use App\Jobs\ProductLineEventJob;
use Bschmitt\Amqp\Facades\Amqp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AmqpConsume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:amqp-consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
     public function handle()
    {
     $this->info('Starting AMQP Manufacturer consumer...');
        Amqp::consume(config('amqp.queue'), function ($message, $resolver) {

            try {
                $this->processMessage($message);
                $resolver->acknowledge($message);
            } catch (\Exception $e) {
                $resolver->reject($message, true);
            }
        }, [
            'queue_passive' => true,
            'exchange_passive' => true,
            'queue_durable' => true,
            'persistent' => true // Agar consumer tetap berjalan (listen)
        ]);
    }

    private function processMessage($message)
    {
        $result = json_decode($message->body, true);

        $event = $result['event'] ?? null;

        Log::info('AMQP event: ' . $event);

        switch ($event) {
            case AmqpEventEnum::ProductEvent->value:
                ProductEventJob::dispatch($result['payload']);
                break;
            case AmqpEventEnum::ProductLineEvent->value:
                ProductLineEventJob::dispatch($result['payload']);
                break;
            case AmqpEventEnum::CategoryEvent->value:
                ProductCategoryEventJob::dispatch($result['payload']);
                break;
            case AmqpEventEnum::ManufacturerEvent->value:
                ManufacturerEventJob::dispatch($result['payload']);
                break;
            case AmqpEventEnum::FactoryEvent->value:
                FactoryEventJob::dispatch($result['payload']);
                break;
            case AmqpEventEnum::FulfillmentEvent->value:
                FulfillmentEventJob::dispatch($result['payload']);
                break;

            default:
                Log::info('Unknown event: ' . $event);
                break;
        }

        $this->info("Processed message: " . substr($message->body, 0, 50) . "...");
    }
}
