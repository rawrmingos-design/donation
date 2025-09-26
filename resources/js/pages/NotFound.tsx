import { Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { useState, useEffect } from 'react';

export default function NotFound() {
    const [isFloating, setIsFloating] = useState(false);
    const [mousePosition, setMousePosition] = useState({ x: 0, y: 0 });

    useEffect(() => {
        const interval = setInterval(() => {
            setIsFloating(prev => !prev);
        }, 3000);

        const handleMouseMove = (e: MouseEvent) => {
            setMousePosition({ x: e.clientX, y: e.clientY });
        };

        window.addEventListener('mousemove', handleMouseMove);

        return () => {
            clearInterval(interval);
            window.removeEventListener('mousemove', handleMouseMove);
        };
    }, []);

    return (
        <PublicLayout title="Halaman Tidak Ditemukan" currentPage="">
            <div className="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 flex items-center justify-center relative overflow-hidden">
                {/* Floating Background Elements */}
                <div className="absolute inset-0 overflow-hidden">
                    {[...Array(20)].map((_, i) => (
                        <div
                            key={i}
                            className="absolute animate-pulse"
                            style={{
                                left: `${Math.random() * 100}%`,
                                top: `${Math.random() * 100}%`,
                                animationDelay: `${Math.random() * 3}s`,
                                animationDuration: `${3 + Math.random() * 2}s`
                            }}
                        >
                            <div className="w-2 h-2 bg-blue-400 rounded-full opacity-30"></div>
                        </div>
                    ))}
                </div>

                {/* Mouse Follower */}
                <div
                    className="fixed w-4 h-4 bg-gradient-to-r from-pink-400 to-purple-400 rounded-full pointer-events-none z-10 transition-all duration-300 ease-out opacity-50"
                    style={{
                        left: mousePosition.x - 8,
                        top: mousePosition.y - 8,
                        transform: 'scale(1.5)',
                    }}
                ></div>

                <div className="text-center px-4 sm:px-6 lg:px-8 relative z-20">
                    {/* Animated 404 */}
                    <div className="mb-8">
                        <div className={`text-8xl md:text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 transition-all duration-1000 ${isFloating ? 'transform -translate-y-4' : ''}`}>
                            4
                            <span className="inline-block animate-spin text-yellow-400">🎯</span>
                            4
                        </div>
                    </div>

                    {/* Bouncing Robot/Character */}
                    <div className="mb-8 flex justify-center">
                        <div className="relative">
                            <div className={`text-6xl transition-all duration-500 ${isFloating ? 'transform -translate-y-2 rotate-12' : 'rotate-0'}`}>
                                🤖
                            </div>
                            {/* Speech Bubble */}
                            <div className="absolute -top-16 -left-8 bg-white rounded-lg px-4 py-2 shadow-lg animate-bounce">
                                <div className="text-gray-800 text-sm font-medium">Oops!</div>
                                <div className="absolute bottom-0 left-6 transform translate-y-full">
                                    <div className="w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-white"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Error Message */}
                    <div className="mb-8 space-y-4">
                        <h1 className="text-4xl md:text-5xl font-bold text-white mb-4 animate-pulse">
                            Halaman Tidak Ditemukan
                        </h1>
                        <p className="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed">
                            Sepertinya halaman yang Anda cari sedang berlibur! 🏖️
                            <br />
                            Mungkin sedang membantu kampanye donasi di tempat lain...
                        </p>
                    </div>

                    {/* Animated Icons */}
                    <div className="mb-8 flex justify-center space-x-8">
                        {['💝', '🎁', '❤️', '🌟', '🎈'].map((emoji, index) => (
                            <div
                                key={index}
                                className="text-3xl animate-bounce"
                                style={{
                                    animationDelay: `${index * 0.2}s`,
                                    animationDuration: '2s'
                                }}
                            >
                                {emoji}
                            </div>
                        ))}
                    </div>

                    {/* Action Buttons */}
                    <div className="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                        <Link
                            href="/"
                            className="group inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                        >
                            <svg className="w-5 h-5 mr-2 group-hover:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Kembali ke Beranda
                        </Link>

                        <Link
                            href="/campaigns"
                            className="group inline-flex items-center bg-transparent border-2 border-white text-white hover:bg-white hover:text-gray-900 font-semibold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105"
                        >
                            <svg className="w-5 h-5 mr-2 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Jelajahi Kampanye
                        </Link>
                    </div>

                    {/* Fun Facts */}
                    <div className="mt-12 bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 max-w-md mx-auto">
                        <h3 className="text-lg font-semibold text-white mb-3">💡 Tahukah Anda?</h3>
                        <p className="text-gray-300 text-sm">
                            Sementara Anda di sini, ada {Math.floor(Math.random() * 50) + 10} kampanye donasi yang sedang menunggu dukungan Anda!
                        </p>
                    </div>

                    {/* Loading Animation */}
                    <div className="mt-8 flex justify-center">
                        <div className="flex space-x-2">
                            {[...Array(3)].map((_, i) => (
                                <div
                                    key={i}
                                    className="w-3 h-3 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full animate-bounce"
                                    style={{
                                        animationDelay: `${i * 0.2}s`,
                                        animationDuration: '1.4s'
                                    }}
                                ></div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Floating Hearts */}
                <div className="absolute bottom-0 left-0 w-full overflow-hidden pointer-events-none">
                    {[...Array(5)].map((_, i) => (
                        <div
                            key={i}
                            className="absolute text-red-400 text-2xl animate-ping opacity-30"
                            style={{
                                left: `${20 + i * 15}%`,
                                bottom: `${Math.random() * 20}%`,
                                animationDelay: `${i * 0.5}s`,
                                animationDuration: '3s'
                            }}
                        >
                            ❤️
                        </div>
                    ))}
                </div>
            </div>
        </PublicLayout>
    );
}
