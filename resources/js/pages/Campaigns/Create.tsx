import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Alert, AlertDescription } from '@/components/ui/alert';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type PageProps, type Category } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Upload, Target, Calendar, FileText, Image as ImageIcon, AlertCircle } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Buat Kampanye',
        href: '/campaign/create',
    },
];

interface Props extends PageProps {
    categories: Category[];
}

export default function CreateCampaign({ auth, categories }: Props) {
    const [previewImage, setPreviewImage] = useState<string | null>(null);
    
    const { data, setData, post, processing, errors, progress } = useForm({
        title: '',
        category_id: '',
        short_desc: '',
        description: '',
        target_amount: '',
        deadline: '',
        featured_image: null as File | null,
    });

    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setData('featured_image', file);
            
            // Create preview
            const reader = new FileReader();
            reader.onload = (e) => {
                setPreviewImage(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/campaign', {
            forceFormData: true,
            onSuccess: () => {
                // Success handled by redirect in controller
            },
        });
    };

    const formatCurrency = (value: string) => {
        const numericValue = value.replace(/\D/g, '');
        return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    const handleAmountChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value.replace(/\D/g, '');
        setData('target_amount', value);
    };

    // Get minimum date (tomorrow)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Buat Kampanye Baru" />
            
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                            Buat Kampanye Baru
                        </h1>
                        <p className="mt-2 text-gray-600 dark:text-gray-400">
                            Mulai penggalangan dana untuk tujuan yang bermakna
                        </p>
                    </div>
                    <Link href="/dashboard">
                        <Button variant="outline">
                            <ArrowLeft className="h-4 w-4 mr-2" />
                            Kembali ke Dashboard
                        </Button>
                    </Link>
                </div>

                {/* Form */}
                <Card className="max-w-4xl mx-auto w-full">
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Target className="h-5 w-5" />
                            Informasi Kampanye
                        </CardTitle>
                        <CardDescription>
                            Lengkapi informasi kampanye penggalangan dana Anda
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Basic Information */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <Label htmlFor="title" className="flex items-center gap-2">
                                        <FileText className="h-4 w-4" />
                                        Judul Kampanye *
                                    </Label>
                                    <Input
                                        id="title"
                                        type="text"
                                        value={data.title}
                                        onChange={(e) => setData('title', e.target.value)}
                                        placeholder="Masukkan judul kampanye yang menarik"
                                        className={errors.title ? 'border-red-500' : ''}
                                        maxLength={300}
                                    />
                                    {errors.title && (
                                        <p className="text-sm text-red-600 flex items-center gap-1">
                                            <AlertCircle className="h-3 w-3" />
                                            {errors.title}
                                        </p>
                                    )}
                                    <p className="text-xs text-gray-500">
                                        {data.title.length}/300 karakter
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="category_id">Kategori *</Label>
                                    <Select
                                        value={data.category_id}
                                        onValueChange={(value) => setData('category_id', value)}
                                    >
                                        <SelectTrigger className={errors.category_id ? 'border-red-500' : ''}>
                                            <SelectValue placeholder="Pilih kategori kampanye" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {categories.map((category) => (
                                                <SelectItem key={category.id} value={category.id.toString()}>
                                                    {category.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.category_id && (
                                        <p className="text-sm text-red-600 flex items-center gap-1">
                                            <AlertCircle className="h-3 w-3" />
                                            {errors.category_id}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Target Amount and Deadline */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <Label htmlFor="target_amount" className="flex items-center gap-2">
                                        <Target className="h-4 w-4" />
                                        Target Dana *
                                    </Label>
                                    <div className="relative">
                                        <span className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                            Rp
                                        </span>
                                        <Input
                                            id="target_amount"
                                            type="text"
                                            value={formatCurrency(data.target_amount)}
                                            onChange={handleAmountChange}
                                            placeholder="100.000"
                                            className={`pl-10 ${errors.target_amount ? 'border-red-500' : ''}`}
                                        />
                                    </div>
                                    {errors.target_amount && (
                                        <p className="text-sm text-red-600 flex items-center gap-1">
                                            <AlertCircle className="h-3 w-3" />
                                            {errors.target_amount}
                                        </p>
                                    )}
                                    <p className="text-xs text-gray-500">
                                        Minimum Rp 100.000
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="deadline" className="flex items-center gap-2">
                                        <Calendar className="h-4 w-4" />
                                        Batas Waktu *
                                    </Label>
                                    <Input
                                        id="deadline"
                                        type="date"
                                        value={data.deadline}
                                        onChange={(e) => setData('deadline', e.target.value)}
                                        min={minDate}
                                        className={errors.deadline ? 'border-red-500' : ''}
                                    />
                                    {errors.deadline && (
                                        <p className="text-sm text-red-600 flex items-center gap-1">
                                            <AlertCircle className="h-3 w-3" />
                                            {errors.deadline}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Short Description */}
                            <div className="space-y-2">
                                <Label htmlFor="short_desc">Deskripsi Singkat *</Label>
                                <Textarea
                                    id="short_desc"
                                    value={data.short_desc}
                                    onChange={(e) => setData('short_desc', e.target.value)}
                                    placeholder="Jelaskan kampanye Anda dalam beberapa kalimat"
                                    className={errors.short_desc ? 'border-red-500' : ''}
                                    rows={3}
                                    maxLength={500}
                                />
                                {errors.short_desc && (
                                    <p className="text-sm text-red-600 flex items-center gap-1">
                                        <AlertCircle className="h-3 w-3" />
                                        {errors.short_desc}
                                    </p>
                                )}
                                <p className="text-xs text-gray-500">
                                    {data.short_desc.length}/500 karakter
                                </p>
                            </div>

                            {/* Full Description */}
                            <div className="space-y-2">
                                <Label htmlFor="description">Deskripsi Lengkap *</Label>
                                <Textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    placeholder="Ceritakan detail kampanye Anda, tujuan, dan bagaimana dana akan digunakan"
                                    className={errors.description ? 'border-red-500' : ''}
                                    rows={6}
                                />
                                {errors.description && (
                                    <p className="text-sm text-red-600 flex items-center gap-1">
                                        <AlertCircle className="h-3 w-3" />
                                        {errors.description}
                                    </p>
                                )}
                            </div>

                            {/* Featured Image */}
                            <div className="space-y-2">
                                <Label htmlFor="featured_image" className="flex items-center gap-2">
                                    <ImageIcon className="h-4 w-4" />
                                    Gambar Utama
                                </Label>
                                <div className="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 overflow-hidden">
                                    <input
                                        id="featured_image"
                                        type="file"
                                        accept="image/*"
                                        onChange={handleImageChange}
                                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    />
                                    {previewImage ? (
                                        <div className="space-y-4 relative z-0">
                                            <img
                                                src={previewImage}
                                                alt="Preview"
                                                className="max-w-full h-48 object-cover rounded-lg mx-auto"
                                            />
                                            <div className="text-center">
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    onClick={() => {
                                                        setPreviewImage(null);
                                                        setData('featured_image', null);
                                                    }}
                                                    className="relative z-20"
                                                >
                                                    Hapus Gambar
                                                </Button>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="text-center relative z-0">
                                            <Upload className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                            <div className="space-y-2">
                                                <p className="text-sm text-gray-600 dark:text-gray-400">
                                                    Klik untuk upload gambar atau drag & drop
                                                </p>
                                                <p className="text-xs text-gray-500">
                                                    PNG, JPG, JPEG hingga 2MB
                                                </p>
                                            </div>
                                        </div>
                                    )}
                                </div>
                                {errors.featured_image && (
                                    <p className="text-sm text-red-600 flex items-center gap-1">
                                        <AlertCircle className="h-3 w-3" />
                                        {errors.featured_image}
                                    </p>
                                )}
                            </div>

                            {/* Upload Progress */}
                            {progress && (
                                <div className="space-y-2">
                                    <div className="flex justify-between text-sm">
                                        <span>Uploading...</span>
                                        <span>{progress.percentage}%</span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            className="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                            style={{ width: `${progress.percentage}%` }}
                                        ></div>
                                    </div>
                                </div>
                            )}

                            {/* Info Alert */}
                            <Alert>
                                <AlertCircle className="h-4 w-4" />
                                <AlertDescription>
                                    Kampanye akan disimpan sebagai draft dan perlu disetujui oleh admin sebelum dapat dipublikasikan.
                                </AlertDescription>
                            </Alert>

                            {/* Submit Button */}
                            <div className="flex gap-4 pt-6">
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    className="flex-1"
                                >
                                    {processing ? 'Menyimpan...' : 'Buat Kampanye'}
                                </Button>
                                <Link href="/dashboard">
                                    <Button type="button" variant="outline">
                                        Batal
                                    </Button>
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
