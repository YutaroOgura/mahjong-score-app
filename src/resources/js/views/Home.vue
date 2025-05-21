<template>
  <div>
    <!-- JANRECO風ヒーローセクション -->
    <div class="relative h-[40vh] min-h-[220px] mb-8 rounded-lg overflow-hidden flex flex-col items-center justify-center bg-gradient-to-br from-blue-50 via-white to-gray-100 border border-blue-100">
      <div class="flex flex-col items-center justify-center w-full h-full text-center px-4">
        <div class="mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
          </svg>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2 tracking-tight">麻雀収支管理アプリ</h1>
        <p class="text-base md:text-lg text-gray-500 mb-6 max-w-xl mx-auto">シンプルで見やすいUIで、あなたの麻雀成績をスマートに記録・分析。</p>
        <div class="flex flex-wrap justify-center gap-4">
          <router-link to="/games/create"
            class="inline-flex items-center px-6 py-3 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-medium transition duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            新しい対局を記録
          </router-link>
          <router-link to="/players"
            class="inline-flex items-center px-6 py-3 rounded-md bg-gray-100 hover:bg-gray-200 text-blue-700 font-medium transition duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
            </svg>
            プレイヤーを管理
          </router-link>
        </div>
      </div>
    </div>
    
    <!-- メインコンテンツ -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- 最近の対局 -->
      <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
          <h2 class="text-lg font-bold text-blue-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
            最近の対局
          </h2>
        </div>
        <div v-if="recentGames.length > 0" class="flex space-x-4 overflow-x-auto px-6 py-4 snap-x snap-mandatory">
          <div v-for="game in recentGames" :key="game.id" class="min-w-[260px] snap-center rounded-lg border border-gray-200 bg-white p-4 hover:border-blue-200 transition duration-150">
            <div class="flex justify-between items-center mb-3">
              <span class="text-sm font-medium text-gray-700 bg-blue-50 px-2 py-1 rounded">{{ formatDate(game.played_at) }}</span>
              <span class="text-sm text-gray-500">{{ game.location || '場所未設定' }}</span>
            </div>
            <div class="space-y-2">
              <div v-for="result in game.results" :key="result.id" class="flex justify-between items-center">
                <div class="flex items-center">
                  <div class="w-6 h-6 flex items-center justify-center rounded-full mr-2 text-white"
                    :class="{
                      'bg-yellow-500': result.rank === 1,
                      'bg-gray-400': result.rank === 2,
                      'bg-amber-600': result.rank === 3,
                      'bg-red-500': result.rank === 4,
                    }">
                    {{ result.rank }}
                  </div>
                  <span class="font-medium text-gray-800">{{ result.player.name }}</span>
                </div>
                <span :class="[
                  getScoreClass(result.score),
                  'font-bold'
                ]">{{ result.score > 0 ? '+' : '' }}{{ result.score.toLocaleString() }}</span>
              </div>
            </div>
            <router-link :to="{ name: 'game-detail', params: { id: game.id }}" class="text-blue-600 hover:text-blue-800 text-sm mt-3 flex items-center justify-end">
              詳細を見る
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </router-link>
          </div>
        </div>
        <div v-else class="text-gray-500 text-center py-8">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          まだ対局記録がありません
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
          <router-link :to="{ name: 'games' }" class="text-blue-600 hover:text-blue-800 flex items-center justify-center">
            すべての対局を見る
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </router-link>
        </div>
      </div>

      <!-- プレイヤーランキング -->
      <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
          <h2 class="text-lg font-bold text-blue-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
            </svg>
            プレイヤーランキング
          </h2>
        </div>
        <div v-if="topPlayers.length > 0" class="flex space-x-4 overflow-x-auto px-6 py-4 snap-x snap-mandatory">
          <div v-for="(player, index) in topPlayers" :key="player.id" class="min-w-[220px] snap-center rounded-lg border border-gray-200 bg-white px-4 py-3 hover:border-blue-200 transition duration-150" data-aos="fade-up" data-aos-delay="100">
            <div class="flex justify-between items-center">
              <div class="flex items-center">
                <div class="w-8 h-8 flex items-center justify-center rounded-full mr-3 text-white font-bold"
                  :class="{
                    'bg-yellow-500': index === 0,
                    'bg-gray-400': index === 1,
                    'bg-amber-600': index === 2,
                    'bg-blue-500': index > 2
                  }">
                  {{ index + 1 }}
                </div>
                <div>
                  <div class="font-medium text-gray-800">{{ player.name }}</div>
                  <div class="text-sm text-gray-500 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                      対局数: {{ player.total_games }}回
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                      平均順位: {{ player.average_rank.toFixed(2) }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <div :class="[
                  getScoreClass(player.total_points),
                  'text-xl font-bold'
                ]">
                  {{ player.total_points > 0 ? '+' : '' }}{{ player.total_points.toLocaleString() }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-500 text-center py-8">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          まだプレイヤーが登録されていません
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
          <router-link :to="{ name: 'players' }" class="text-blue-600 hover:text-blue-800 flex items-center justify-center">
            すべてのプレイヤーを見る
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'Home',
  setup() {
    const recentGames = ref([]);
    const topPlayers = ref([]);
    const heroVisible = ref(false);
    // 1st try: picsum.photos generates a random image
    const heroImage = ref('https://picsum.photos/seed/mahjong/1600/900');
    const onHeroError = () => {
      // 2nd fallback: placeholder image with text
      heroImage.value = 'https://via.placeholder.com/1600x900.png?text=Mahjong';
    };

    const fetchRecentGames = async () => {
      try {
        const response = await axios.get('/api/games?limit=5');
        recentGames.value = response.data.data;
      } catch (error) {
        console.error('Failed to fetch recent games:', error);
      }
    };

    const fetchTopPlayers = async () => {
      try {
        const response = await axios.get('/api/players');
        topPlayers.value = response.data
          .sort((a, b) => b.total_points - a.total_points)
          .slice(0, 5);
      } catch (error) {
        console.error('Failed to fetch top players:', error);
      }
    };

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

    onMounted(() => {
      fetchRecentGames();
      fetchTopPlayers();
      // 次の描画フレームでフェードイン開始
      requestAnimationFrame(() => heroVisible.value = true);
    });

    return {
      recentGames,
      topPlayers,
      formatDate,
      getScoreClass,
      heroVisible,
      heroImage,
      onHeroError
    };
  }
};
</script> 