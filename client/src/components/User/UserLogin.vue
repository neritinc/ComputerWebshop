<template>
  <div class="login-shell">
    <div class="login-card">
      <div class="login-head">
        <p class="title-kicker">Account</p>
        <h2>Login</h2>
        <p class="title-sub">Use your email and password to continue.</p>
      </div>

      <form
        @submit.prevent="handleSubmit"
        :class="{ 'was-validated': validated }"
        novalidate
      >
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            id="email"
            v-model="user.email"
            required
            autocomplete="email"
            placeholder="you@example.com"
          />
          <div class="invalid-feedback">Adj meg egy ervenyes email cimet.</div>
        </div>

        <PasswordField
          class="mt-1"
          v-model="user.password"
          :label="'Password'"
          :label-id="'password'"
        />

        <div class="actions-row">
          <button type="submit" class="btn btn-success px-4">Login</button>
          <RouterLink to="/registration" class="btn btn-primary px-4">Registration</RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import PasswordField from "./PasswordField.vue";

class User {
  constructor(email = "", password = "") {
    this.email = email;
    this.password = password;
  }
}

export default {
  name: "UserLogin",
  components: {
    PasswordField,
  },
  data() {
    return {
      validated: false,
      user: new User(),
    };
  },
  methods: {
    handleSubmit(event) {
      const form = event.target;
      this.validated = true;

      if (form.checkValidity() === false) {
        return;
      }

      this.$emit("logIn", this.user);
    },
  },
};
</script>

<style scoped>
.login-shell {
  display: flex;
  justify-content: center;
}

.login-card {
  width: 100%;
  border: 1px solid #d7e5f8;
  border-radius: 16px;
  background: linear-gradient(175deg, #ffffff 0%, #f7fbff 100%);
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
  overflow: hidden;
}

.login-head {
  padding: 16px 16px 12px;
  border-bottom: 1px solid #e3edfb;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #fff;
}

.title-kicker {
  margin: 0;
  font-size: 0.72rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  opacity: 0.95;
  font-weight: 700;
}

.login-head h2 {
  margin: 4px 0 0;
  font-size: 1.35rem;
  font-weight: 800;
}

.title-sub {
  margin: 6px 0 0;
  opacity: 0.9;
  font-size: 0.9rem;
}

form {
  padding: 14px 16px 16px;
}

.form-label {
  font-weight: 700;
  color: #0f172a;
}

.form-control {
  border-color: #c7d9f2;
  background: #f8fbff;
}

.form-control:focus {
  border-color: #60a5fa;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.2);
  background: #fff;
}

.actions-row {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 2px;
}

@media (max-width: 480px) {
  .actions-row .btn {
    width: 100%;
  }
}
</style>
