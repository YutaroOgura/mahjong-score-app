<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">プレイヤー一覧</h1>
      <button @click="showCreateModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        新規プレイヤー登録
      </button>
    </div>

    <!-- プレイヤー一覧テーブル -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">名前</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ニックネーム</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">対局数</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">平均順位</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">総ポイント</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="player in players" :key="player.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <router-link :to="{ name: 'player-detail', params: { id: player.id }}" class="text-blue-600 hover:text-blue-800">
                {{ player.name }}
              </router-link>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ player.nickname || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ player.total_games }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ player.average_rank.toFixed(2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap" :class="getScoreClass(player.total_points)">
              {{ player.total_points.toLocaleString() }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="editPlayer(player)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                編集
              </button>
              <button @click="confirmDelete(player)" class="text-red-600 hover:text-red-900">
                削除
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 新規作成モーダル -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6">新規プレイヤー登録</h2>
        <form @submit.prevent="createPlayer">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
              名前
            </label>
            <input v-model="newPlayer.name" type="text" id="name" required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="nickname">
              ニックネーム
            </label>
            <input v-model="newPlayer.nickname" type="text" id="nickname"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="flex justify-end">
            <button type="button" @click="showCreateModal = false"
              class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
              キャンセル
            </button>
            <button type="submit"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              登録
            </button>
          </div>
        </form>
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

    <!-- 削除確認モーダル -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6">プレイヤー削除の確認</h2>
        <p class="mb-6">{{ deletingPlayer.name }}を削除してもよろしいですか？</p>
        <div class="flex justify-end">
          <button @click="showDeleteModal = false"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
            キャンセル
          </button>
          <button @click="deletePlayer"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            削除
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'Players',
  setup() {
    const players = ref([]);
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const newPlayer = ref({ name: '', nickname: '' });
    const editingPlayer = ref({ id: null, name: '', nickname: '' });
    const deletingPlayer = ref({ id: null, name: '' });

    const fetchPlayers = async () => {
      try {
        const response = await axios.get('/api/players');
        players.value = response.data;
      } catch (error) {
        console.error('Failed to fetch players:', error);
      }
    };

    const createPlayer = async () => {
      try {
        await axios.post('/api/players', newPlayer.value);
        showCreateModal.value = false;
        newPlayer.value = { name: '', nickname: '' };
        await fetchPlayers();
      } catch (error) {
        console.error('Failed to create player:', error);
      }
    };

    const editPlayer = (player) => {
      editingPlayer.value = { ...player };
      showEditModal.value = true;
    };

    const updatePlayer = async () => {
      try {
        await axios.put(`/api/players/${editingPlayer.value.id}`, {
          name: editingPlayer.value.name,
          nickname: editingPlayer.value.nickname,
        });
        showEditModal.value = false;
        await fetchPlayers();
      } catch (error) {
        console.error('Failed to update player:', error);
      }
    };

    const confirmDelete = (player) => {
      deletingPlayer.value = { ...player };
      showDeleteModal.value = true;
    };

    const deletePlayer = async () => {
      try {
        await axios.delete(`/api/players/${deletingPlayer.value.id}`);
        showDeleteModal.value = false;
        await fetchPlayers();
      } catch (error) {
        console.error('Failed to delete player:', error);
      }
    };

    const getScoreClass = (score) => {
      return {
        'text-red-600': score < 0,
        'text-green-600': score > 0,
        'font-semibold': true
      };
    };

    onMounted(fetchPlayers);

    return {
      players,
      showCreateModal,
      showEditModal,
      showDeleteModal,
      newPlayer,
      editingPlayer,
      deletingPlayer,
      createPlayer,
      editPlayer,
      updatePlayer,
      confirmDelete,
      deletePlayer,
      getScoreClass
    };
  }
};
</script> 