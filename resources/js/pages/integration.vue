<template>
  <div class="container">
    <div class="header">
      <h1>Integration</h1>
      <button @click="toggleDropdown">+ Add New</button>
      <div v-if="showDropdown" class="dropdown">
        <ul>
          <li @click="handleIntegration('Facebook Ads')">Facebook Ads</li>
          <li @click="handleIntegration('Google Ads')">Google Ads</li>
          <li @click="handleIntegration('Twitter Ads')">Twitter Ads</li>
          <li @click="handleIntegration('TikTok Ads')">TikTok Ads</li>
          <li @click="handleIntegration('Snapchat Ads')">Snapchat Ads</li>
          <li @click="handleIntegration('Instagram Ads')">Instagram Ads</li>
        </ul>
      </div>
    </div>


    <div class="cards-container">
      <div class="card" v-for="card in cards" :key="card.id">
        <h2>{{ card.title }}</h2>
        <p>{{ card.description }}</p>
      </div>
    </div>
     
      <div v-if="accessToken">
        <p>Access Token: {{ accessToken }}</p>
      </div>
  </div>
</template>

<script>
export default {
  name: 'IntegrationPage',
  data() {
    return {
      showDropdown: false,
      accessToken: null, 
      cards: [
        { id: 1, title: 'Facebook Integration', description: 'Lead Ads Integration for Facebook.' },
        { id: 2, title: 'Google Integration', description: 'Lead Ads Integration for Google Ads.' },
        { id: 3, title: 'Twitter Integration', description: 'Lead Ads Integration for Twitter Ads.' }
      ]
    };
  },
  mounted() {
    this.checkForAccessToken();
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

        'Twitter Ads': 'https://api.twitter.com/oauth/authorize?client_id=YOUR_TWITTER_CLIENT_ID&redirect_uri=https://your-redirect-url.com&response_type=token&scope=ads.read,ads.write&state=twitter_ads',
        'TikTok Ads': 'https://ads.tiktok.com/open/oauth?app_id=YOUR_TIKTOK_APP_ID&redirect_uri=https://your-redirect-url.com&state=tiktok_ads&scope=ad.manage',
        'Snapchat Ads': 'https://accounts.snapchat.com/accounts/oauth2/auth?client_id=YOUR_SNAPCHAT_CLIENT_ID&redirect_uri=https://your-redirect-url.com&response_type=token&scope=snapchat_ads&state=snapchat_ads',
        'Instagram Ads': 'https://api.instagram.com/oauth/authorize?client_id=YOUR_INSTAGRAM_CLIENT_ID&redirect_uri=https://your-redirect-url.com&response_type=token&scope=ads_management&state=instagram_ads'
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
            const url = oauthWindow.location.href;
            const accessToken = this.extractAccessToken(url);

            if (accessToken) {
              this.accessToken = accessToken; 
              console.log('Access Token:', this.accessToken);  
              oauthWindow.close();  
              clearInterval(interval);  
            }
          }
        } catch (e) {
       
        }
      }, 1000);
    },
    extractAccessToken(url) {
      const regex = /access_token=([^&]+)/;
      const match = url.match(regex);
      return match ? match[1] : null;
    },
    checkForAccessToken() {
      const urlHash = window.location.hash;
      const regex = /access_token=([^&]+)/;
      const match = urlHash.match(regex);

      if (match && match[1]) {
        this.accessToken = match[1]; 
        console.log('Access Token:', this.accessToken);  
      } else {
        console.log('No access token found in the URL fragment.');
      }
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
  inline-size: max-content;
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
</style>
