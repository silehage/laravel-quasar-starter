<?php

namespace App\Enums;

enum AmqpEventEnum: string
{
    case ProductEvent = 'product.event';
    case ProductLineEvent = 'productLine.event';
    case WarehouseEvent = 'warehouse.event';
    case CategoryEvent = 'category.event';
    case OrderEvent = 'order.event';
    case CustomerEvent = 'customer.event';
    case CourierEvent = 'courier.event';
    case TraderEvent = 'trader.event';
    case StoreEvent = 'store.event';
    case FulfillmentEvent = 'fulfillment.event';
    case PlatformEvent = 'platform.event';
    case ManufacturerEvent = 'manufacturer.event';
    case FactoryEvent = 'factory.event';
}
