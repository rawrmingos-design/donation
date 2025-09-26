import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { useState, useEffect } from 'react';
import toast from 'react-hot-toast';

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const [remember, setRemember] = useState(false);

    // Show status message with toast
    useEffect(() => {
        if (status) {
            toast.success(status, {
                icon: '✅',
                duration: 4000,
            });
        }
    }, [status]);

    const switchRemember = () => {
        setRemember(!remember);
    };

    return (
        <AuthLayout title="Selamat Datang Kembali!" description="Masuk ke akun Anda untuk dapat berpartisipasi dalam kampanye donasi.">
            <Head title="Log in">
                <link rel="icon" type="image/png" href="/favicon.png" />
            </Head>


            <Form {...AuthenticatedSessionController.store.form()} resetOnSuccess={['password']} className="space-y-6">
                {({ processing, errors }) => (
                    <>
                        <div className="space-y-4">
                            <div>
                                <Label htmlFor="email" className="text-sm font-medium text-white">
                                    Email
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="email"
                                    placeholder="Email or phone number"
                                    className="mt-1 block w-full focus:text-gray-700 text-gray-800 rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-sm placeholder-gray-400 shadow-sm focus:border-gray-500 focus:bg-white focus:outline-none focus:ring-gray-500"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div>
                                <Label htmlFor="password" className="text-sm font-medium text-white">
                                    Password
                                </Label>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    tabIndex={2}
                                    autoComplete="current-password"
                                    placeholder="Enter password"
                                    className="mt-1 block w-full focus:text-gray-700 text-gray-800 rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-sm placeholder-gray-400 shadow-sm focus:border-gray-500 focus:bg-white focus:outline-none focus:ring-gray-500"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="flex items-center justify-between">
                                <div className="flex items-center">
                                    <Switch checked={remember} onCheckedChange={switchRemember} className={remember ? 'bg-red-500' : 'bg-green-500'} id="remember" name="remember" tabIndex={3} />
                                    <Label htmlFor="remember" className="ml-3 text-xs md:text-sm text-gray-300">
                                        Remember me
                                    </Label>
                                </div>
                                {canResetPassword && (
                                    <TextLink href={request()} className="text-xs md:text-sm text-blue-600 hover:text-blue-500" tabIndex={5}>
                                        Forgot password?
                                    </TextLink>
                                )}
                            </div>


                            <Button 
                                type="submit" 
                                className="w-full rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600" 
                                tabIndex={4} 
                                disabled={processing}
                            >
                                {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                Sign In
                            </Button>
                        </div>

                        <div className="relative">
                            <div className="absolute inset-0 flex items-center">
                                <span className="w-full border-t border-gray-300" />
                            </div>
                            <div className="relative flex justify-center text-base ">
                                <span className="bg-gray-900 px-2 text-gray-200">Or sign in with</span>
                            </div>
                        </div>

                        <div className="space-y-3 flex gap-2">
                            <Button
                                variant="outline"
                                type="button"
                                onClick={() => window.location.href = '/auth/google'}
                                className="w-full cursor-pointer rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-700"
                                tabIndex={6}
                            >
                                <svg className="mr-2 h-4 w-4" viewBox="0 0 24 24">
                                    <path
                                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                        fill="#4285F4"
                                    />
                                    <path
                                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                        fill="#34A853"
                                    />
                                    <path
                                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                        fill="#FBBC05"
                                    />
                                    <path
                                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                        fill="#EA4335"
                                    />
                                </svg>
                                Google
                            </Button>

                            <Button
                                variant="outline"
                                type="button"
                                onClick={() => window.location.href = '/auth/facebook'}
                                className="w-full cursor-pointer rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-700"
                                tabIndex={7}
                            >
                                <svg className="mr-2 h-4 w-4" fill="#1877F2" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </Button>
                        </div>

                        <div className="text-center text-sm text-gray-600">
                            Don't have an account?{' '}
                            <TextLink href={register()} className="font-semibold text-blue-600 hover:text-blue-500" tabIndex={8}>
                                Sign up now
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
