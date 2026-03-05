<template>
  <section class="login-view">
    <div class="login-grid">
      <article class="login-intro">
        <p class="login-kicker">Welcome Back</p>
        <h1>Sign in to DOOMSHOP</h1>
        <p>
          Track your cart, manage your profile and continue building your next PC setup.
        </p>
        <ul class="intro-list">
          <li><i class="bi bi-shield-lock"></i> Secure login with role-based access</li>
          <li><i class="bi bi-cart3"></i> Fast access to your shopping cart</li>
          <li><i class="bi bi-chat-square-text"></i> Comment and manage your activity</li>
        </ul>
      </article>

      <UserLogin @logIn="loginHandler" />
    </div>
  </section>
</template>

<script>
import { mapActions } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import UserLogin from "@/components/User/UserLogin.vue";

export default {
  name: "LoginView",
  components: {
    UserLogin,
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["login"]),
    async loginHandler(user) {
      try {
        await this.login(user);
        this.$router.push("/");
      } catch (error) {
        console.log("Bejelentkezesi hiba!");
      }
    },
  },
};
</script>

<style scoped>
.login-view {
  padding: 8px 0 6px;
}

.login-grid {
  display: grid;
  grid-template-columns: 1.1fr minmax(320px, 440px);
  gap: 22px;
  align-items: stretch;
}

.login-intro {
  border: 1px solid #d7e5f8;
  border-radius: 16px;
  background:
    radial-gradient(circle at 88% 12%, rgba(37, 99, 235, 0.2) 0, rgba(37, 99, 235, 0) 38%),
    linear-gradient(155deg, #ffffff 0%, #f5f9ff 56%, #edf4ff 100%);
  box-shadow: 0 14px 28px rgba(37, 99, 235, 0.1);
  padding: 20px;
}

.login-kicker {
  margin: 0 0 8px;
  font-size: 0.75rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #1d4ed8;
  font-weight: 800;
}

.login-intro h1 {
  margin: 0;
  color: #0f172a;
  font-size: clamp(1.35rem, 2.5vw, 1.95rem);
  line-height: 1.1;
}

.login-intro p {
  margin: 10px 0 0;
  color: #475569;
  max-width: 46ch;
}

.intro-list {
  list-style: none;
  margin: 14px 0 0;
  padding: 0;
  display: grid;
  gap: 8px;
}

.intro-list li {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #0f172a;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.72);
  border: 1px solid #dbe9fc;
  border-radius: 10px;
  padding: 8px 10px;
}

.intro-list i {
  color: #2563eb;
}

@media (max-width: 960px) {
  .login-grid {
    grid-template-columns: 1fr;
  }
}
</style>
