<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document System</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">


    <div class="ml-5 mr-5">
        <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
            <div class="px-8 py-4">
                <div class="flex items-center justify-end">
                    <div class="text-right">
                        <p class="text-sm text-slate-600" id="currentDate"></p>
                        <p class="text-xs text-slate-500" id="currentTime"></p>
                    </div>
                </div>
            </div>
        </header>

        <main class=" min-h-screen">
            <?= $this->renderSection('content') ?>
        </main>

        <footer class="bg-white border-t border-slate-200 py-6">
            <div class="px-8">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-600">© <?= date('Y') ?>. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
    function updateClock() {
        const now = new Date();
        
        const wibTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }));
        
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        const dayName = days[wibTime.getDay()];
        const day = wibTime.getDate();
        const month = months[wibTime.getMonth()];
        const year = wibTime.getFullYear();
        
        const hours = String(wibTime.getHours()).padStart(2, '0');
        const minutes = String(wibTime.getMinutes()).padStart(2, '0');
        const seconds = String(wibTime.getSeconds()).padStart(2, '0');
        
        document.getElementById('currentDate').textContent = `${dayName}, ${day} ${month} ${year}`;
        document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds} WIB`;
    }
    
    updateClock();
    setInterval(updateClock, 1000);
    </script>

</body>
</html>