<nav class="bg-blue-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <h1 class="text-white text-xl font-bold">BK SMKN 2 Mojokerto</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('student.dashboard') }}" class="text-white hover:text-blue-200">Dashboard</a>
                <a href="{{ route('student.violations') }}" class="text-white hover:text-blue-200">Riwayat</a>
                <a href="{{ route('student.profile') }}" class="text-white hover:text-blue-200">Profil</a>
                <form method="POST" action="{{ route('student.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
