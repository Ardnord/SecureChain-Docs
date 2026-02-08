<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white border border-gray-300 rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Admin Login</h1>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/auth/process-login') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username
                </label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= old('username') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:border-gray-500 focus:outline-none"
                    placeholder="Username"
                    required
                    autofocus>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:border-gray-500 focus:outline-none"
                    placeholder="Password"
                    required>
            </div>

            <button
                type="submit"
                class="w-full bg-gray-800 hover:bg-gray-900 text-white font-semibold py-2 px-4 rounded transition-colors">
                Login
            </button>
        </form>
    </div>
</body>

</html>