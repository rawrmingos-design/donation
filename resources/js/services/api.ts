import { router } from '@inertiajs/react';

export interface CampaignFilters {
  search?: string;
  category?: string;
  status?: string;
  page?: number;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
}

export interface Campaign {
  id: number;
  title: string;
  slug: string;
  short_desc: string;
  description: string;
  target_amount: number;
  collected_amount: number;
  status: 'active' | 'completed' | 'draft';
  deadline: string;
  featured_image?: string;
  created_at: string;
  updated_at: string;
  category: Category;
  user: User;
  donations?: Donation[];
  comments?: Comment[];
}

export interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
  created_at: string;
  updated_at: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
}

export interface Donation {
  id: number;
  amount: number;
  message?: string;
  is_anonymous: boolean;
  created_at: string;
  donor?: User;
}


export interface Comment {
  id: number;
  content: string;
  is_public: boolean;
  created_at: string;
  user: User;
}

class ApiService {
  private baseUrl = '/campaigns';

  async getCampaigns(filters: CampaignFilters = {}): Promise<{
    campaigns: PaginatedResponse<Campaign>;
    categories: Category[];
    filters: CampaignFilters;
  }> {
    return new Promise((resolve, reject) => {
      const params = new URLSearchParams();
      
      if (filters.search) params.set('search', filters.search);
      if (filters.category && filters.category !== 'all') params.set('category', filters.category);
      if (filters.status && filters.status !== 'all') params.set('status', filters.status);
      if (filters.page) params.set('page', filters.page.toString());

      const url = params.toString() ? `${this.baseUrl}?${params.toString()}` : this.baseUrl;

      router.get(url, {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['campaigns', 'categories', 'filters'],
        onSuccess: (page: any) => {
          resolve({
            campaigns: page.props.campaigns,
            categories: page.props.categories,
            filters: page.props.filters,
          });
        },
        onError: (errors) => {
          reject(new Error(Object.values(errors).join(', ')));
        },
      });
    });
  }

  async getCampaign(id: number): Promise<Campaign> {
    return new Promise((resolve, reject) => {
      router.get(`${this.baseUrl}/${id}`, {}, {
        preserveState: true,
        only: ['campaign'],
        onSuccess: (page: any) => {
          resolve(page.props.campaign);
        },
        onError: (errors) => {
          reject(new Error(Object.values(errors).join(', ')));
        },
      });
    });
  }

  async getCategories(): Promise<Category[]> {
    return new Promise((resolve, reject) => {
      router.get('/api/categories', {}, {
        preserveState: true,
        onSuccess: (response: any) => {
          resolve(response.data);
        },
        onError: (errors) => {
          reject(new Error(Object.values(errors).join(', ')));
        },
      });
    });
  }

  // Cache invalidation helpers
  invalidateCampaigns() {
    // This will be handled by TanStack Query's invalidateQueries
    return ['campaigns'];
  }

  invalidateCampaign(id: number) {
    return ['campaign', id];
  }

  invalidateCategories() {
    return ['categories'];
  }
}

export const apiService = new ApiService();
