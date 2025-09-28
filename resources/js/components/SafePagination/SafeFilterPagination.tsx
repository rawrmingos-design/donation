import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';

interface SafeFilterPaginationProps {
    initialFilters: {
        search?: string;
        category?: string;
        status?: string;
        [key: string]: any;
    };
    route: string;
    onFiltersChange?: (filters: any) => void;
    debounceMs?: number;
    children: (props: {
        filters: any;
        setSearch: (value: string) => void;
        setCategory: (value: string) => void;
        setStatus: (value: string) => void;
        setFilter: (key: string, value: any) => void;
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

    // Handle filter changes with debounce for search
    useEffect(() => {
        if (!isInitialized) return; // CRITICAL: Prevent initial trigger
        
        const timeoutId = setTimeout(() => {
            handleFilterChange();
        }, debounceMs);
        
        return () => clearTimeout(timeoutId);
    }, [filters.search, isInitialized]);

    // Handle non-search filters immediately
    useEffect(() => {
        if (!isInitialized) return; // CRITICAL: Prevent initial trigger
        
        handleFilterChange();
    }, [filters.category, filters.status, isInitialized]);

    const handleFilterChange = () => {
        const params: any = {};
        
        // Add all non-empty filters
        Object.keys(filters).forEach(key => {
            const value = filters[key];
            if (value && value !== 'all' && value !== '') {
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
    };

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

    const setFilter = (key: string, value: any) => {
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
