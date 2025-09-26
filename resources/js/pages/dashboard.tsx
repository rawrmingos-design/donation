import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, type PageProps, type FundraiserApplication, type Campaign } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { Plus, Edit, Trash2, Eye, Clock, CheckCircle, AlertCircle, Users, Target, Calendar } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface Props extends PageProps {
    fundraiserApplications?: FundraiserApplication[];
    campaigns?: Campaign[];
    stats?: {
        totalCampaigns: number;
        totalDonations: number;
        totalAmount: number;
        activeCampaigns: number;
    };
}

export default function Dashboard({ auth, fundraiserApplications = [], campaigns = [], stats }: Props) {
    const [showCreateForm, setShowCreateForm] = useState(false);
    const [editingCampaign, setEditingCampaign] = useState<Campaign | null>(null);
    
    const { data, setData, post, put, processing, errors, reset } = useForm({
        title: '',
        short_desc: '',
        description: '',
        target_amount: '',
        category_id: '',
        deadline: '',
        goal_type: 'amount'
    });
    
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editingCampaign) {
            put(`/campaigns/${editingCampaign.id}`, {
                onSuccess: () => {
                    setEditingCampaign(null);
                    setShowCreateForm(false);
                    reset();
                }
            });
        } else {
            post('/campaigns', {
                onSuccess: () => {
                    setShowCreateForm(false);
                    reset();
                }
            });
        }
    };
    
    const handleEdit = (campaign: Campaign) => {
        setEditingCampaign(campaign);
        setData({
            title: campaign.title,
            short_desc: campaign.short_desc || '',
            description: campaign.description || '',
            target_amount: campaign.target_amount.toString(),
            category_id: campaign.category_id.toString(),
            deadline: campaign.deadline || '',
            goal_type: campaign.goal_type
        });
        setShowCreateForm(true);
    };
    
    const handleDelete = (campaignId: number) => {
        if (confirm('Apakah Anda yakin ingin menghapus kampanye ini?')) {
            router.delete(`/campaigns/${campaignId}`);
        }
    };
    
    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'pending':
                return <Badge variant="secondary" className="flex items-center gap-1"><Clock className="h-3 w-3" />Menunggu</Badge>;
            case 'approved':
                return <Badge variant="default" className="flex items-center gap-1 bg-green-500"><CheckCircle className="h-3 w-3" />Disetujui</Badge>;
            case 'rejected':
                return <Badge variant="destructive" className="flex items-center gap-1"><AlertCircle className="h-3 w-3" />Ditolak</Badge>;
            case 'active':
                return <Badge variant="default" className="flex items-center gap-1 bg-blue-500"><CheckCircle className="h-3 w-3" />Aktif</Badge>;
            case 'completed':
                return <Badge variant="default" className="flex items-center gap-1 bg-green-600"><CheckCircle className="h-3 w-3" />Selesai</Badge>;
            case 'draft':
                return <Badge variant="outline" className="flex items-center gap-1"><Edit className="h-3 w-3" />Draft</Badge>;
            default:
                return null;
        }
    };

    console.log(campaigns);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 sm:gap-6 p-4 sm:p-6">
                {/* Welcome Section */}
                <div className="mb-4 sm:mb-6">
                    <h1 className="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        Selamat Datang, {auth.user.name}!
                    </h1>
                    <p className="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        {auth.user.role === 'fundraiser' ? 'Kelola kampanye penggalangan dana Anda' : 'Temukan dan dukung kampanye penggalangan dana'}
                    </p>
                </div>

                {/* Stats Cards */}
                {stats && (
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                        <Card>
                            <CardContent className="p-3 sm:p-6">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Total Kampanye</p>
                                        <p className="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">{stats.totalCampaigns}</p>
                                    </div>
                                    <Target className="h-6 sm:h-8 w-6 sm:w-8 text-blue-500" />
                                </div>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent className="p-6">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Total Donasi</p>
                                        <p className="text-2xl font-bold text-gray-900 dark:text-white">{stats.totalDonations}</p>
                                    </div>
                                    <Users className="h-8 w-8 text-green-500" />
                                </div>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent className="p-6">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Total Terkumpul</p>
                                        <p className="text-2xl font-bold text-gray-900 dark:text-white">Rp {stats.totalAmount.toLocaleString('id-ID')}</p>
                                    </div>
                                    <Target className="h-8 w-8 text-purple-500" />
                                </div>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent className="p-6">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Kampanye Aktif</p>
                                        <p className="text-2xl font-bold text-gray-900 dark:text-white">{stats.activeCampaigns}</p>
                                    </div>
                                    <Calendar className="h-8 w-8 text-orange-500" />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                )}

                {/* Role-based Content */}
                {auth.user.role === 'donor' && (
                    <div className="space-y-6">
                        {/* Fundraiser Applications for Donors */}
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div>
                                        <CardTitle className="flex items-center gap-2">
                                            <Users className="h-5 w-5" />
                                            Pengajuan Penggalang Dana
                                        </CardTitle>
                                        <CardDescription>
                                            Daftar pengajuan untuk menjadi penggalang dana
                                        </CardDescription>
                                    </div>
                                    <Link href="/fundraiser/application">
                                        <Button>
                                            <Plus className="h-4 w-4 mr-2" />
                                            Ajukan Diri
                                        </Button>
                                    </Link>
                                </div>
                            </CardHeader>
                            <CardContent>
                                {fundraiserApplications.length > 0 ? (
                                    <div className="space-y-4">
                                        {fundraiserApplications.map((application) => (
                                            <div key={application.id} className="border rounded-lg p-4">
                                                <div className="flex items-center justify-between mb-2">
                                                    <h4 className="font-semibold">{application.full_name}</h4>
                                                    {getStatusBadge(application.status)}
                                                </div>
                                                <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                    {application.motivation.substring(0, 150)}...
                                                </p>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-xs text-gray-500">
                                                        Diajukan: {new Date(application.created_at).toLocaleDateString('id-ID')}
                                                    </span>
                                                    <Link href={`/fundraiser/application/${application.id}`}>
                                                        <Button variant="outline" size="sm">
                                                            <Eye className="h-3 w-3 mr-1" />
                                                            Lihat Detail
                                                        </Button>
                                                    </Link>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="text-center py-8">
                                        <Users className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                        <p className="text-gray-500">Belum ada pengajuan penggalang dana</p>
                                        <Link href="/fundraiser/application" className="mt-2 inline-block">
                                            <Button>
                                                Ajukan Diri Sebagai Penggalang Dana
                                            </Button>
                                        </Link>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                )}

                {auth.user.role === 'creator' && (
                    <div className="space-y-6">
                        {/* Campaign CRUD for Fundraisers */}
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div>
                                        <CardTitle className="flex items-center gap-2">
                                            <Target className="h-5 w-5" />
                                            Kampanye Saya
                                        </CardTitle>
                                        <CardDescription>
                                            Kelola kampanye penggalangan dana Anda
                                        </CardDescription>
                                    </div>
                                    <Button onClick={() => setShowCreateForm(true)}>
                                        <Plus className="h-4 w-4 mr-2" />
                                        Buat Kampanye
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                {showCreateForm && (
                                    <div className="mb-6 p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                                        <h4 className="font-semibold mb-4">
                                            {editingCampaign ? 'Edit Kampanye' : 'Buat Kampanye Baru'}
                                        </h4>
                                        <form onSubmit={handleSubmit} className="space-y-4">
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <Label htmlFor="title">Judul Kampanye *</Label>
                                                    <Input
                                                        id="title"
                                                        value={data.title}
                                                        onChange={(e) => setData('title', e.target.value)}
                                                        placeholder="Masukkan judul kampanye"
                                                    />
                                                    {errors.title && <p className="text-sm text-red-600">{errors.title}</p>}
                                                </div>
                                                <div>
                                                    <Label htmlFor="target_amount">Target Dana *</Label>
                                                    <Input
                                                        id="target_amount"
                                                        type="number"
                                                        value={data.target_amount}
                                                        onChange={(e) => setData('target_amount', e.target.value)}
                                                        placeholder="Masukkan target dana"
                                                    />
                                                    {errors.target_amount && <p className="text-sm text-red-600">{errors.target_amount}</p>}
                                                </div>
                                            </div>
                                            <div>
                                                <Label htmlFor="short_desc">Deskripsi Singkat</Label>
                                                <Input
                                                    id="short_desc"
                                                    value={data.short_desc}
                                                    onChange={(e) => setData('short_desc', e.target.value)}
                                                    placeholder="Deskripsi singkat kampanye"
                                                />
                                                {errors.short_desc && <p className="text-sm text-red-600">{errors.short_desc}</p>}
                                            </div>
                                            <div>
                                                <Label htmlFor="description">Deskripsi Lengkap</Label>
                                                <Textarea
                                                    id="description"
                                                    value={data.description}
                                                    onChange={(e) => setData('description', e.target.value)}
                                                    placeholder="Deskripsi lengkap kampanye"
                                                    rows={4}
                                                />
                                                {errors.description && <p className="text-sm text-red-600">{errors.description}</p>}
                                            </div>
                                            <div className="flex gap-2">
                                                <Button type="submit" disabled={processing}>
                                                    {processing ? 'Menyimpan...' : (editingCampaign ? 'Update' : 'Buat Kampanye')}
                                                </Button>
                                                <Button 
                                                    type="button" 
                                                    variant="outline" 
                                                    onClick={() => {
                                                        setShowCreateForm(false);
                                                        setEditingCampaign(null);
                                                        reset();
                                                    }}
                                                >
                                                    Batal
                                                </Button>
                                            </div>
                                        </form>
                                    </div>
                                )}

                                {campaigns.length > 0 ? (
                                    <div className="space-y-4">
                                        {campaigns.map((campaign) => (
                                            <div key={campaign.id} className="border rounded-lg p-4">
                                                <div className="flex items-center justify-between mb-2">
                                                    <h4 className="font-semibold">{campaign.title}</h4>
                                                    <div className="flex items-center gap-2">
                                                        {getStatusBadge(campaign.status)}
                                                        <div className="flex gap-1">
                                                            <Button 
                                                                variant="outline" 
                                                                size="sm"
                                                                onClick={() => handleEdit(campaign)}
                                                            >
                                                                <Edit className="h-3 w-3" />
                                                            </Button>
                                                            <Button 
                                                                variant="outline" 
                                                                size="sm"
                                                                onClick={() => handleDelete(campaign.id)}
                                                            >
                                                                <Trash2 className="h-3 w-3" />
                                                            </Button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                    {campaign.short_desc || campaign.description?.substring(0, 150) + '...'}
                                                </p>
                                                <div className="flex items-center justify-between">
                                                    <div className="flex items-center gap-4 text-sm">
                                                        <span>Target: Rp {campaign.target_amount.toLocaleString('id-ID')}</span>
                                                        <span>Terkumpul: Rp {campaign.collected_amount.toLocaleString('id-ID')}</span>
                                                        <span>Progress: {campaign.progress_percentage}%</span>
                                                    </div>
                                                    <Link href={`/campaigns/${campaign.slug}`}>
                                                        <Button variant="outline" size="sm">
                                                            <Eye className="h-3 w-3 mr-1" />
                                                            Lihat
                                                        </Button>
                                                    </Link>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="text-center py-8">
                                        <Target className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                        <p className="text-gray-500">Belum ada kampanye</p>
                                        <Button onClick={() => setShowCreateForm(true)} className="mt-2">
                                            Buat Kampanye Pertama
                                        </Button>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                )}

                {/* Admin role can see both */}
                {auth.user.role === 'creator' && (
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {/* Fundraiser Applications */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Users className="h-5 w-5" />
                                    Pengajuan Penggalang Dana
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {fundraiserApplications.length > 0 ? (
                                    <div className="space-y-3">
                                        {fundraiserApplications.slice(0, 5).map((application) => (
                                            <div key={application.id} className="flex items-center justify-between p-3 border rounded">
                                                <div>
                                                    <p className="font-medium">{application.full_name}</p>
                                                    <p className="text-sm text-gray-500">{new Date(application.created_at).toLocaleDateString('id-ID')}</p>
                                                </div>
                                                {getStatusBadge(application.status)}
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-gray-500 text-center py-4">Tidak ada pengajuan</p>
                                )}
                            </CardContent>
                        </Card>

                        {/* Recent Campaigns */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Target className="h-5 w-5" />
                                    Kampanye Terbaru
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {campaigns.length > 0 ? (
                                    <div className="space-y-3">
                                        {campaigns.slice(0, 5).map((campaign) => (
                                            <div key={campaign.id} className="flex items-center justify-between p-3 border rounded">
                                                <div>
                                                    <p className="font-medium">{campaign.title}</p>
                                                    <p className="text-sm text-gray-500">Rp {campaign.collected_amount.toLocaleString('id-ID')} / Rp {campaign.target_amount.toLocaleString('id-ID')}</p>
                                                </div>
                                                {getStatusBadge(campaign.status)}
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-gray-500 text-center py-4">Tidak ada kampanye</p>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
