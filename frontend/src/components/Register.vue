<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useUserStore } from '../stores/userStore';

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const router = useRouter();
const userStore = useUserStore();

const register = async () => {
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });

    localStorage.setItem('token', response.data.token);
    await userStore.fetchUser(); // Lấy thông tin user ngay sau khi đăng ký
    router.push('/dashboard');
  } catch (error) {
    alert('Đăng ký thất bại: ' + (error.response?.data?.message || 'Lỗi không xác định'));
  }
};
</script>

<template>

<div class="container">
    <div class="text-center">
    <h2>Trang Đăng ký</h2>
    <div class="mb-3">
    <input v-model="name" placeholder="Họ và tên" class="form-control">
    </div>
    <div class="mb-3">
    <input v-model="email" placeholder="Email" class="form-control">
    </div>
    <div class="mb-3">
    <input v-model="password" type="password" placeholder="Mật khẩu" class="form-control">
    </div>
    <div class="mb-3">
    <input v-model="passwordConfirmation" type="password" placeholder="Nhập lại mật khẩu" class="form-control">
    </div>
    <button @click="register" class="btn btn-success">Đăng ký</button>
  </div>
</div>

</template>
