<!-- resources/views/facebook-auth.blade.php -->
<script>
    // Pass the access token back to the Vue.js app
    window.opener.postMessage({
        accessToken: "{{ $accessToken }}"
    }, "*");

    // Close the popup automatically
    window.close();
</script>
