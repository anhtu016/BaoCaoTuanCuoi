<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const router = useRouter();

const login = async () => {
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      email: email.value,
      password: password.value,
    });

    localStorage.setItem('token', response.data.token);
    router.push('/dashboard');
  } catch (error) {
    alert('Đăng nhập thất bại');
  }
};
</script>

<template>
  <div class="flex justify-center items-center h-screen bg-gray-100">
    <div class="">
      <h2 class="text-2xl font-bold mb-4 text-center">Trang Đăng nhập</h2>
      <form @submit.prevent="login" class="space-y-4 text-center">
        <div class="mb-4">
          <label for="email" class="block font-medium">Tài Khoản :</label>
          <input 
            v-model="email" 
            type="email" 
            id="email"
            placeholder="Email" 
            class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            required 
          />
        </div>
        <div class="mb-4">
          <label for="password" class="block font-medium">Mật Khẩu :</label>
          <input 
            v-model="password" 
            type="password" 
            id="password"
            placeholder="Mật khẩu" 
            class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            required 
          />
        </div>
        <button 
          type="submit" 
          class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600 transition btn btn-success">
          Đăng nhập
        </button>
      </form>
      <p class="mt-4 text-center">
        Chưa có tài khoản? 
        <router-link to="/register" class="text-blue-500 hover:underline">
          Đăng ký ngay
        </router-link>
      </p>
    </div>
  </div>
</template>
