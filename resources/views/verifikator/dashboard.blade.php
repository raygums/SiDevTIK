<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Verifikator - Submission Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-6 border-b border-blue-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">Admin Panel</h1>
                        <p class="text-blue-200 text-sm">UPA TIK</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4">
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-blue-700 text-white mb-2">
                    <i class="fas fa-th-large"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-blue-700 text-blue-100 mb-2">
                    <i class="fas fa-file-alt"></i>
                    <span class="font-medium">All Submissions</span>
                </a>
            </nav>

            <div class="p-4 border-t border-blue-700">
                <div class="mb-3">
                    <p class="text-sm font-medium">Admin UPA TIK</p>
                    <p class="text-xs text-blue-200">admin@tik.unila.ac.id</p>
                </div>
                <button class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-blue-700 hover:bg-blue-600 rounded-lg transition">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-6">
                <h2 class="text-3xl font-bold text-gray-800">Submission Management</h2>
                <p class="text-gray-600 mt-1">Review and process domain requests</p>
            </header>

            <!-- Content -->
            <div class="p-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Pending Review -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Pending Review</p>
                                <p class="text-3xl font-bold text-gray-800">2</p>
                            </div>
                            <div class="w-14 h-14 bg-yellow-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Verified -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Verified</p>
                                <p class="text-3xl font-bold text-gray-800">2</p>
                            </div>
                            <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-check text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Completed</p>
                                <p class="text-3xl font-bold text-gray-800">2</p>
                            </div>
                            <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Rejected</p>
                                <p class="text-3xl font-bold text-gray-800">1</p>
                            </div>
                            <div class="w-14 h-14 bg-red-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-times-circle text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submissions Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Submissions List -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-1">Submissions</h3>
                            <p class="text-sm text-gray-600">Click to view details</p>
                        </div>

                        <!-- Tabs -->
                        <div class="flex space-x-1 border-b border-gray-200 mb-6">
                            <button class="px-4 py-2 text-sm font-medium text-gray-800 border-b-2 border-blue-600">
                                Pending
                            </button>
                            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                                Verified
                            </button>
                            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                                Completed
                            </button>
                            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                                Rejected
                            </button>
                        </div>

                        <!-- Submission Item -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer transition">
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">REQ-002</h4>
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                                    Rejected
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 mb-2">invalid-request.unila.ac.id</p>
                            <div class="text-xs text-gray-500">
                                <p>By: Dr. Siti Nurhaliza</p>
                                <p>2026-01-07 13:20:00</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detail View -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-600">Select a submission to view full details</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Button -->
            <button class="fixed bottom-6 right-6 w-12 h-12 bg-gray-800 hover:bg-gray-700 text-white rounded-full shadow-lg flex items-center justify-center transition">
                <i class="fas fa-question"></i>
            </button>
        </main>
    </div>
</body>
</html>