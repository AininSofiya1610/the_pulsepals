<?php

namespace App\Services;

use App\Models\TicketOption;
use App\Models\Unit;
use Illuminate\Support\Facades\Cache;

class DropdownService
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Get cached priorities
     */
    public function getPriorities()
    {
        return Cache::remember('dropdown.priorities', self::CACHE_TTL, fn() =>
            TicketOption::where('type', 'priority')
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
        );
    }

    /**
     * Get cached categories (help topics)
     */
    public function getCategories()
    {
        return Cache::remember('dropdown.categories', self::CACHE_TTL, fn() =>
            TicketOption::where('type', 'category')
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
        );
    }

    /**
     * Get cached ticket types
     */
    public function getTicketTypes()
    {
        return Cache::remember('dropdown.ticket_types', self::CACHE_TTL, fn() =>
            TicketOption::where('type', 'ticket_type')
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
        );
    }

    /**
     * Get cached units
     */
    public function getUnits()
    {
        return Cache::remember('dropdown.units', self::CACHE_TTL, fn() =>
            Unit::where('is_active', true)->get()
        );
    }

    /**
     * Get all dropdown data at once (optimized for Create Ticket page)
     */
    public function getAllDropdowns(): array
    {
        return [
            'priorities'  => $this->getPriorities(),
            'categories'  => $this->getCategories(),
            'ticketTypes' => $this->getTicketTypes(),
            'units'       => $this->getUnits(),
        ];
    }

    /**
     * Clear cache for a specific type or all dropdowns
     */
    public function clearCache(?string $type = null): void
    {
        if ($type) {
            // Map type to cache key
            $keyMap = [
                'priority'    => 'dropdown.priorities',
                'category'    => 'dropdown.categories',
                'ticket_type' => 'dropdown.ticket_types',
                'units'       => 'dropdown.units',
            ];
            
            if (isset($keyMap[$type])) {
                Cache::forget($keyMap[$type]);
            }
        } else {
            // Clear all dropdown caches
            Cache::forget('dropdown.priorities');
            Cache::forget('dropdown.categories');
            Cache::forget('dropdown.ticket_types');
            Cache::forget('dropdown.units');
        }
    }
}
