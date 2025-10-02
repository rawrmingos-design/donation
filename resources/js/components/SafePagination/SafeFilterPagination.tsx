import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';

interface FilterObject {
    search?: string;
    category?: string;
    status?: string;
    [key: string]: string | number | boolean | undefined;
}

interface SafeFilterPaginationProps {
    initialFilters: FilterObject;
    route: string;
    onFiltersChange?: (filters: Record<string, string | number>) => void;
    debounceMs?: number;
    children: (props: {
        filters: FilterObject;
        setSearch: (value: string) => void;
        setCategory: (value: string) => void;
        setStatus: (value: string) => void;
        setFilter: (key: string, value: string | number | boolean | undefined) => void;
        handleFilterChange: () => void;
    }) => React.ReactNode;
}

/**
 * Safe Filter Pagination Component
 * 
 * Prevents the common pagination bug where useEffect triggers on component mount
 * causing unwanted redirects when accessing pages directly via URL.
 * 
 * Usage:
 * <SafeFilterPagination
 *   initialFilters={filters}
 *   route="/campaigns"
 *   debounceMs={500}
 * >
 *   {({ filters, setSearch, handleFilterChange }) => (
 *     <div>
 *       <input 
 *         value={filters.search} 
 *         onChange={(e) => setSearch(e.target.value)} 
 *       />
 *       // Your pagination UI here
 *     </div>
 *   )}
 * </SafeFilterPagination>
 */
export default function SafeFilterPagination({
    initialFilters,
    route,
    onFiltersChange,
    debounceMs = 500,
    children
}: SafeFilterPaginationProps) {
    // State for all filters
    const [filters, setFilters] = useState(initialFilters);
    const [isInitialized, setIsInitialized] = useState(false);

    // Initialize component - prevents initial useEffect trigger
    useEffect(() => {
        setIsInitialized(true);
    }, []);

    const handleFilterChange = React.useCallback(() => {
        const params: Record<string, string | number> = {};
        
        // Add all non-empty filters
        Object.keys(filters).forEach(key => {
            const value = filters[key];
            if (value && value !== 'all' && value !== '' && typeof value !== 'boolean') {
                params[key] = value;
            }
        });
        
        // Always reset to page 1 when filters change
        params.page = 1;
        
        // Call optional callback
        if (onFiltersChange) {
            onFiltersChange(params);
        }
        
        // Navigate with new filters
        router.get(route, params, {
            preserveState: true,
            preserveScroll: true,
        });
    }, [filters, onFiltersChange, route]);

    // Handle filter changes with debounce for search
    useEffect(() => {
        if (!isInitialized) return; // CRITICAL: Prevent initial trigger
        
        const timeoutId = setTimeout(() => {
            handleFilterChange();
        }, debounceMs);
        
        return () => clearTimeout(timeoutId);
    }, [filters.search, isInitialized, debounceMs, handleFilterChange]);

    // Handle non-search filters immediately
    useEffect(() => {
        if (!isInitialized) return; // CRITICAL: Prevent initial trigger
        
        handleFilterChange();
    }, [filters.category, filters.status, isInitialized, handleFilterChange]);

    // Helper functions for common filter types
    const setSearch = (value: string) => {
        setFilters(prev => ({ ...prev, search: value }));
    };

    const setCategory = (value: string) => {
        setFilters(prev => ({ ...prev, category: value }));
    };

    const setStatus = (value: string) => {
        setFilters(prev => ({ ...prev, status: value }));
    };

    const setFilter = (key: string, value: string | number | boolean | undefined) => {
        setFilters(prev => ({ ...prev, [key]: value }));
    };

    return (
        <>
            {children({
                filters,
                setSearch,
                setCategory,
                setStatus,
                setFilter,
                handleFilterChange
            })}
        </>
    );
}
