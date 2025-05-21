<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-8">
      <router-link :to="{ name: 'games' }" class="text-blue-600 hover:text-blue-800">
        ← 対局一覧に戻る
      </router-link>
    </div>

    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold mb-8">新規対局を記録</h1>

      <form @submit.prevent="createGame" class="space-y-8">
        <!-- 基本情報 -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">基本情報</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                対局日時
              </label>
              <input type="datetime-local" v-model="gameData.played_at" required
                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                場所
              </label>
              <input type="text" v-model="gameData.location"
                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                placeholder="任意">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                ゲームタイプ
              </label>
              <select v-model="gameData.game_type" required
                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="east_south">東南戦</option>
                <option value="east_only">東風戦</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                供託点
              </label>
              <input type="number" v-model.number="gameData.starting_points" required
                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                :value="25000">
            </div>
          </div>
        </div>

        <!-- ウマ設定 -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">ウマ設定</h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="(position, index) in ['1st', '2nd', '3rd', '4th']" :key="position">
              <label :for="'uma_' + position" class="block text-sm font-medium text-gray-700 mb-2">
                {{ position }}着
              </label>
              <input type="number" :id="'uma_' + position"
                v-model.number="gameData['uma_' + position.toLowerCase()]" required
                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                :value="[30, 10, -10, -30][index]">
            </div>
          </div>
        </div>

        <!-- 対局結果 -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">対局結果</h2>
          <div class="space-y-6">
            <div v-for="(result, index) in gameData.results" :key="index"
              class="p-4 border rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    プレイヤー
                  </label>
                  <select v-model="result.player_id" required
                    class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">選択してください</option>
                    <option v-for="player in availablePlayers(index)" :key="player.id" :value="player.id">
                      {{ player.name }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    座席
                  </label>
                  <select v-model="result.seat" required
                    class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">選択してください</option>
                    <option v-for="(position, seatIndex) in ['東家', '南家', '西家', '北家']"
                      :key="seatIndex"
                      :value="seatIndex"
                      :disabled="isPositionTaken(seatIndex, index)">
                      {{ position }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    得点
                  </label>
                  <input type="number" v-model.number="result.points" required
                    class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- メモ -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">メモ</h2>
          <textarea v-model="gameData.note" rows="3"
            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            placeholder="任意"></textarea>
        </div>

        <!-- 送信ボタン -->
        <div class="flex justify-center">
          <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg">
            対局を記録する
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'GameCreate',
  setup() {
    const router = useRouter();
    const players = ref([]);
    const gameData = ref({
      played_at: new Date().toISOString().slice(0, 16),
      location: '',
      game_type: 'east_south',
      starting_points: 25000,
      uma_1st: 30,
      uma_2nd: 10,
      uma_3rd: -10,
      uma_4th: -30,
      note: '',
      results: Array(4).fill().map(() => ({
        player_id: '',
        seat: '',
        points: 25000
      }))
    });

    const fetchPlayers = async () => {
      try {
        const response = await axios.get('/api/players');
        players.value = response.data;
      } catch (error) {
        console.error('Failed to fetch players:', error);
      }
    };

    const availablePlayers = (currentIndex) => {
      const selectedPlayerIds = gameData.value.results
        .filter((_, index) => index !== currentIndex)
        .map(result => result.player_id);
      return players.value.filter(player => !selectedPlayerIds.includes(player.id));
    };

    const isPositionTaken = (position, currentIndex) => {
      return gameData.value.results
        .some((result, index) => index !== currentIndex && result.seat === position);
    };

    const createGame = async () => {
      try {
        await axios.post('/api/games', gameData.value);
        router.push({ name: 'games' });
      } catch (error) {
        console.error('Failed to create game:', error);
        // TODO: エラーハンドリングの実装
      }
    };

    onMounted(fetchPlayers);

    return {
      gameData,
      players,
      availablePlayers,
      isPositionTaken,
      createGame
    };
  }
};
</script> 