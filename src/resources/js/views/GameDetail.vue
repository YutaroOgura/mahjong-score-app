<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-8">
      <router-link :to="{ name: 'games' }" class="text-blue-600 hover:text-blue-800 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        対局一覧に戻る
      </router-link>
    </div>

    <div v-if="game" class="max-w-4xl mx-auto space-y-6">
      <!-- 基本情報 -->
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex justify-between items-start mb-6">
          <div>
            <h1 class="text-2xl font-bold mb-2 text-gray-800">{{ formatDate(game.played_at) }}の対局</h1>
            <p class="text-gray-600">{{ game.location || '場所未設定' }}</p>
          </div>
          <div class="flex space-x-3">
            <button @click="showEditModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
              編集
            </button>
            <button @click="confirmDelete"
              class="bg-white hover:bg-red-50 text-red-600 border border-red-300 px-4 py-2 rounded-md text-sm">
              削除
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-blue-50 p-4 rounded-md">
            <div class="text-sm text-blue-700 mb-1">ゲームタイプ</div>
            <div class="text-gray-800 font-medium">
              {{ game.game_type === 'east_only' ? '東風戦' : '東南戦' }}
            </div>
          </div>
          <div class="bg-blue-50 p-4 rounded-md">
            <div class="text-sm text-blue-700 mb-1">供託点</div>
            <div class="text-gray-800 font-medium">
              {{ game.starting_points.toLocaleString() }}点
            </div>
          </div>
          <div class="bg-blue-50 p-4 rounded-md">
            <div class="text-sm text-blue-700 mb-1">ウマ</div>
            <div class="text-gray-800 text-sm">
              1着: +{{ game.uma_1st }}、2着: +{{ game.uma_2nd }}、<br>
              3着: {{ game.uma_3rd }}、4着: {{ game.uma_4th }}
            </div>
          </div>
        </div>
      </div>

      <!-- 対局結果 -->
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h2 class="text-lg font-bold mb-4 text-blue-700 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
          </svg>
          対局結果
        </h2>
        <div class="space-y-3">
          <div v-for="result in sortedResults" :key="result.id"
            class="flex items-center justify-between p-4 bg-white rounded-md border border-gray-200 hover:bg-blue-50 transition-colors">
            <div class="flex items-center">
              <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold mr-3 text-white"
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
                <div class="text-sm text-gray-500">
                  {{ ['東家', '南家', '西家', '北家'][result.seat] }}
                </div>
              </div>
            </div>
            <div class="text-right">
              <div :class="getScoreClass(result.score)" class="text-lg font-bold">
                {{ result.score > 0 ? '+' : '' }}{{ result.score.toLocaleString() }}
              </div>
              <div class="text-sm text-gray-500">
                {{ result.points.toLocaleString() }}点
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- メモ -->
      <div v-if="game.note" class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h2 class="text-lg font-bold mb-4 text-blue-700 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
          メモ
        </h2>
        <p class="whitespace-pre-wrap text-gray-700 bg-blue-50 p-4 rounded-md">{{ game.note }}</p>
      </div>
    </div>

    <!-- 編集モーダル -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg p-6 max-w-2xl w-full border border-gray-200">
        <h2 class="text-xl font-bold mb-4 text-gray-800 pb-2 border-b border-gray-200">対局情報の編集</h2>
        <form @submit.prevent="updateGame">
          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  対局日時
                </label>
                <input type="datetime-local" v-model="editingGame.played_at" required
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  場所
                </label>
                <input type="text" v-model="editingGame.location"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  ゲームタイプ
                </label>
                <select v-model="editingGame.game_type" required
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                  <option value="east_south">東南戦</option>
                  <option value="east_only">東風戦</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                メモ
              </label>
              <textarea v-model="editingGame.note" rows="3"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
            </div>
          </div>

          <div class="flex justify-end mt-6 space-x-3">
            <button type="button" @click="showEditModal = false"
              class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
              キャンセル
            </button>
            <button type="submit"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-md text-white text-sm">
              更新
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- 削除確認モーダル -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg p-6 max-w-md w-full border border-gray-200">
        <h2 class="text-xl font-bold mb-2 text-gray-800">対局の削除</h2>
        <p class="mb-4 text-gray-600">この対局を削除してもよろしいですか？この操作は取り消せません。</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false"
            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
            キャンセル
          </button>
          <button @click="deleteGame"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-md text-white text-sm">
            削除
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'GameDetail',
  props: {
    id: {
      type: [Number, String],
      required: true
    }
  },
  setup(props) {
    const router = useRouter();
    const game = ref(null);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const editingGame = ref({
      played_at: '',
      location: '',
      game_type: '',
      note: ''
    });

    const fetchGame = async () => {
      try {
        const response = await axios.get(`/api/games/${props.id}`);
        game.value = response.data;
        editingGame.value = {
          played_at: game.value.played_at.slice(0, 16),
          location: game.value.location,
          game_type: game.value.game_type,
          note: game.value.note
        };
      } catch (error) {
        console.error('Failed to fetch game:', error);
        router.push({ name: 'games' });
      }
    };

    const sortedResults = computed(() => {
      if (!game.value) return [];
      return [...game.value.results].sort((a, b) => a.rank - b.rank);
    });

    const updateGame = async () => {
      try {
        await axios.put(`/api/games/${props.id}`, editingGame.value);
        showEditModal.value = false;
        await fetchGame();
      } catch (error) {
        console.error('Failed to update game:', error);
      }
    };

    const confirmDelete = () => {
      showDeleteModal.value = true;
    };

    const deleteGame = async () => {
      try {
        await axios.delete(`/api/games/${props.id}`);
        router.push({ name: 'games' });
      } catch (error) {
        console.error('Failed to delete game:', error);
      }
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('ja-JP', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    const getScoreClass = (score) => {
      return {
        'text-red-600': score < 0,
        'text-green-600': score > 0,
        'font-semibold': true
      };
    };

    onMounted(fetchGame);

    return {
      game,
      sortedResults,
      showEditModal,
      showDeleteModal,
      editingGame,
      updateGame,
      confirmDelete,
      deleteGame,
      formatDate,
      getScoreClass
    };
  }
};
</script> 