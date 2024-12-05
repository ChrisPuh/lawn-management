<footer class="bg-primary-500 text-white">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <div>
                <h3 class="mb-4 text-lg font-semibold">About Us</h3>
                <p class="text-primary-100">Professional lawn management solutions for your garden needs.</p>
            </div>
            <div>
                <h3 class="mb-4 text-lg font-semibold">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('privacy') }}" class="text-primary-100 hover:text-white">Privacy
                            Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="text-primary-100 hover:text-white">Terms of
                            Service</a></li>
                    <li><a href="{{ route('contact') }}" class="text-primary-100 hover:text-white">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="mb-4 text-lg font-semibold">Contact</h3>
                <p class="text-primary-100">info@lawnmanagement.com</p>
            </div>
        </div>
        <div class="mt-8 border-t border-primary-400 pt-8 text-center text-primary-100">
            <p>&copy; {{ date('Y') }} Lawn Management. All rights reserved.</p>
        </div>
    </div>
</footer>
