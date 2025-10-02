import { useQuery } from '@tanstack/react-query';
import { CampaignFilters, type Campaign } from '@/services/api';

export const QUERY_KEYS = {
  campaigns: (filters?: CampaignFilters) => ['campaigns', filters],
  campaign: (id: number) => ['campaign', id],
  categories: () => ['categories'],
  campaignDonations: (id: number) => ['campaignDonations', id],
} as const;

export function useCampaigns(filters: CampaignFilters = {}) {
  return useQuery({
    queryKey: QUERY_KEYS.campaigns(filters),
    queryFn: () => apiService.getCampaigns(filters),
    staleTime: 2 * 60 * 1000, // 2 minutes for campaigns list
    gcTime: 5 * 60 * 1000, // 5 minutes
  });
}

export function useCampaign(id: number) {
  return useQuery({
    queryKey: QUERY_KEYS.campaign(id),
    queryFn: () => apiService.getCampaign(id),
    staleTime: 5 * 60 * 1000, // 5 minutes for individual campaign
    gcTime: 10 * 60 * 1000, // 10 minutes
    enabled: !!id,
  });
}

export function useCategories() {
  return useQuery({
    queryKey: QUERY_KEYS.categories(),
    queryFn: () => apiService.getCategories(),
    staleTime: 15 * 60 * 1000, // 15 minutes for categories (they change less frequently)
    gcTime: 30 * 60 * 1000, // 30 minutes
  });
}

export function useCampaignMutations() {
  const queryClient = useQueryClient();

  const invalidateCampaigns = () => {
    queryClient.invalidateQueries({ queryKey: ['campaigns'] });
  };

  const invalidateCampaign = (id: number) => {
    queryClient.invalidateQueries({ queryKey: ['campaign', id] });
  };

  const invalidateCategories = () => {
    queryClient.invalidateQueries({ queryKey: ['categories'] });
  };

  // Optimistic update for campaign data
  const updateCampaignOptimistic = (id: number, updatedData: Partial<Campaign>) => {
    queryClient.setQueryData(['campaign', id], (oldData: Campaign | undefined) => {
      if (!oldData) return oldData;
      return { ...oldData, ...updatedData };
    });
  };

  // Prefetch campaign for better UX
  const prefetchCampaign = (id: number) => {
    queryClient.prefetchQuery({
      queryKey: QUERY_KEYS.campaign(id),
      queryFn: () => apiService.getCampaign(id),
      staleTime: 5 * 60 * 1000,
    });
  };

  return {
    invalidateCampaigns,
    invalidateCampaign,
    invalidateCategories,
    updateCampaignOptimistic,
    prefetchCampaign,
  };
}
