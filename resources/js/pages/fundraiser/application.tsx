import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { AlertCircle, CheckCircle, Clock, Upload, User } from 'lucide-react';

interface FundraiserApplication {
    id: number;
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
}

interface Props {
    application?: FundraiserApplication;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Pengajuan Penggalang Dana',
        href: '/fundraiser/application',
    },
];

export default function FundraiserApplication({ application }: Props) {
    const { data, setData, post, put, processing, errors } = useForm({
        full_name: application?.full_name || '',
        phone: application?.phone || '',
        address: application?.address || '',
        id_card_number: application?.id_card_number || '',
        id_card_photo: null as File | null,
        motivation: application?.motivation || '',
        experience: application?.experience || '',
        social_media_links: application?.social_media_links || '',
    });

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        
        if (application) {
            put(`/fundraiser/application/${application.id}`, {
                forceFormData: true,
            });
        } else {
            post('/fundraiser/application', {
                forceFormData: true,
            });
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'pending':
                return <Badge variant="secondary" className="flex items-center gap-1"><Clock className="h-3 w-3" />Menunggu Review</Badge>;
            case 'approved':
                return <Badge variant="default" className="flex items-center gap-1 bg-green-500"><CheckCircle className="h-3 w-3" />Disetujui</Badge>;
            case 'rejected':
                return <Badge variant="destructive" className="flex items-center gap-1"><AlertCircle className="h-3 w-3" />Ditolak</Badge>;
            default:
                return null;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pengajuan Penggalang Dana" />
            
            <div className="container mx-auto max-w-4xl p-6">
                <div className="mb-6">
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                        Pengajuan Penggalang Dana
                    </h1>
                    <p className="mt-2 text-gray-600 dark:text-gray-400">
                        Bergabunglah dengan kami sebagai penggalang dana dan bantu menyebarkan kebaikan
                    </p>
                </div>

                {application && (
                    <Card className="mb-6">
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle className="flex items-center gap-2">
                                    <User className="h-5 w-5" />
                                    Status Pengajuan
                                </CardTitle>
                                {getStatusBadge(application.status)}
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm text-gray-600 dark:text-gray-400">
                                Pengajuan disubmit pada: {new Date(application.created_at).toLocaleDateString('id-ID', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                })}
                            </p>
                            {application.admin_notes && (
                                <div className="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h4 className="font-medium text-sm text-gray-900 dark:text-white mb-2">
                                        Catatan Admin:
                                    </h4>
                                    <p className="text-sm text-gray-600 dark:text-gray-400">
                                        {application.admin_notes}
                                    </p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                )}

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {application ? 'Edit Pengajuan' : 'Form Pengajuan Penggalang Dana'}
                        </CardTitle>
                        <CardDescription>
                            {application && !application.status.includes('pending') 
                                ? 'Pengajuan Anda sudah diproses dan tidak dapat diubah.'
                                : 'Lengkapi formulir di bawah ini untuk mengajukan diri sebagai penggalang dana.'
                            }
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <Label htmlFor="full_name">Nama Lengkap *</Label>
                                    <Input
                                        id="full_name"
                                        type="text"
                                        value={data.full_name}
                                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('full_name', e.target.value)}
                                        disabled={application && !application.status.includes('pending')}
                                        placeholder="Masukkan nama lengkap Anda"
                                    />
                                    {errors.full_name && (
                                        <p className="text-sm text-red-600">{errors.full_name}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="phone">Nomor Telepon *</Label>
                                    <Input
                                        id="phone"
                                        type="tel"
                                        value={data.phone}
                                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('phone', e.target.value)}
                                        disabled={application && !application.status.includes('pending')}
                                        placeholder="Contoh: 08123456789"
                                    />
                                    {errors.phone && (
                                        <p className="text-sm text-red-600">{errors.phone}</p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="address">Alamat Lengkap *</Label>
                                <Textarea
                                    id="address"
                                    value={data.address}
                                    onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setData('address', e.target.value)}
                                    disabled={application && !application.status.includes('pending')}
                                    placeholder="Masukkan alamat lengkap Anda"
                                    rows={3}
                                />
                                {errors.address && (
                                    <p className="text-sm text-red-600">{errors.address}</p>
                                )}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <Label htmlFor="id_card_number">Nomor KTP *</Label>
                                    <Input
                                        id="id_card_number"
                                        type="text"
                                        value={data.id_card_number}
                                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('id_card_number', e.target.value)}
                                        disabled={application && !application.status.includes('pending')}
                                        placeholder="Masukkan nomor KTP"
                                    />
                                    {errors.id_card_number && (
                                        <p className="text-sm text-red-600">{errors.id_card_number}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="id_card_photo">Foto KTP</Label>
                                    <div className="flex items-center gap-2">
                                        <Input
                                            id="id_card_photo"
                                            type="file"
                                            accept="image/*"
                                            onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('id_card_photo', e.target.files?.[0] || null)}
                                            disabled={application && !application.status.includes('pending')}
                                            className="file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        />
                                        <Upload className="h-4 w-4 text-gray-400" />
                                    </div>
                                    {application?.id_card_photo && (
                                        <p className="text-sm text-green-600">✓ File sudah terupload</p>
                                    )}
                                    {errors.id_card_photo && (
                                        <p className="text-sm text-red-600">{errors.id_card_photo}</p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="motivation">Motivasi Menjadi Penggalang Dana *</Label>
                                <Textarea
                                    id="motivation"
                                    value={data.motivation}
                                    onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setData('motivation', e.target.value)}
                                    disabled={application && !application.status.includes('pending')}
                                    placeholder="Ceritakan motivasi Anda untuk menjadi penggalang dana..."
                                    rows={4}
                                />
                                {errors.motivation && (
                                    <p className="text-sm text-red-600">{errors.motivation}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="experience">Pengalaman Terkait (Opsional)</Label>
                                <Textarea
                                    id="experience"
                                    value={data.experience}
                                    onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setData('experience', e.target.value)}
                                    disabled={application && !application.status.includes('pending')}
                                    placeholder="Ceritakan pengalaman Anda dalam bidang sosial, fundraising, atau kegiatan kemanusiaan..."
                                    rows={3}
                                />
                                {errors.experience && (
                                    <p className="text-sm text-red-600">{errors.experience}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="social_media_links">Link Media Sosial (Opsional)</Label>
                                <Input
                                    id="social_media_links"
                                    type="text"
                                    value={data.social_media_links}
                                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('social_media_links', e.target.value)}
                                    disabled={application && !application.status.includes('pending')}
                                    placeholder="Instagram, Facebook, LinkedIn, dll. (pisahkan dengan koma)"
                                />
                                {errors.social_media_links && (
                                    <p className="text-sm text-red-600">{errors.social_media_links}</p>
                                )}
                            </div>

                            {(!application || application.status === 'pending') && (
                                <div className="flex justify-end">
                                    <Button type="submit" disabled={processing} className="min-w-32">
                                        {processing ? 'Memproses...' : (application ? 'Update Pengajuan' : 'Kirim Pengajuan')}
                                    </Button>
                                </div>
                            )}
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
