<div id="cookie-consent" style="position:fixed;bottom:0;width:100%;background:rgba(0,0,0,0.9);color:#fff;padding:20px;text-align:center;z-index:9999;display:none;">
    We use cookies to save your cart, reservations and preferences.
    <button onclick="accept()" style="background:#00ff88;color:#000;border:none;padding:10px 30px;margin-left:15px;border-radius:25px;font-weight:bold;">Accept All Cookies</button>
</div>

<script>
if (!localStorage.getItem('cookies_accepted')) {
    document.getElementById('cookie-consent').style.display = 'block';
}
function accept() {
    localStorage.setItem('cookies_accepted', 'true');
    document.getElementById('cookie-consent').style.display = 'none';
}
</script>