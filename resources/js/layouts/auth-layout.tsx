import AppLogoIcon from '@/components/app-logo-icon';
import Notification from '@/components/notification';
import { usePage } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    title?: string;
    description?: string;
}

export default function AuthLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    const currentYear = new Date().getFullYear();
    const { errors } = usePage().props as any;

    return (
        <div className="min-h-screen flex bg-gray-900">
            {/* Left side - Image (Desktop only) */}
            <div className="hidden md:flex md:w-1/2 min-h-screen relative">
                <img 
                    src="/storage/images/sidebar-login.png" 
                    alt="Beautiful landscape" 
                    className="w-full min-h-screen object-cover"
                />
            </div>

            {/* Right side - Form */}
            <div className="w-full md:w-1/2 flex flex-col justify-center px-6 py-12 md:px-8">
                <div className="sm:mx-auto sm:w-full sm:max-w-sm">
                    {/* Logo */}
                    {/* <div className="flex justify-center mb-6">
                        <div className="flex items-center flex-col">
                            <AppLogoIcon className="size-50" />
                            <span className="text-lg font-semibold text-white">Donation Platform</span>
                        </div>
                    </div> */}

                    {/* Error Notifications */}
                    {errors && Object.keys(errors).length > 0 && (
                        <div className="mb-6 space-y-3">
                            {errors.oauth && (
                                <Notification
                                    type="error"
                                    title="OAuth Error"
                                    message={errors.oauth}
                                />
                            )}
                            {errors.email && (
                                <Notification
                                    type="error"
                                    title="Email Error"
                                    message={errors.email}
                                />
                            )}
                            {errors.general && (
                                <Notification
                                    type="error"
                                    message={errors.general}
                                />
                            )}
                        </div>
                    )}

                    {/* Title */}
                    <h2 className="text-center text-2xl font-bold leading-9 tracking-tight text-white mb-2">
                        {title}
                    </h2>

                    {/* Description */}
                    {description && (
                        <p className="text-center text-sm text-white mb-8">
                            {description}
                        </p>
                    )}
                </div>

                <div className="sm:mx-auto sm:w-full sm:max-w-sm">
                    {children}
                </div>

                {/* Footer */}
                <div className="mt-8 flex items-center justify-center text-xs text-gray-200">
                    <span>© Donation {currentYear}</span>
                </div>
            </div>
        </div>
    );
}
