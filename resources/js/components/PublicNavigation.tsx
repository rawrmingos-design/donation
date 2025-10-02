import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { PageProps } from '@/types';

interface PublicNavigationProps {
    currentPage?: string;
}

export default function PublicNavigation({ currentPage }: PublicNavigationProps) {
    const { auth } = usePage<PageProps>().props;
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    const isActive = (page: string) => currentPage === page;

    const navItems = [
        { href: '/about', label: 'Tentang', page: 'about' },
        { href: '/campaigns', label: 'Jelajahi Kampanye', page: 'campaigns' },
        { href: '/how-it-works', label: 'Cara Kerja', page: 'how-it-works' },
        { href: '/faq', label: 'FAQ', page: 'faq' },
        { href: '/contact', label: 'Kontak', page: 'contact' },
    ];

    return (
        <nav className="bg-gray-800/95 backdrop-blur-sm border-b border-gray-700/50 sticky top-0 z-50">
            <div className="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-16">
                    {/* Logo */}
                    <div className="flex items-center">
                        <Link href="/" className="flex-shrink-0 group">
                            <div className="flex items-center space-x-2">
                                <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <h1 className="text-xl sm:text-2xl font-bold text-white group-hover:text-blue-400 transition-colors">
                                    <span className="hidden sm:inline">Donation</span>Platform
                                </h1>
                            </div>
                        </Link>
                    </div>

                    {/* Desktop Navigation */}
                    <div className="hidden lg:flex items-center space-x-1">
                        {navItems.map((item) => (
                            <Link
                                key={item.page}
                                href={item.href}
                                className={`px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-700/50 ${
                                    isActive(item.page) 
                                        ? 'text-blue-400 bg-blue-500/10 border border-blue-500/20' 
                                        : 'text-gray-300 hover:text-white'
                                }`}
                            >
                                {item.label}
                            </Link>
                        ))}
                        
                        {/* Auth Buttons */}
                        <div className="flex items-center space-x-2 ml-4 pl-4 border-l border-gray-700">
                            {auth?.user ? (
                                <div className="flex items-center space-x-2">
                                    <span className="text-sm text-gray-400">Halo, {auth.user.name}</span>
                                    <Link
                                        href="/dashboard"
                                        className="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl"
                                    >
                                        Dashboard
                                    </Link>
                                </div>
                            ) : (
                                <>
                                    <Link
                                        href="/login"
                                        className="text-gray-300 hover:text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-700/50"
                                    >
                                        Masuk
                                    </Link>
                                    <Link
                                        href="/register"
                                        className="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl"
                                    >
                                        Daftar
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>

                    {/* Mobile menu button */}
                    <div className="lg:hidden flex items-center">
                        {auth?.user && (
                            <Link
                                href="/dashboard"
                                className="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium mr-3"
                            >
                                Dashboard
                            </Link>
                        )}
                        <button
                            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                            className="text-gray-300 hover:text-white p-2 rounded-lg hover:bg-gray-700/50 transition-colors"
                            aria-label="Toggle mobile menu"
                        >
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {isMobileMenuOpen ? (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                ) : (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                )}
                            </svg>
                        </button>
                    </div>
                </div>

                {/* Mobile Navigation Menu */}
                <div className={`lg:hidden transition-all duration-300 ease-in-out ${
                    isMobileMenuOpen 
                        ? 'max-h-screen opacity-100 pb-4' 
                        : 'max-h-0 opacity-0 overflow-hidden'
                }`}>
                    <div className="pt-2 pb-3 space-y-1 border-t border-gray-700/50 mt-2">
                        {navItems.map((item) => (
                            <Link
                                key={item.page}
                                href={item.href}
                                onClick={() => setIsMobileMenuOpen(false)}
                                className={`block px-3 py-3 rounded-lg text-base font-medium transition-all duration-200 ${
                                    isActive(item.page) 
                                        ? 'text-blue-400 bg-blue-500/10 border-l-4 border-blue-500' 
                                        : 'text-gray-300 hover:text-white hover:bg-gray-700/50'
                                }`}
                            >
                                {item.label}
                            </Link>
                        ))}
                        
                        {/* Mobile Auth Section */}
                        {!auth?.user && (
                            <div className="pt-4 mt-4 border-t border-gray-700/50 space-y-2">
                                <Link
                                    href="/login"
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="block w-full text-center text-gray-300 hover:text-white px-3 py-3 rounded-lg text-base font-medium transition-all duration-200 hover:bg-gray-700/50"
                                >
                                    Masuk
                                </Link>
                                <Link
                                    href="/register"
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="block w-full text-center bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-3 py-3 rounded-lg text-base font-medium transition-all duration-200"
                                >
                                    Daftar
                                </Link>
                            </div>
                        )}
                        
                        {auth?.user && (
                            <div className="pt-4 mt-4 border-t border-gray-700/50">
                                <div className="px-3 py-2 text-sm text-gray-400">
                                    Halo, {auth.user.name}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </nav>
    );
}
