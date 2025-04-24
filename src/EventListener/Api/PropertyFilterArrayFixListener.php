<?php

namespace App\EventListener\Api;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Listener to fix #[ApiFilter(PropertyFilter::class)] in the API Platform.
 * It converts a string of properties into an array format for proper filtering.
 * This fixes the issue in Swagger where the properties are not correctly interpreted as an array.
 * For example, it converts "id,name" into ["id", "name"] for the request.
 */
#[AsEventListener(event: 'kernel.request', priority: 100)]
class PropertyFilterArrayFixListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $properties = $request->query->all('properties')[0] ?? null;
        
        if (is_string($properties)) {
            $propertiesArray = array_map('trim', explode(',', $properties));
            $query = $request->query->all();
            $query['properties'] = $propertiesArray;
            $request->query->replace($query);
        }
    }
}
