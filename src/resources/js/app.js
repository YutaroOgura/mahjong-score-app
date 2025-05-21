import './bootstrap';
import { createApp, nextTick } from 'vue';
import App from './App.vue';
import router from './router';
import '../css/app.css';

// AOS (Animate On Scroll)
import AOS from 'aos';
import 'aos/dist/aos.css';

const app = createApp(App);
app.use(router);
app.mount('#app');

// Initialize AOS after mount
AOS.init({
  duration: 600,
  easing: 'ease-out-cubic',
  once: true,
});

// Refresh AOS on every route navigation
router.afterEach(() => {
  nextTick(() => {
    AOS.refresh();
  });
});
