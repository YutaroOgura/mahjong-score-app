<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col">
    <!-- ナビゲーションバー -->
    <nav class="bg-white shadow-md border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <router-link to="/" class="flex items-center">
                <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-blue-500">麻雀収支管理</span>
              </router-link>
            </div>
            <!-- Desktop Nav -->
            <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
              <router-link 
                to="/" 
                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150"
                active-class="border-indigo-500 text-gray-900"
                exact
              >
                ホーム
              </router-link>
              <router-link 
                to="/games" 
                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150"
                active-class="border-indigo-500 text-gray-900"
              >
                対局記録
              </router-link>
              <router-link 
                to="/players" 
                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150"
                active-class="border-indigo-500 text-gray-900"
              >
                プレイヤー
              </router-link>
            </div>
          </div>
          <!-- Mobile hamburger -->
          <div class="flex items-center sm:hidden">
            <button @click="toggleMenu" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition">
              <svg v-if="!isMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <!-- Desktop CTA -->
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <router-link 
              to="/games/create" 
              class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150"
            >
              新規対局
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Dropdown Menu -->
    <transition name="fade">
      <div v-if="isMenuOpen" class="sm:hidden bg-white border-b border-gray-200 shadow-md">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <router-link @click.native="closeMenu" to="/" exact class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100" active-class="bg-indigo-50 text-indigo-600">ホーム</router-link>
          <router-link @click.native="closeMenu" to="/games" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100" active-class="bg-indigo-50 text-indigo-600">対局記録</router-link>
          <router-link @click.native="closeMenu" to="/players" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100" active-class="bg-indigo-50 text-indigo-600">プレイヤー</router-link>
          <router-link @click.native="closeMenu" to="/games/create" class="block w-full text-center px-3 py-2 rounded-md font-medium bg-indigo-600 text-white hover:bg-indigo-700">新規対局</router-link>
        </div>
      </div>
    </transition>

    <!-- メインコンテンツ -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <router-view></router-view>
    </main>

    <!-- フッター -->
    <footer class="bg-white mt-auto border-t border-gray-200">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 text-sm">
          © {{ new Date().getFullYear() }} 麻雀収支管理アプリ
        </p>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
body {
  font-family: 'Hiragino Sans', 'Hiragino Kaku Gothic ProN', 'Noto Sans JP', Meiryo, sans-serif;
}
</style> 