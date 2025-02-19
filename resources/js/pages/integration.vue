<template>
  <div class="container">
    <div class="header">
      <h1>Integration</h1>
      <button @click="toggleDropdown">+ Add New</button>
      <!-- Dropdown for selecting integration options -->
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

    <!-- Cards Container -->
    <div class="cards-container">
      <div class="card" v-for="card in cards" :key="card.id">
        <h2>{{ card.title }}</h2>
        <p>{{ card.description }}</p>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'IntegrationPage',
  data() {
    return {
      showDropdown: false, // Controls the visibility of the dropdown
      cards: [
        { id: 1, title: 'Facebook Integration', description: 'Lead Ads Integration for Facebook.' },
        { id: 2, title: 'Google Integration', description: 'Lead Ads Integration for Google Ads.' },
        { id: 3, title: 'Twitter Integration', description: 'Lead Ads Integration for Twitter Ads.' }
      ]
    };
  },
  methods: {
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },
    handleIntegration(platform: string) {
      this.showDropdown = false; // Hide the dropdown after selection
      switch (platform) {
        case 'Facebook Ads':
          this.openFacebookPopup();
          break;
        case 'Google Ads':
          this.openGoogleAdsPopup();
          break;
        case 'Twitter Ads':
          this.openTwitterAdsPopup();
          break;
        case 'TikTok Ads':
          this.openTikTokAdsPopup();
          break;
        case 'Snapchat Ads':
          this.openSnapchatAdsPopup();
          break;
        case 'Instagram Ads':
          this.openInstagramAdsPopup();
          break;
        default:
          alert(`Integration for ${platform} is under development.`);
      }
    },
    openFacebookPopup() {
      const facebookOAuthUrl = `https://www.facebook.com/v18.0/dialog/oauth?client_id=983501733459311&redirect_uri=https://jsithub.com/test&response_type=token&scope=public_profile,email&display=popup`;
      window.open(facebookOAuthUrl, 'Facebook Login', 'width=600,height=700');
    },
    openGoogleAdsPopup() {
      const googleOAuthUrl = `https://accounts.google.com/o/oauth2/auth?client_id=932644499351-u8hu1a2885v7277npsisd0u31mopjulc.apps.googleusercontent.com&redirect_uri=http://127.0.0.1:8000/&response_type=token&scope=https://www.googleapis.com/auth/adwords&state=google_ads`;
      window.open(googleOAuthUrl, 'Google Ads Login', 'width=600,height=700');
    },
    openTwitterAdsPopup() {
      const twitterOAuthUrl = `https://api.twitter.com/oauth/authorize?client_id=YOUR_TWITTER_CLIENT_ID&redirect_uri=https://your-redirect-url.com&response_type=token&scope=ads.read,ads.write&state=twitter_ads`;
      window.open(twitterOAuthUrl, 'Twitter Ads Login', 'width=600,height=700');
    },
    openTikTokAdsPopup() {
      const tiktokOAuthUrl = `https://ads.tiktok.com/open/oauth?app_id=YOUR_TIKTOK_APP_ID&redirect_uri=https://your-redirect-url.com&state=tiktok_ads&scope=ad.manage`;
      window.open(tiktokOAuthUrl, 'TikTok Ads Login', 'width=600,height=700');
    },
    openSnapchatAdsPopup() {
      const snapchatOAuthUrl = `https://accounts.snapchat.com/accounts/oauth2/auth?client_id=YOUR_SNAPCHAT_CLIENT_ID&redirect_uri=https://your-redirect-url.com&response_type=token&scope=snapchat_ads&state=snapchat_ads`;
      window.open(snapchatOAuthUrl, 'Snapchat Ads Login', 'width=600,height=700');
    },
    openInstagramAdsPopup() {
      const instagramOAuthUrl = `https://api.instagram.com/oauth/authorize?client_id=YOUR_INSTAGRAM_CLIENT_ID&redirect_uri=https://your-redirect-url.com&response_type=token&scope=ads_management&state=instagram_ads`;
      window.open(instagramOAuthUrl, 'Instagram Ads Login', 'width=600,height=700');
    }
  }
}
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  padding: 10px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  position: relative; /* Ensure the dropdown is positioned correctly */
}

button {
  padding: 4px 16px;
  background-color: hsla(11, 100%, 42.2%, 1);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  width: max-content;
}

button:hover {
  background-color: #b31500;
}

.dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: white;
  border: 1px solid #ddd;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  z-index: 10;
}

.dropdown ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.dropdown li {
  padding: 10px 20px;
  cursor: pointer;
}

.dropdown li:hover {
  background-color: #f0f0f0;
}

.cards-container {
  display: flex;
  flex-direction: column;
  gap: 20px; /* Space between cards */
}

.card {
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  background-color: #fff;
}

.card h2 {
  margin-top: 0;
}

.card p {
  margin-bottom: 0;
}
</style>
