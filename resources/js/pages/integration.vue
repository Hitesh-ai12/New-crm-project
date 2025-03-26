<template>
  <div class="container">
    <div class="header">
      <h1>Integration</h1>
      <button @click="toggleDropdown">+ Add New</button>
      <div v-if="showDropdown" class="dropdown">
        <ul>
          <li @click="handleIntegration('Facebook Ads')">Facebook Ads</li>
          <li @click="handleIntegration('Google Ads')">Google Ads</li>
        </ul>
      </div>
    </div>

    <div v-if="connectedAccounts.length" class="connected-accounts">
      <h2>Connected Accounts</h2>
      <ul>
        <li v-for="(account, index) in connectedAccounts" :key="index">
          {{ account.platform }} - {{ account.token.substring(0, 15) }}...
          <button @click="removeAccount(index)">Remove</button>
        </li>
      </ul>
    </div>

    <div class="cards-container">
      <div class="card" v-for="card in cards" :key="card.id">
        <h2>{{ card.title }}</h2>
        <p>{{ card.description }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'IntegrationPage',
  data() {
    return {
      showDropdown: false,
      connectedAccounts: [], 
      cards: [
        { id: 1, title: 'Facebook Integration', description: 'Lead Ads Integration for Facebook.' },
        { id: 2, title: 'Google Integration', description: 'Lead Ads Integration for Google Ads.' }
      ]
    };
  },
  mounted() {
    this.loadConnectedAccounts(); 
  },
  methods: {
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },
    handleIntegration(platform) {
      this.showDropdown = false;
      const oauthUrls = {
        'Facebook Ads': 'https://www.facebook.com/v18.0/dialog/oauth?client_id=983501733459311&redirect_uri=https://jsithub.com/test&response_type=token&scope=public_profile,email&display=popup',
        'Google Ads': 'https://accounts.google.com/o/oauth2/auth?client_id=932644499351-u8hu1a2885v7277npsisd0u31mopjulc.apps.googleusercontent.com&redirect_uri=http://127.0.0.1:8000/&response_type=token&scope=https://www.googleapis.com/auth/adwords&state=google_ads',
      };

      if (oauthUrls[platform]) {
        this.openOAuthPopup(platform, oauthUrls[platform]);
      } else {
        alert(`Integration for ${platform} is under development.`);
      }
    },
    openOAuthPopup(platform, url) {
      const oauthWindow = window.open(url, `${platform} Login`, 'width=600,height=700');
      const interval = setInterval(() => {
        try {
          if (oauthWindow && oauthWindow.location.href.includes('access_token')) {
            const accessToken = this.extractAccessToken(oauthWindow.location.href);

            if (accessToken) {
              this.connectedAccounts.push({ platform, token: accessToken });
              this.saveConnectedAccounts();
              oauthWindow.close();
              clearInterval(interval);
            }
          }
        } catch (e) {}
      }, 1000);
    },
    extractAccessToken(url) {
      const match = url.match(/access_token=([^&]+)/);
      return match ? match[1] : null;
    },
    saveConnectedAccounts() {
      localStorage.setItem('connectedAccounts', JSON.stringify(this.connectedAccounts));
    },
    loadConnectedAccounts() {
      const storedAccounts = localStorage.getItem('connectedAccounts');
      if (storedAccounts) {
        this.connectedAccounts = JSON.parse(storedAccounts);
      }
    },
    removeAccount(index) {
      this.connectedAccounts.splice(index, 1);
      this.saveConnectedAccounts();
    }
  }
};
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  padding: 10px;
}

.header {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-block-end: 20px;
}

button {
  border: none;
  border-radius: 4px;
  background-color: hsla(11deg, 100%, 42.2%, 100%);
  color: white;
  cursor: pointer;
  padding-block: 4px;
  padding-inline: 16px;
}

button:hover {
  background-color: #b31500;
}

.dropdown {
  position: absolute;
  z-index: 10;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 10%);
  inset-block-start: 100%;
  inset-inline-end: 0;
}

.dropdown ul {
  padding: 0;
  margin: 0;
  list-style: none;
}

.dropdown li {
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 20px;
}

.dropdown li:hover {
  background-color: #f0f0f0;
}

.cards-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.card {
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 10%);
}

.card h2 {
  margin-block-start: 0;
}

.card p {
  margin-block-end: 0;
}

.connected-accounts {
  margin-block-end: 20px;
}

.connected-accounts ul {
  padding: 0;
  list-style: none;
}

/* .connected-accounts li {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-radius: 5px;
  background: #f8f8f8;
  margin-block-end: 5px;
} */

.connected-accounts button {
  border: none;
  border-radius: 3px;
  background: #d9534f;
  color: white;
  cursor: pointer;
  padding-block: 5px;
  padding-inline: 10px;
}

.connected-accounts button:hover {
  background: #c9302c;
}
</style>
