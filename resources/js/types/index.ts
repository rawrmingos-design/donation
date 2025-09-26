export interface User {
    id: number;
    name: string;
    email: string;
    phone?: string;
    role: string;
    avatar?: string;
    is_verified: boolean;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    name: string;
    slug: string;
    description?: string;
    created_at: string;
    updated_at: string;
}

export interface Campaign {
    id: number;
    user_id: number;
    category_id: number;
    title: string;
    slug: string;
    short_desc?: string;
    description?: string;
    featured_image?: string;
    target_amount: number;
    collected_amount: number;
    currency: string;
    goal_type: string;
    deadline?: string;
    status: string;
    allow_anonymous: boolean;
    created_at: string;
    updated_at: string;
    
    // Relationships
    user: User;
    category: Category;
    donations?: Donation[];
    campaign_updates?: CampaignUpdate[];
    comments?: Comment[];
    
    // Computed attributes
    progress_percentage: number;
    donors_count: number;
}

export interface Donor {
    id: number;
    user_id?: number;
    name: string;
    email: string;
    phone?: string;
    is_anonymous: boolean;
    created_at: string;
    
    // Relationships
    user?: User;
}

export interface Donation {
    id: number;
    campaign_id: number;
    donor_id: number;
    amount: number;
    currency: string;
    message?: string;
    created_at: string;
    
    // Relationships
    campaign: Campaign;
    donor: Donor;
    transaction?: Transaction;
}

export interface PaymentProvider {
    id: number;
    name: string;
    code: string;
    active: boolean;
    created_at: string;
    updated_at: string;
}

export interface PaymentChannel {
    id: number;
    provider_id: number;
    code: string;
    name: string;
    fee_fixed: number;
    fee_percentage: number;
    created_at: string;
    updated_at: string;
    
    // Relationships
    provider: PaymentProvider;
}

export interface Transaction {
    id: number;
    donation_id: number;
    channel_id: number;
    ref_id: string;
    pay_url?: string;
    va_number?: string;
    instruction?: string;
    total_paid: number;
    total_received?: number;
    status: string;
    created_at: string;
    updated_at: string;
    paid_at?: string;
    
    // Relationships
    donation: Donation;
    paymentChannel: PaymentChannel;
}

export interface CampaignUpdate {
    id: number;
    campaign_id: number;
    title: string;
    content: string;
    created_at: string;
    updated_at: string;
    
    // Relationships
    campaign: Campaign;
}

export interface Comment {
    id: number;
    campaign_id: number;
    user_id: number;
    content: string;
    is_public: boolean;
    created_at: string;
    updated_at: string;
    
    // Relationships
    campaign: Campaign;
    user: User;
}

export interface Withdrawal {
    id: number;
    campaign_id: number;
    amount: number;
    fee_amount: number;
    net_amount: number;
    method: string;
    account_info: {
        account_name: string;
        bank_name?: string;
        account_number?: string;
        wallet_type?: string;
        phone_number?: string;
    };
    status: 'pending' | 'approved' | 'processing' | 'completed' | 'rejected' | 'cancelled';
    notes?: string;
    reference_number?: string;
    approved_by?: number;
    requested_at: string;
    approved_at?: string;
    processed_at?: string;
    completed_at?: string;
    created_at: string;
    updated_at: string;
    
    // Relationships
    campaign: Campaign;
    approvedBy?: User;
}

export interface FundraiserApplication {
    id: number;
    user_id: number;
    full_name: string;
    phone: string;
    address: string;
    id_card_number: string;
    id_card_photo?: string;
    motivation: string;
    experience?: string;
    social_media_links?: string;
    status: 'pending' | 'approved' | 'rejected';
    admin_notes?: string;
    created_at: string;
    updated_at: string;
    
    // Relationships
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: any;
    icon: any;
}


export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};
