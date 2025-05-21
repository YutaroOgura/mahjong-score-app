<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-8">
      <router-link :to="{ name: 'players' }" class="text-blue-600 hover:text-blue-800">
        ← プレイヤー一覧に戻る
      </router-link>
    </div>

    <div v-if="player" class="space-y-8">
      <!-- プレイヤー基本情報 -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold mb-2">{{ player.name }}</h1>
            <p v-if="player.nickname" class="text-gray-600">{{ player.nickname }}</p>
          </div>
          <button @click="showEditModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            編集
          </button>
        </div>
      </div>

      <!-- 統計情報 -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">統計情報</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-gray-50 p-4 rounded">
            <div class="text-gray-600">総対局数</div>
            <div class="text-2xl font-bold">{{ statistics.total_games }}回</div>
          </div>
          <div class="bg-gray-50 p-4 rounded">
            <div class="text-gray-600">平均順位</div>
            <div class="text-2xl font-bold">{{ statistics.average_rank.toFixed(2) }}</div>
          </div>
          <div class="bg-gray-50 p-4 rounded">
            <div class="text-gray-600">総ポイント</div>
            <div class="text-2xl font-bold" :class="getScoreClass(statistics.total_points)">
              {{ statistics.total_points.toLocaleString() }}
            </div>
          </div>
        </div>

        <!-- 順位分布 -->
        <div class="mt-6">
          <h3 class="text-lg font-semibold mb-3">順位分布</h3>
          <div class="grid grid-cols-4 gap-4">
            <div v-for="rank in 4" :key="rank" class="bg-gray-50 p-4 rounded text-center">
              <div class="text-gray-600">{{ rank }}位</div>
              <div class="text-xl font-bold">
                {{ statistics.rank_distribution[rank] || 0 }}回
                ({{ calculatePercentage(statistics.rank_distribution[rank]) }}%)
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 月別成績 -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">月別成績</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">月</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">対局数</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">平均順位</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">月間収支</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="score in statistics.monthly_scores" :key="score.month">
                <td class="px-6 py-4 whitespace-nowrap">{{ score.month }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ score.games_played }}回</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ Number(score.average_rank).toFixed(2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap" :class="getScoreClass(score.total_score)">
                  {{ score.total_score.toLocaleString() }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 最近の対局 -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">最近の対局</h2>
        <div class="space-y-4">
          <div v-for="game in recentGames" :key="game.id" class="border rounded p-4 hover:bg-gray-50">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-600">{{ formatDate(game.played_at) }}</span>
              <span class="text-gray-600">{{ game.location || '場所未設定' }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div v-for="result in game.results" :key="result.id" class="flex justify-between items-center">
                <span>{{ result.player.name }}</span>
                <span :class="getScoreClass(result.score)">{{ result.score.toLocaleString() }}</span>
              </div>
            </div>
            <router-link :to="{ name: 'game-detail', params: { id: game.id }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 block">
              詳細を見る →
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- 編集モーダル -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6">プレイヤー情報編集</h2>
        <form @submit.prevent="updatePlayer">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-name">
              名前
            </label>
            <input v-model="editingPlayer.name" type="text" id="edit-name" required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-nickname">
              ニックネーム
            </label>
            <input v-model="editingPlayer.nickname" type="text" id="edit-nickname"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="flex justify-end">
            <button type="button" @click="showEditModal = false"
              class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
              キャンセル
            </button>
            <button type="submit"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              更新
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'PlayerDetail',
  props: {
    id: {
      type: [Number, String],
      required: true
    }
  },
  setup(props) {
    const player = ref(null);
    const statistics = ref({
      total_games: 0,
      total_points: 0,
      average_rank: 0,
      rank_distribution: {},
      monthly_scores: []
    });
    const recentGames = ref([]);
    const showEditModal = ref(false);
    const editingPlayer = ref({ name: '', nickname: '' });

    const fetchPlayerData = async () => {
      try {
        const [playerResponse, statsResponse] = await Promise.all([
          axios.get(`/api/players/${props.id}`),
          axios.get(`/api/players/${props.id}/statistics`)
        ]);
        
        player.value = playerResponse.data.player;
        recentGames.value = playerResponse.data.recent_games;
        statistics.value = statsResponse.data;
        
        editingPlayer.value = {
          name: player.value.name,
          nickname: player.value.nickname
        };
      } catch (error) {
        console.error('Failed to fetch player data:', error);
      }
    };

    const updatePlayer = async () => {
      try {
        await axios.put(`/api/players/${props.id}`, {
          name: editingPlayer.value.name,
          nickname: editingPlayer.value.nickname,
        });
        showEditModal.value = false;
        await fetchPlayerData();
      } catch (error) {
        console.error('Failed to update player:', error);
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

    const calculatePercentage = (count) => {
      if (!count || !statistics.value.total_games) return 0;
      return ((count / statistics.value.total_games) * 100).toFixed(1);
    };

    onMounted(fetchPlayerData);

    return {
      player,
      statistics,
      recentGames,
      showEditModal,
      editingPlayer,
      updatePlayer,
      formatDate,
      getScoreClass,
      calculatePercentage
    };
  }
};
</script> 