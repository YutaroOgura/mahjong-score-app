<template>
  <div class="container mx-auto px-4 py-8">
    <!-- ページヘッダー -->
    <div class="bg-white rounded-xl shadow-sm mb-8 overflow-hidden border border-gray-200">
      <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
        <div class="flex justify-between items-center">
          <h1 class="text-xl font-bold text-blue-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            対局一覧
          </h1>
          <router-link :to="{ name: 'game-create' }" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            新規対局を記録
          </router-link>
        </div>
      </div>
      
      <!-- フィルターエリア -->
      <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
        <div class="flex items-center">
          <span class="text-gray-500 text-sm mr-2">絞り込み:</span>
          <!-- ここにフィルターの要素を追加することができます -->
          <!-- 例: 期間、プレイヤー、場所など -->
        </div>
      </div>
    </div>

    <!-- 対局一覧 -->
    <div class="space-y-4">
      <div v-for="game in games" :key="game.id" class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 hover:border-blue-200 transition-colors">
        <!-- 対局ヘッダー -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
          <div class="flex justify-between items-start">
            <div>
              <div class="flex items-center text-gray-800 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                {{ formatDate(game.played_at) }}
              </div>
              <div class="text-sm text-gray-500 mt-1 flex items-center" v-if="game.location">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                {{ game.location }}
              </div>
            </div>
            <div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                {{ game.game_type === 'east_only' ? '東風戦' : '東南戦' }}
              </span>
            </div>
          </div>
        </div>

        <!-- 対局結果 -->
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div v-for="result in sortedResults(game.results)" :key="result.id"
              class="flex justify-between items-center p-3 rounded-lg transition-colors border border-gray-100 hover:bg-blue-50">
              <div class="flex items-center">
                <div class="w-8 h-8 flex items-center justify-center rounded-full mr-3 text-white font-bold"
                  :class="{
                    'bg-yellow-500': result.rank === 1,
                    'bg-gray-400': result.rank === 2,
                    'bg-amber-600': result.rank === 3,
                    'bg-red-500': result.rank === 4,
                  }">
                  {{ result.rank }}
                </div>
                <div>
                  <div class="font-medium text-gray-800">{{ result.player.name }}</div>
                  <div class="text-xs text-gray-500 mt-1">
                    {{ ['東家', '南家', '西家', '北家'][result.seat] }}
                  </div>
                </div>
              </div>
              <div class="text-right">
                <div :class="[
                  getScoreClass(result.score),
                  'font-bold text-base'
                ]">
                  {{ result.score > 0 ? '+' : '' }}{{ result.score.toLocaleString() }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ result.points.toLocaleString() }}点
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 対局フッター -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
          <div class="text-sm text-gray-500 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
              <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
            </svg>
            供託: {{ game.starting_points.toLocaleString() }}点
          </div>
          <router-link :to="{ name: 'game-detail', params: { id: game.id }}"
            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            詳細を見る
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </router-link>
        </div>
      </div>
    </div>

    <!-- ページネーション -->
    <div v-if="totalPages > 1" class="mt-8 flex justify-center space-x-2">
      <button @click="changePage(currentPage - 1)"
        :disabled="currentPage === 1"
        :class="[
          'px-4 py-2 rounded-md border',
          currentPage === 1
            ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
            : 'bg-white text-blue-600 border-blue-200 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
        ]">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
      <button v-for="page in displayedPages" :key="page"
        @click="changePage(page)"
        :class="[
          'px-4 py-2 rounded-md border',
          currentPage === page
            ? 'bg-blue-600 text-white border-blue-600'
            : 'bg-white text-gray-700 border-gray-200 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
        ]">
        {{ page }}
      </button>
      <button @click="changePage(currentPage + 1)"
        :disabled="currentPage === totalPages"
        :class="[
          'px-4 py-2 rounded-md border',
          currentPage === totalPages
            ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
            : 'bg-white text-blue-600 border-blue-200 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
        ]">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'Games',
  setup() {
    const games = ref([]);
    const currentPage = ref(1);
    const totalPages = ref(1);
    const perPage = 20;

    const fetchGames = async (page) => {
      try {
        const response = await axios.get(`/api/games?page=${page}`);
        games.value = response.data.data;
        currentPage.value = response.data.current_page;
        totalPages.value = response.data.last_page;
      } catch (error) {
        console.error('Failed to fetch games:', error);
      }
    };

    const changePage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        fetchGames(page);
      }
    };

    const displayedPages = computed(() => {
      const pages = [];
      const maxDisplayed = 5;
      let start = Math.max(1, currentPage.value - Math.floor(maxDisplayed / 2));
      let end = Math.min(totalPages.value, start + maxDisplayed - 1);

      if (end - start + 1 < maxDisplayed) {
        start = Math.max(1, end - maxDisplayed + 1);
      }

      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    });

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('ja-JP');
    };

    const getScoreClass = (score) => {
      return {
        'text-red-600': score < 0,
        'text-green-600': score > 0,
        'font-semibold': true
      };
    };

    const sortedResults = (results) => {
      return [...results].sort((a, b) => a.rank - b.rank);
    };

    onMounted(() => {
      fetchGames(1);
    });

    return {
      games,
      currentPage,
      totalPages,
      displayedPages,
      changePage,
      formatDate,
      getScoreClass,
      sortedResults
    };
  }
};
</script> 