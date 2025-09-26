import { Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';

export default function Contact() {
    return (
        <PublicLayout title="Hubungi Kami" currentPage="contact">

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    {/* Hero Section */}
                    <div className="text-center mb-12">
                        <h1 className="text-4xl font-bold text-white mb-4">
                            Hubungi Developer
                        </h1>
                        <p className="text-xl text-gray-300 max-w-2xl mx-auto">
                            Ada pertanyaan, saran, atau ingin berkolaborasi? Jangan ragu untuk menghubungi developer platform ini.
                        </p>
                    </div>

                    {/* Developer Profile */}
                    <div className="bg-gray-800 rounded-lg p-8 mb-12 text-center">
                        <div className="w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                            <img src="/profiles.jpg" alt="" className='object-cover rounded-full' />
                        </div>
                        <h2 className="text-2xl font-bold text-white mb-2">Fahmi Aksan Nugroho</h2>
                        <p className="text-blue-400 mb-4">Full Stack Developer</p>
                        <p className="text-gray-300 max-w-2xl mx-auto">
                            Passionate developer yang berdedikasi untuk menciptakan solusi teknologi yang bermanfaat 
                            untuk masyarakat. Spesialisasi dalam pengembangan web aplikasi modern dengan fokus pada 
                            user experience dan performance.
                        </p>
                    </div>

                    {/* Contact Methods */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        {/* Email */}
                        <div className="bg-gray-800 rounded-lg p-8 hover:bg-gray-750 transition-colors">
                            <div className="flex items-center mb-4">
                                <div className="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center mr-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 className="text-xl font-semibold text-white">Email</h3>
                                    <p className="text-gray-400">Untuk pertanyaan teknis & kolaborasi</p>
                                </div>
                            </div>
                            <a 
                                href="mailto:fahmiaksannugroho@gmail.com"
                                className="text-blue-400 hover:text-blue-300 font-medium transition-colors"
                            >
                                fahmiaksannugroho@gmail.com
                            </a>
                        </div>

                        {/* Instagram */}
                        <div className="bg-gray-800 rounded-lg p-8 hover:bg-gray-750 transition-colors">
                            <div className="flex items-center mb-4">
                                <div className="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-4">
                                    <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 className="text-xl font-semibold text-white">Instagram</h3>
                                    <p className="text-gray-400">Follow untuk update terbaru</p>
                                </div>
                            </div>
                            <a 
                                href="https://instagram.com/fhxmiii"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-blue-400 hover:text-blue-300 font-medium transition-colors"
                            >
                                @fhxmiii
                            </a>
                        </div>
                    </div>

                    {/* Additional Contact Info */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        <div className="bg-gray-800 rounded-lg p-6 text-center">
                            <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 className="text-lg font-semibold text-white mb-2">Response Time</h3>
                            <p className="text-gray-300">Biasanya membalas dalam 24 jam</p>
                        </div>

                        <div className="bg-gray-800 rounded-lg p-6 text-center">
                            <div className="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 className="text-lg font-semibold text-white mb-2">Location</h3>
                            <p className="text-gray-300">Indonesia</p>
                        </div>

                        <div className="bg-gray-800 rounded-lg p-6 text-center">
                            <div className="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                            </div>
                            <h3 className="text-lg font-semibold text-white mb-2">Tech Stack</h3>
                            <p className="text-gray-300">Laravel, React, TypeScript</p>
                        </div>
                    </div>

                    {/* FAQ Section */}
                    <div className="bg-gray-800 rounded-lg p-8 mb-12">
                        <h2 className="text-2xl font-bold text-white mb-6 text-center">
                            Frequently Asked Questions
                        </h2>
                        <div className="space-y-6">
                            <div className="border-b border-gray-700 pb-4">
                                <h3 className="text-lg font-semibold text-white mb-2">
                                    Apakah platform ini gratis digunakan?
                                </h3>
                                <p className="text-gray-300">
                                    Ya, platform ini gratis untuk digunakan oleh siapa saja yang ingin berdonasi atau membuat kampanye donasi.
                                </p>
                            </div>
                            <div className="border-b border-gray-700 pb-4">
                                <h3 className="text-lg font-semibold text-white mb-2">
                                    Bagaimana cara melaporkan bug atau masalah teknis?
                                </h3>
                                <p className="text-gray-300">
                                    Silakan kirim email ke fahmiaksannugroho@gmail.com dengan detail masalah yang Anda alami.
                                </p>
                            </div>
                            <div>
                                <h3 className="text-lg font-semibold text-white mb-2">
                                    Apakah bisa request fitur baru?
                                </h3>
                                <p className="text-gray-300">
                                    Tentu! Saya sangat terbuka dengan saran dan ide fitur baru. Hubungi melalui email atau Instagram.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Call to Action */}
                    <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
                        <h2 className="text-3xl font-bold text-white mb-4">
                            Mari Berkolaborasi!
                        </h2>
                        <p className="text-blue-100 mb-6 max-w-2xl mx-auto">
                            Punya ide untuk mengembangkan platform ini lebih baik? Atau ingin berkontribusi dalam pengembangan? 
                            Jangan ragu untuk menghubungi saya!
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <a
                                href="mailto:fahmiaksannugroho@gmail.com"
                                className="bg-white text-blue-600 hover:bg-gray-100 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Kirim Email
                            </a>
                            <a
                                href="https://instagram.com/fhxmiii"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Follow Instagram
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
