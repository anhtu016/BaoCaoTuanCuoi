<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const myTasks = ref([]);
const errorMessage = ref('');
const loading = ref(true);

// Lấy token
const token = localStorage.getItem('token');
if (!token) {
  alert('Bạn chưa đăng nhập! Vui lòng đăng nhập lại.');
  router.push('/Mytask');
}

// Lấy danh sách công việc của mình
const fetchMyTasks = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/my-tasks', {
      headers: { Authorization: `Bearer ${token}` }
    });

    myTasks.value = response.data;
  } catch (error) {
    console.error('Lỗi khi tải công việc:', error);
    errorMessage.value = 'Không thể tải danh sách công việc.';
  } finally {
    loading.value = false;
  }
};

onMounted(fetchMyTasks);
</script>

<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <h1 class="text-center text-lg font-bold">Danh sách công việc của tôi</h1>

    <div v-if="errorMessage" class="text-red-500 mb-4 text-center">{{ errorMessage }}</div>

    <div v-if="loading" class="text-center">Đang tải công việc...</div>

    <div v-else-if="myTasks.length === 0" class="text-center">Bạn chưa có công việc nào.</div>

    <div v-else class="container mx-auto mt-4">
      <table class="">
        <thead class="">
          <tr>
            <th class="px-4 py-2">Tên công việc</th>
            <th class="px-4 py-2">Trạng thái</th>
            <th class="px-4 py-2">Ngày hết hạn</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="task in myTasks" :key="task.id" class="border-b">
            <td class="px-4 py-2">{{ task.name }}</td>
            <td class="px-4 py-2">
              <span :class="{
                'text-yellow-500': task.status === 'pending',
                'text-blue-500': task.status === 'in_progress',
                'text-green-500': task.status === 'completed'
              }">
                {{ task.status === 'pending' ? 'Chưa bắt đầu' : task.status === 'in_progress' ? 'Đang thực hiện' : 'Đã hoàn thành' }}
              </span>
            </td>
            <td class="px-4 py-2">{{ task.due_date }}</td>
          </tr>
        </tbody>
        <a href="./Dashboard"><button  class="btn btn-success border m-4">Quay lại</button></a>
      </table>
    </div>
  </div>
  
</template>
